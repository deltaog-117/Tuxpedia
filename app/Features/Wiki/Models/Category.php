<?php

declare(strict_types=1);

namespace App\Features\Wiki\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A topic that groups articles (init system, package manager, desktop).
 */
class Category extends Model
{
    protected $table = 'wiki_categories';

    protected $fillable = [
        'slug',
        'name',
        'description',
    ];

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'wiki_category_page', 'category_id', 'page_id');
    }
}
