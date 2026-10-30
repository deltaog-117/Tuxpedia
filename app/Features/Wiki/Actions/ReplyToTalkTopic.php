<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Auth\Models\User;
use App\Features\Wiki\Models\TalkReply;
use App\Features\Wiki\Models\TalkTopic;
use App\Features\Wiki\ValueObjects\TalkBody;

final class ReplyToTalkTopic
{
    public function execute(TalkTopic $topic, TalkBody $body, User $author): TalkReply
    {
        $reply = $topic->replies()->create([
            'body' => $body->toString(),
            'author' => $author->name,
            'user_id' => $author->id,
        ]);

        // A reply moves the topic to the top of the talk page.
        $topic->touch();

        return $reply;
    }
}
