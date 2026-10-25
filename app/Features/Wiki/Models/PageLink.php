<?php

declare(strict_types=1);

namespace App\Features\Wiki\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One [[...]] link from a page to a target slug.
 */
class PageLink extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'wiki_links';

    protected $fillable = [
        'page_id',
        'target_slug',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
