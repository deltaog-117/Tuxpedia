<?php

declare(strict_types=1);

namespace App\Features\DistroComparison\Models;

use App\Features\DistroComparison\ValueObjects\Accent;
use App\Features\Wiki\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distribution extends Model
{
    protected $table = 'distributions';

    protected $fillable = [
        'name',
        'slug',
        'based_on',
        'package_manager',
        'default_desktop',
        'release_model',
        'architecture',
        'description',
        'website',
        'accent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function getAccentAttribute(?string $value): ?Accent
    {
        return $value === null ? null : Accent::fromString($value);
    }

    public function setAccentAttribute(Accent|string|null $value): void
    {
        $this->attributes['accent'] = $value === null ? null : (string) ($value instanceof Accent ? $value : Accent::fromString($value));
    }

    /**
     * Inline style that exposes the accent to CSS as `--distro-accent`; empty when unset.
     */
    public function accentStyle(): string
    {
        return $this->accent === null ? '' : '--distro-accent: ' . $this->accent->toRgbChannels();
    }
}
