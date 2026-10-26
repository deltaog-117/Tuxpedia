<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\TalkReply;
use App\Features\Wiki\Models\TalkTopic;
use App\Features\Wiki\ValueObjects\TalkBody;

final class ReplyToTalkTopic
{
    public function execute(TalkTopic $topic, TalkBody $body, string $author): TalkReply
    {
        $reply = $topic->replies()->create([
            'body' => $body->toString(),
            'author' => $author,
        ]);

        // A reply moves the topic to the top of the talk page.
        $topic->touch();

        return $reply;
    }
}
