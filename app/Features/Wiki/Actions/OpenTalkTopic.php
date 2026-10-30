<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Auth\Models\User;
use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\TalkTopic;
use App\Features\Wiki\ValueObjects\TalkBody;
use App\Features\Wiki\ValueObjects\TalkTitle;

final class OpenTalkTopic
{
    public function execute(Page $page, TalkTitle $title, TalkBody $body, User $author): TalkTopic
    {
        return $page->talkTopics()->create([
            'title' => $title->toString(),
            'body' => $body->toString(),
            'author' => $author->name,
            'user_id' => $author->id,
        ]);
    }
}
