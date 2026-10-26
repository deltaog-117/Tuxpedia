<?php

declare(strict_types=1);

namespace App\Features\Wiki\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalkReply extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'wiki_talk_replies';

    protected $fillable = [
        'topic_id',
        'body',
        'author',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(TalkTopic::class, 'topic_id');
    }
}
