<?php

namespace App\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\File;
use Throwable;

class ExternalReleaseDocumentDownloader
{
    protected const MAX_BYTES = 120 * 1024 * 1024;

    /**
     * Mengambil dokumen asli dari link eksternal untuk dimasukkan ke berkas publikasi.
     * Jika link tidak public atau hanya membuka halaman preview/login, method ini
     * akan mengembalikan status gagal agar controller membuat file TXT fallback.
     */
    public function download(string $url, string $temporaryDirectory, string $fallbackName, ?string $documentType = null): array
    {
        $url = trim($url);

        if (!$this->isValidDownloadableUrl($url)) {
            return $this->failed('Link tidak valid. Hanya link http dan https yang dapat diambil otomatis.');
        }

        File::ensureDirectoryExists($temporaryDirectory);
        $downloadUrl = $this->normalizeDownloadUrl($url, $documentType);
        $temporaryPath = tempnam($temporaryDirectory, 'external_release_');

        if ($temporaryPath === false) {
            return $this->failed('Folder sementara tidak dapat digunakan untuk mengambil dokumen eksternal.');
        }

        try {
            $cookieJar = new CookieJar();
            $client = new Client([
                'timeout' => 55,
                'connect_timeout' => 12,
                'allow_redirects' => [
                    'max' => 6,
                    'strict' => true,
                    'referer' => true,
                    'track_redirects' => true,
                ],
                'cookies' => $cookieJar,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (compatible; Manajemen Publikasi Statistik)',
                    'Accept' => 'application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/octet-stream,image/*,*/*;q=0.8',
                ],
                'http_errors' => false,
            ]);

            $response = $client->request('GET', $downloadUrl, [
                'sink' => $temporaryPath,
                'on_headers' => function ($response) {
                    $contentLength = (int) ($response->getHeaderLine('Content-Length') ?: 0);
                    if ($contentLength > self::MAX_BYTES) {
                        throw new \RuntimeException('Ukuran file dari link melebihi batas 120 MB.');
                    }
                },
            ]);

            if ($this->needsGoogleDriveConfirmation($downloadUrl, $response->getHeaderLine('Content-Type'), $temporaryPath)) {
                $confirmUrl = $this->extractGoogleDriveConfirmUrl((string) file_get_contents($temporaryPath), $downloadUrl);

                if ($confirmUrl) {
                    @unlink($temporaryPath);
                    $temporaryPath = tempnam($temporaryDirectory, 'external_release_');

                    if ($temporaryPath === false) {
                        return $this->failed('Folder sementara tidak dapat digunakan untuk mengambil dokumen eksternal.');
                    }

                    $response = $client->request('GET', $confirmUrl, [
                        'sink' => $temporaryPath,
                        'on_headers' => function ($response) {
                            $contentLength = (int) ($response->getHeaderLine('Content-Length') ?: 0);
                            if ($contentLength > self::MAX_BYTES) {
                                throw new \RuntimeException('Ukuran file dari link melebihi batas 120 MB.');
                            }
                        },
                    ]);
                }
            }

            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                @unlink($temporaryPath);
                return $this->failed('Link mengembalikan status HTTP ' . $statusCode . '.');
            }

            if (!is_file($temporaryPath) || filesize($temporaryPath) === 0) {
                @unlink($temporaryPath);
                return $this->failed('File dari link kosong atau tidak berhasil diunduh.');
            }

            $contentType = strtolower(trim(explode(';', $response->getHeaderLine('Content-Type'))[0] ?? ''));
            if ($this->looksLikePreviewOrLoginPage($contentType, $temporaryPath)) {
                @unlink($temporaryPath);
                return $this->failed('Link masih mengarah ke halaman preview/login, bukan file unduhan langsung. Pastikan akses link dibuat public.');
            }

            $fileName = $this->detectFileName($response->getHeaderLine('Content-Disposition'), $downloadUrl, $fallbackName, $contentType, $documentType);

