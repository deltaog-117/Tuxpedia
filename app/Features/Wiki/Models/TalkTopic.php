<?php

declare(strict_types=1);

namespace App\Features\Wiki\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A conversation on an article's talk page: a title, an opening message and replies.
 */
class TalkTopic extends Model
{
    protected $table = 'wiki_talk_topics';

    protected $fillable = [
        'page_id',
        'title',
        'body',
        'author',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TalkReply::class, 'topic_id')->orderBy('created_at')->orderBy('id');
    }
}
