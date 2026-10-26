<?php

declare(strict_types=1);

namespace App\Features\Wiki\Models;

use App\Features\Auth\Models\User;
use App\Features\DistroComparison\Models\Distribution;
use App\Features\Wiki\ValueObjects\Content;
use App\Features\Wiki\ValueObjects\Slug;
use App\Features\Wiki\ValueObjects\Title;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $table = 'wiki_pages';

    protected $fillable = [
        'slug',
        'title',
        'content',
        'author',
        'distribution_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'protected_at' => 'datetime',
    ];

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'wiki_category_page', 'page_id', 'category_id')->orderBy('name');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class)->orderByDesc('created_at')->orderByDesc('id');
    }

    public function getSlugAttribute(string $value): Slug
    {
        return Slug::fromString($value);
    }

    public function setSlugAttribute(Slug|string $value): void
    {
        $this->attributes['slug'] = (string) ($value instanceof Slug ? $value : Slug::fromString($value));
    }

    public function getTitleAttribute(string $value): Title
    {
        return Title::fromString($value);
    }

    public function setTitleAttribute(Title|string $value): void
    {
        $this->attributes['title'] = (string) ($value instanceof Title ? $value : Title::fromString($value));
    }

    public function getContentAttribute(string $value): Content
    {
        return Content::fromString($value);
    }

    public function setContentAttribute(Content|string $value): void
    {
        $this->attributes['content'] = (string) ($value instanceof Content ? $value : Content::fromString($value));
    }

    public function isProtected(): bool
    {
        return $this->protected_at !== null;
    }

    public function toMarkdownFileName(): string
    {
        return $this->slug . '.md';
    }

    public function getMarkdownFilePath(): string
    {
        return 'pages/' . $this->toMarkdownFileName();
    }

    public function wasUpdatedSinceLastVisit(?User $user): bool
    {
        if (!$user || !$user->last_visited_at) {
            return false;
        }
        return $this->updated_at->gt($user->last_visited_at);
    }
}