            return [
                'success' => true,
                'path' => $temporaryPath,
                'file_name' => $fileName,
                'content_type' => $contentType ?: 'application/octet-stream',
                'message' => 'Dokumen eksternal berhasil diunduh.',
            ];
        } catch (Throwable $exception) {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }

            return $this->failed($exception->getMessage() ?: 'Dokumen eksternal gagal diunduh.');
        }
    }

    /**
     * Mengambil isi paket infografis dari satu link eksternal.
     * Link gambar langsung menghasilkan satu file. Link folder Google Drive public
     * dibaca melalui embedded folder view, lalu setiap JPG/PNG diunduh terpisah.
     * Link ZIP public juga didukung dan hanya isi JPG/PNG yang diambil.
     */
    public function downloadInfographicPackage(string $url, string $temporaryDirectory, string $fallbackName = 'Paket Infografis'): array
    {
        $url = trim($url);

        if (!$this->isValidDownloadableUrl($url)) {
            return $this->failedPackage('Link paket infografis tidak valid.');
        }

        File::ensureDirectoryExists($temporaryDirectory);

        $folderId = $this->googleDriveFolderId($url);
        if ($folderId) {
            return $this->downloadGoogleDriveFolderImages($folderId, $temporaryDirectory);
        }

        $downloaded = $this->download($url, $temporaryDirectory, $fallbackName, 'infografis');
        if (!$downloaded['success']) {
            return $this->failedPackage($downloaded['message']);
        }

        if ($this->isZipDownload($downloaded)) {
            $result = $this->extractImagesFromZip($downloaded['path'], $temporaryDirectory);
            @unlink($downloaded['path']);

            return $result;
        }

        if (!$this->isAllowedInfographicImage($downloaded['path'], $downloaded['file_name'], $downloaded['content_type'])) {
            @unlink($downloaded['path']);

            return $this->failedPackage('Link tidak berisi gambar JPG/PNG atau folder paket infografis yang didukung.');
        }

        return [
            'success' => true,
            'files' => [[
                'path' => $downloaded['path'],
                'file_name' => $this->sanitizeExternalName($downloaded['file_name']),
                'relative_path' => $this->sanitizeExternalName($downloaded['file_name']),
                'content_type' => $downloaded['content_type'],
            ]],
            'message' => 'Satu file infografis dari link berhasil diunduh.',
        ];
    }

    protected function downloadGoogleDriveFolderImages(string $folderId, string $temporaryDirectory): array
    {
        $client = new Client([
            'timeout' => 35,
            'connect_timeout' => 12,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; Manajemen Publikasi Statistik)',
                'Accept' => 'text/html,application/xhtml+xml,*/*;q=0.8',
            ],
            'http_errors' => false,
        ]);

        try {
            $visited = [];
            $entries = $this->collectGoogleDriveFolderEntries($client, $folderId, '', $visited, 0);
        } catch (Throwable $exception) {
            return $this->failedPackage($exception->getMessage() ?: 'Isi folder Google Drive tidak dapat dibaca.');
        }

        if (empty($entries)) {
            return $this->failedPackage('Folder Google Drive kosong atau belum dibagikan untuk siapa saja yang memiliki link.');
        }

        $downloadedFiles = [];

        foreach ($entries as $entry) {
            if (count($downloadedFiles) >= 100) {
                $this->cleanupPackageFiles($downloadedFiles);

                return $this->failedPackage('Isi folder melebihi batas 100 file infografis.');
            }

            $downloaded = $this->download(
                'https://drive.google.com/file/d/' . rawurlencode($entry['id']) . '/view',
                $temporaryDirectory,
                $entry['name'],
                'infografis'
            );

            if (!$downloaded['success']) {
                $this->cleanupPackageFiles($downloadedFiles);

                return $this->failedPackage('File "' . $entry['name'] . '" gagal diunduh dari folder: ' . $downloaded['message']);
            }

            if (!$this->isAllowedInfographicImage($downloaded['path'], $downloaded['file_name'], $downloaded['content_type'])) {
                @unlink($downloaded['path']);
                continue;
            }

            $fileName = $this->sanitizeExternalName($downloaded['file_name'] ?: $entry['name']);
            $relativeDirectory = trim((string) ($entry['relative_directory'] ?? ''), '/');
            $relativePath = $relativeDirectory !== ''
                ? $relativeDirectory . '/' . $fileName
                : $fileName;

            $downloadedFiles[] = [
                'path' => $downloaded['path'],
                'file_name' => $fileName,
                'relative_path' => $relativePath,
                'content_type' => $downloaded['content_type'],
            ];
        }

        if (empty($downloadedFiles)) {
            return $this->failedPackage('Folder Google Drive tidak berisi file JPG, JPEG, atau PNG yang dapat diunduh.');
        }

        return [
            'success' => true,
            'files' => $downloadedFiles,
            'message' => count($downloadedFiles) . ' file infografis dari folder Google Drive berhasil diunduh.',
        ];
    }

    protected function collectGoogleDriveFolderEntries(Client $client, string $folderId, string $relativeDirectory, array &$visited, int $depth): array
    {
        if ($depth > 5) {
            throw new \RuntimeException('Struktur folder Google Drive terlalu dalam. Maksimal 5 tingkat folder.');
        }

        if (isset($visited[$folderId])) {
            return [];
        }
        $visited[$folderId] = true;

        $response = $client->request('GET', 'https://drive.google.com/embeddedfolderview', [
            'query' => ['id' => $folderId],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Folder Google Drive mengembalikan status HTTP ' . $response->getStatusCode() . '.');
        }

        $html = (string) $response->getBody();
        $entries = [];

        preg_match_all('/<a\b[^>]*\bhref=(["\'])(.*?)\1[^>]*>(.*?)<\/a>/is', $html, $anchors, PREG_SET_ORDER);

        foreach ($anchors as $anchor) {
            $href = html_entity_decode((string) $anchor[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $name = trim(html_entity_decode(strip_tags((string) $anchor[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $name = $this->sanitizeExternalName($name !== '' ? $name : 'infografis');

            if (preg_match('#https://drive\.google\.com/file/d/([-\w]{20,})/view#i', $href, $matches)
                || preg_match('#https://docs\.google\.com/\w+/d/([-\w]{20,})/#i', $href, $matches)) {
                $entries[] = [
                    'id' => $matches[1],
                    'name' => $name,
                    'relative_directory' => $relativeDirectory,
                ];
                continue;
            }

            if (preg_match('#https://drive\.google\.com/drive/(?:u/\d+/)?folders/([-\w]{20,})#i', $href, $matches)) {
                $childDirectory = trim($relativeDirectory . '/' . $name, '/');
                $entries = array_merge(
                    $entries,
                    $this->collectGoogleDriveFolderEntries($client, $matches[1], $childDirectory, $visited, $depth + 1)
                );
            }
        }

        $unique = [];
        foreach ($entries as $entry) {
            $unique[$entry['id']] = $entry;
        }

        return array_values($unique);
    }

    protected function googleDriveFolderId(string $url): ?string
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        if (!str_contains($host, 'drive.google.com')) {
            return null;
        }

        if (preg_match('#/folders/([-\w]{20,})#', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function isZipDownload(array $downloaded): bool
    {
        $contentType = strtolower((string) ($downloaded['content_type'] ?? ''));
        $extension = strtolower(pathinfo((string) ($downloaded['file_name'] ?? ''), PATHINFO_EXTENSION));

        return in_array($extension, ['zip'], true)
            || str_contains($contentType, 'application/zip')
            || str_contains($contentType, 'application/x-zip-compressed');
    }

    protected function extractImagesFromZip(string $zipPath, string $temporaryDirectory): array
    {
        if (!class_exists(\ZipArchive::class)) {
            return $this->extractImagesFromZipWithPhar($zipPath, $temporaryDirectory);
        }

        $archive = new \ZipArchive();
        if ($archive->open($zipPath) !== true) {
            return $this->failedPackage('File ZIP paket infografis tidak dapat dibuka.');
        }

        $files = [];
        $totalBytes = 0;

        try {
            for ($index = 0; $index < $archive->numFiles; $index++) {
                $stat = $archive->statIndex($index);
                $entryName = (string) ($stat['name'] ?? '');

                if ($entryName === '' || str_ends_with($entryName, '/')) {
                    continue;
                }

                $normalized = str_replace('\\', '/', $entryName);
                $segments = array_values(array_filter(explode('/', $normalized), fn ($segment) => $segment !== '' && $segment !== '.' && $segment !== '..'));
                if (empty($segments)) {
                    continue;
                }

                $extension = strtolower(pathinfo(end($segments), PATHINFO_EXTENSION));
                if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
                    continue;
                }

                if (count($files) >= 100) {
                    $this->cleanupPackageFiles($files);
                    return $this->failedPackage('Isi ZIP melebihi batas 100 file infografis.');
                }

                $entrySize = (int) ($stat['size'] ?? 0);
                $totalBytes += $entrySize;
                if ($entrySize > 20 * 1024 * 1024 || $totalBytes > self::MAX_BYTES) {
                    $this->cleanupPackageFiles($files);
                    return $this->failedPackage('Ukuran isi ZIP paket infografis melebihi batas yang diizinkan.');
                }

                $stream = $archive->getStream($entryName);
                if ($stream === false) {
                    $this->cleanupPackageFiles($files);
                    return $this->failedPackage('Salah satu file di dalam ZIP tidak dapat dibaca.');
                }

                $temporaryPath = tempnam($temporaryDirectory, 'external_infographic_');
                if ($temporaryPath === false) {
                    fclose($stream);
                    $this->cleanupPackageFiles($files);
                    return $this->failedPackage('Folder sementara tidak dapat digunakan untuk mengekstrak ZIP.');
                }

                $output = fopen($temporaryPath, 'wb');
                if ($output === false) {
                    fclose($stream);
                    @unlink($temporaryPath);
                    $this->cleanupPackageFiles($files);
                    return $this->failedPackage('File sementara paket infografis tidak dapat dibuat.');
                }

                stream_copy_to_stream($stream, $output);
                fclose($stream);
                fclose($output);

                $safeSegments = array_map(fn ($segment) => $this->sanitizeExternalName($segment), $segments);
                $fileName = end($safeSegments);
                $relativePath = implode('/', $safeSegments);

                if (!$this->isAllowedInfographicImage($temporaryPath, $fileName, null)) {
                    @unlink($temporaryPath);
                    continue;
                }

                $files[] = [
                    'path' => $temporaryPath,
                    'file_name' => $fileName,
                    'relative_path' => $relativePath,
                    'content_type' => function_exists('mime_content_type') ? (mime_content_type($temporaryPath) ?: 'application/octet-stream') : 'application/octet-stream',
                ];
            }
        } finally {
            $archive->close();
        }

        if (empty($files)) {
            return $this->failedPackage('ZIP tidak berisi gambar JPG, JPEG, atau PNG.');
        }

        return [
            'success' => true,
            'files' => $files,
            'message' => count($files) . ' file infografis dari ZIP berhasil diekstrak.',
        ];
    }

    protected function extractImagesFromZipWithPhar(string $zipPath, string $temporaryDirectory): array
    {
        if (!class_exists(\PharData::class)) {
            return $this->failedPackage('Server tidak menyediakan pembaca ZIP untuk paket infografis.');
        }

        $files = [];
        $totalBytes = 0;
        $pharZipPath = $zipPath;
        $temporaryZipCopy = null;

        if (strtolower(pathinfo($zipPath, PATHINFO_EXTENSION)) !== 'zip') {
            $baseTemporaryPath = tempnam($temporaryDirectory, 'external_zip_');
            if ($baseTemporaryPath === false) {
                return $this->failedPackage('Folder sementara tidak dapat digunakan untuk membaca ZIP.');
            }

            @unlink($baseTemporaryPath);
            $temporaryZipCopy = $baseTemporaryPath . '.zip';
            if (!@copy($zipPath, $temporaryZipCopy)) {
                @unlink($temporaryZipCopy);
                return $this->failedPackage('File ZIP sementara tidak dapat disiapkan.');
            }
            $pharZipPath = $temporaryZipCopy;
        }

        try {
            $archive = new \PharData($pharZipPath);
            $iterator = new \RecursiveIteratorIterator($archive);

            foreach ($iterator as $entry) {
                if (!$entry->isFile()) {
                    continue;
                }

                $entryPath = str_replace('\\', '/', (string) $entry->getPathName());
                $prefix = 'phar://' . str_replace('\\', '/', $pharZipPath) . '/';
                $relative = str_starts_with($entryPath, $prefix)
                    ? substr($entryPath, strlen($prefix))
                    : $entry->getFilename();

                $segments = array_values(array_filter(explode('/', $relative), fn ($segment) => $segment !== '' && $segment !== '.' && $segment !== '..'));
                if (empty($segments)) {
                    continue;
                }

                $extension = strtolower(pathinfo(end($segments), PATHINFO_EXTENSION));
                if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
                    continue;
                }

                if (count($files) >= 100) {
                    $this->cleanupPackageFiles($files);
                    return $this->failedPackage('Isi ZIP melebihi batas 100 file infografis.');
                }

                $entrySize = (int) $entry->getSize();
                $totalBytes += $entrySize;
                if ($entrySize > 20 * 1024 * 1024 || $totalBytes > self::MAX_BYTES) {
                    $this->cleanupPackageFiles($files);
                    return $this->failedPackage('Ukuran isi ZIP paket infografis melebihi batas yang diizinkan.');
                }

                $input = fopen($entryPath, 'rb');
                $temporaryPath = tempnam($temporaryDirectory, 'external_infographic_');
                if ($input === false || $temporaryPath === false) {
                    if (is_resource($input)) {
                        fclose($input);
                    }
                    if ($temporaryPath) {
                        @unlink($temporaryPath);
                    }
                    $this->cleanupPackageFiles($files);
                    return $this->failedPackage('Salah satu file di dalam ZIP tidak dapat dibaca.');
                }

                $output = fopen($temporaryPath, 'wb');
                if ($output === false) {
                    fclose($input);
                    @unlink($temporaryPath);
                    $this->cleanupPackageFiles($files);
                    return $this->failedPackage('File sementara paket infografis tidak dapat dibuat.');
                }

                stream_copy_to_stream($input, $output);
                fclose($input);
                fclose($output);

                $safeSegments = array_map(fn ($segment) => $this->sanitizeExternalName($segment), $segments);
                $fileName = end($safeSegments);
                $relativePath = implode('/', $safeSegments);

                if (!$this->isAllowedInfographicImage($temporaryPath, $fileName, null)) {
                    @unlink($temporaryPath);
                    continue;
                }

                $contentType = function_exists('mime_content_type')
                    ? (mime_content_type($temporaryPath) ?: 'application/octet-stream')
                    : 'application/octet-stream';

                $files[] = [
                    'path' => $temporaryPath,
                    'file_name' => $fileName,
                    'relative_path' => $relativePath,
                    'content_type' => $contentType,
                ];
            }
        } catch (Throwable $exception) {
            $this->cleanupPackageFiles($files);
            if ($temporaryZipCopy) {
                @unlink($temporaryZipCopy);
            }
            return $this->failedPackage($exception->getMessage() ?: 'File ZIP paket infografis tidak dapat dibuka.');
        }

        if ($temporaryZipCopy) {
            @unlink($temporaryZipCopy);
        }

        if (empty($files)) {
            return $this->failedPackage('ZIP tidak berisi gambar JPG, JPEG, atau PNG.');
        }

        return [
            'success' => true,
            'files' => $files,
            'message' => count($files) . ' file infografis dari ZIP berhasil diekstrak.',
        ];
    }

    protected function isAllowedInfographicImage(string $path, ?string $fileName, ?string $contentType): bool
    {
        $extension = strtolower(pathinfo((string) $fileName, PATHINFO_EXTENSION));
        $mime = strtolower((string) $contentType);

        if (is_file($path) && function_exists('mime_content_type')) {
            $detectedMime = mime_content_type($path);
            if (is_string($detectedMime) && $detectedMime !== '') {
                $mime = strtolower($detectedMime);
            }
        }

        return in_array($extension, ['jpg', 'jpeg', 'png'], true)
            && in_array($mime, ['image/jpeg', 'image/png', 'application/octet-stream', ''], true);
    }

    protected function sanitizeExternalName(string $name): string
    {
        $name = trim(str_replace(['\\', '/'], '-', $name));
        $name = preg_replace('/[\x00-\x1F\x7F]+/', '', $name) ?: 'infografis';
        $name = preg_replace('/[<>:"|?*]+/', '-', $name) ?: 'infografis';
        $name = trim($name, " .-_\t\n\r\0\x0B");

        return mb_substr($name !== '' ? $name : 'infografis', 0, 180);
    }

    protected function cleanupPackageFiles(array $files): void
    {
        foreach ($files as $file) {
            $path = is_array($file) ? ($file['path'] ?? null) : null;
            if ($path && is_file($path)) {
                @unlink($path);
            }
        }
    }

    protected function failedPackage(string $message): array
    {
        return [
            'success' => false,
            'files' => [],
            'message' => $message,
        ];
    }

    protected function isValidDownloadableUrl(string $url): bool
    {
        $parts = parse_url($url);

        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }

        return in_array(strtolower($parts['scheme']), ['http', 'https'], true);
    }

    protected function normalizeDownloadUrl(string $url, ?string $documentType = null): string
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $query = [];
        parse_str($parts['query'] ?? '', $query);

        if (str_contains($host, 'docs.google.com')) {
            if (preg_match('#/document/d/([^/]+)#', $path, $matches)) {
                $format = $documentType === 'naskah_pdf' ? 'pdf' : 'docx';
                return 'https://docs.google.com/document/d/' . $matches[1] . '/export?format=' . $format;
            }

            if (preg_match('#/spreadsheets/d/([^/]+)#', $path, $matches)) {
                return 'https://docs.google.com/spreadsheets/d/' . $matches[1] . '/export?format=xlsx';
            }

            if (preg_match('#/presentation/d/([^/]+)#', $path, $matches)) {
                return 'https://docs.google.com/presentation/d/' . $matches[1] . '/export/pptx';
            }
        }

        if (str_contains($host, 'drive.google.com')) {
            $fileId = null;

            if (preg_match('#/file/d/([^/]+)#', $path, $matches)) {
                $fileId = $matches[1];
            } elseif (!empty($query['id'])) {
                $fileId = $query['id'];
            }

            if ($fileId) {
                return 'https://drive.google.com/uc?export=download&id=' . rawurlencode($fileId);
            }
        }

        if (str_contains($host, 'dropbox.com')) {
            $query['dl'] = '1';
            $scheme = $parts['scheme'] ?? 'https';
            return $scheme . '://' . $host . $path . '?' . http_build_query($query);
        }

        return $url;
    }

    protected function needsGoogleDriveConfirmation(string $url, string $contentType, string $temporaryPath): bool
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');
        $contentType = strtolower($contentType);

        if (!str_contains($host, 'drive.google.com')) {
            return false;
        }

        if (!str_contains($contentType, 'text/html')) {
            return false;
        }

        $html = is_file($temporaryPath) ? (string) file_get_contents($temporaryPath, false, null, 0, 1024 * 1024) : '';

        return str_contains($html, 'confirm=') || str_contains($html, 'download_warning') || str_contains($html, 'uc?export=download');
    }

    protected function extractGoogleDriveConfirmUrl(string $html, string $currentUrl): ?string
    {
        if (preg_match('/href="([^"]*uc\?export=download[^"]+)"/i', $html, $matches)) {
            $href = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (str_starts_with($href, '/')) {
                return 'https://drive.google.com' . $href;
            }
            if (str_starts_with($href, 'http')) {
                return $href;
            }
        }

        if (preg_match('/confirm=([0-9A-Za-z_\-]+)&amp;id=([0-9A-Za-z_\-]+)/', $html, $matches)) {
            return 'https://drive.google.com/uc?export=download&confirm=' . $matches[1] . '&id=' . $matches[2];
        }

        $id = null;
        parse_str(parse_url($currentUrl, PHP_URL_QUERY) ?: '', $query);
        if (!empty($query['id'])) {
            $id = $query['id'];
        }

        if ($id && preg_match('/confirm=([0-9A-Za-z_\-]+)/', $html, $matches)) {
            return 'https://drive.google.com/uc?export=download&confirm=' . $matches[1] . '&id=' . rawurlencode($id);
        }

        return null;
    }

    protected function looksLikePreviewOrLoginPage(string $contentType, string $temporaryPath): bool
    {
        if (!str_contains($contentType, 'text/html')) {
            return false;
        }

        $sample = strtolower((string) file_get_contents($temporaryPath, false, null, 0, 4096));

        return str_contains($sample, '<html')
            || str_contains($sample, '<!doctype html')
            || str_contains($sample, 'sign in')
            || str_contains($sample, 'login');
    }

    protected function detectFileName(string $contentDisposition, string $url, string $fallbackName, string $contentType, ?string $documentType = null): string
    {
        $fileName = $this->fileNameFromContentDisposition($contentDisposition)
            ?: $this->fileNameFromUrl($url)
            ?: $fallbackName
            ?: 'dokumen-eksternal';

        $fileName = trim(str_replace(['\\', '/'], '-', $fileName));
        $fileName = preg_replace('/[\x00-\x1F\x7F]+/', '', $fileName) ?: 'dokumen-eksternal';

        if (!pathinfo($fileName, PATHINFO_EXTENSION)) {
            $extension = $this->extensionFromContentType($contentType) ?: $this->extensionFromDocumentType($documentType);
            if ($extension) {
                $fileName .= '.' . $extension;
            }
        }

        return $fileName;
    }

    protected function fileNameFromContentDisposition(string $contentDisposition): ?string
    {
        if (!$contentDisposition) {
            return null;
        }

        if (preg_match('/filename\*=UTF-8\'\'([^;]+)/i', $contentDisposition, $matches)) {
            return rawurldecode(trim($matches[1], '" '));
        }

        if (preg_match('/filename="?([^";]+)"?/i', $contentDisposition, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    protected function fileNameFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $basename = basename($path);

        if ($basename && $basename !== '/' && !in_array($basename, ['view', 'edit', 'export', 'download', 'uc'], true)) {
            return rawurldecode($basename);
        }

        return null;
    }

    protected function extensionFromContentType(string $contentType): ?string
    {
        return match ($contentType) {
            'application/pdf' => 'pdf',
            'application/zip', 'application/x-zip-compressed' => 'zip',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/msword' => 'doc',
            'application/vnd.ms-excel' => 'xls',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
    }

    protected function extensionFromDocumentType(?string $documentType): ?string
    {
        return match ($documentType) {
            'naskah_pdf' => 'pdf',
            'naskah_zip' => 'zip',
            'infografis' => 'jpg',
            'daftar_tabel_gambar' => 'xlsx',
            'surat_persetujuan_rilis', 'surat_pernyataan_rilis' => 'pdf',
            default => null,
        };
    }

    protected function failed(string $message): array
    {
        return [
            'success' => false,
            'path' => null,
            'file_name' => null,
            'content_type' => null,
            'message' => $message,
        ];
    }
}
