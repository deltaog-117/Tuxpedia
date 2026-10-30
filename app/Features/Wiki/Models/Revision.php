<?php

declare(strict_types=1);

namespace App\Features\Wiki\Models;

use App\Features\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One saved version of a page. The content itself is read from Git by sha.
 */
class Revision extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'wiki_revisions';

    protected $fillable = [
        'page_id',
        'sha',
        'path',
        'author',
        'user_id',
        'summary',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shortSha(): string
    {
        return substr($this->sha, 0, 7);
    }
}
