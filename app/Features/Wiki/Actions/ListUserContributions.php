<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Auth\Models\User;
use App\Features\Wiki\Models\Revision;
use App\Features\Wiki\Models\TalkReply;
use App\Features\Wiki\Models\TalkTopic;
use App\Features\Wiki\ValueObjects\TalkContribution;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ListUserContributions
{
    public const LIMIT = 20;

    /**
     * The articles an account has edited (most recently edited first, with an edit count)
     * and its latest talk posts.
     *
     * @return array{edits: int, pages: Collection<int, Revision>, talk: Collection<int, TalkContribution>}
     */
    public function execute(User $user): array
    {
        $revisions = Revision::query()->where('user_id', $user->id);

        $pages = (clone $revisions)
            ->selectRaw('page_id, COUNT(*) as edit_count, MAX(created_at) as last_edit')
            ->groupBy('page_id')
            ->orderByDesc('last_edit')
            ->limit(self::LIMIT)
            ->with('page')
            ->get();

        return [
            'edits' => $revisions->count(),
            'pages' => $pages,
            'talk' => $this->talkPosts($user),
        ];
    }

    /**
     * @return Collection<int, TalkContribution>
     */
    private function talkPosts(User $user): Collection
    {
        $topics = TalkTopic::query()
            ->where('user_id', $user->id)
            ->with('page')
            ->latest()
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (TalkTopic $topic) => new TalkContribution(
                $topic->page, $topic->id, $topic->title, false, Str::limit($topic->body, 140), $topic->created_at,
            ));

        $replies = TalkReply::query()
            ->where('user_id', $user->id)
            ->with('topic.page')
            ->latest('created_at')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (TalkReply $reply) => new TalkContribution(
                $reply->topic->page, $reply->topic_id, $reply->topic->title, true, Str::limit($reply->body, 140), $reply->created_at,
            ));

        return $topics->concat($replies)->sortByDesc(fn (TalkContribution $post) => $post->at)->take(self::LIMIT)->values();
    }
}
