<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PublicationDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'publication_team_id',
        'publication_id',
        'uploaded_by',
        'version',
        'source_type',
        'file_path',
        'external_url',
        'file_original_name',
        'mime_type',
        'notes',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (PublicationDraft $draft) {
            if (!Schema::hasTable('publication_review_slides')) {
                return;
            }

            $previousDraft = static::query()
                ->where('publication_team_id', $draft->publication_team_id)
                ->where('id', '<', $draft->id)
                ->orderByDesc('version')
                ->orderByDesc('id')
                ->first();

            if (!$previousDraft) {
                return;
            }

            $previousSlides = PublicationReviewSlide::query()
                ->where('publication_team_id', $draft->publication_team_id)
                ->where('publication_draft_id', $previousDraft->id)
                ->orderByDesc('saved_at')
                ->orderByDesc('id')
                ->get()
                ->unique(fn (PublicationReviewSlide $slide) => $slide->review_type . '|' . $slide->anatomy_section);

            foreach ($previousSlides as $slide) {
                PublicationReviewSlide::firstOrCreate(
                    [
                        'publication_draft_id' => $draft->id,
                        'publication_team_id' => $draft->publication_team_id,
                        'review_type' => $slide->review_type,
                        'anatomy_section' => $slide->anatomy_section,
                    ],
                    [
                        'publication_id' => $draft->publication_id,
                        'reviewer_id' => $slide->reviewer_id,
                        'sort_order' => $slide->sort_order,
                        'answers' => $slide->answers,
                        'notes' => $slide->notes,
                        'saved_at' => $slide->saved_at,
                    ]
                );
            }
        });
    }

    public function publicationTeam()
    {
        return $this->belongsTo(PublicationTeam::class);
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviews()
    {
        return $this->hasMany(PublicationReview::class);
    }

    public function reviewSlides()
    {
        return $this->hasMany(PublicationReviewSlide::class);
    }

    public function getFileUrlAttribute(): string
    {
        if ($this->is_link && $this->external_url) {
            return $this->external_url;
        }

        return Storage::url($this->file_path);
    }

    public function getIsLinkAttribute(): bool
    {
        return $this->source_type === 'link' && !empty($this->external_url);
    }

    public function getFileExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->file_original_name, PATHINFO_EXTENSION));
    }
}