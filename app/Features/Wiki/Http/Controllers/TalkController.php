<?php

declare(strict_types=1);

namespace App\Features\Wiki\Http\Controllers;

use App\Features\Wiki\Actions\GetPage;
use App\Features\Wiki\Actions\OpenTalkTopic;
use App\Features\Wiki\Actions\ReplyToTalkTopic;
use App\Features\Wiki\Exceptions\PageNotFoundException;
use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\TalkTopic;
use App\Features\Wiki\ValueObjects\TalkBody;
use App\Features\Wiki\ValueObjects\TalkTitle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TalkController
{
    public function index(string $slug, GetPage $getPage): View
    {
        $page = $this->findPage($slug, $getPage);

        return view('features.wiki.talk', [
            'page' => $page,
            'topics' => $page->talkTopics()->withCount('replies')->get(),
        ]);
    }

    public function show(string $slug, int $topic, GetPage $getPage): View
    {
        $page = $this->findPage($slug, $getPage);

        return view('features.wiki.talk-topic', [
            'page' => $page,
            'topic' => $page->talkTopics()->with('replies')->findOrFail($topic),
        ]);
    }

    public function store(Request $request, string $slug, GetPage $getPage, OpenTalkTopic $open): RedirectResponse
    {
        $page = $this->findPage($slug, $getPage);
        Gate::authorize('discuss', $page);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:'.TalkTitle::MAX_LENGTH],
            'body' => ['required', 'string', 'max:'.TalkBody::MAX_LENGTH],
        ]);

        $topic = $open->execute(
            $page,
            TalkTitle::fromString($data['title']),
            TalkBody::fromString($data['body']),
            $request->user()->name,
        );

        return redirect()->route('wiki.talk.topic', [$page->slug->toString(), $topic->id]);
    }

    public function reply(Request $request, string $slug, int $topic, GetPage $getPage, ReplyToTalkTopic $replyTo): RedirectResponse
    {
        $page = $this->findPage($slug, $getPage);
        Gate::authorize('discuss', $page);
        $talkTopic = $page->talkTopics()->findOrFail($topic);
        $data = $request->validate([
            'body' => ['required', 'string', 'max:'.TalkBody::MAX_LENGTH],
        ]);

        $replyTo->execute($talkTopic, TalkBody::fromString($data['body']), $request->user()->name);

        return redirect()->route('wiki.talk.topic', [$page->slug->toString(), $talkTopic->id]);
    }

    public function destroyTopic(string $slug, int $topic, GetPage $getPage): RedirectResponse
    {
        $page = $this->findPage($slug, $getPage);
        Gate::authorize('moderate', $page);
        $page->talkTopics()->findOrFail($topic)->delete();

        return redirect()
            ->route('wiki.talk', $page->slug->toString())
            ->with('message', 'Topic deleted.');
    }

    public function destroyReply(string $slug, int $topic, int $reply, GetPage $getPage): RedirectResponse
    {
        $page = $this->findPage($slug, $getPage);
        Gate::authorize('moderate', $page);
        /** @var TalkTopic $talkTopic */
        $talkTopic = $page->talkTopics()->findOrFail($topic);
        $talkTopic->replies()->findOrFail($reply)->delete();

        return redirect()
            ->route('wiki.talk.topic', [$page->slug->toString(), $talkTopic->id])
            ->with('message', 'Reply deleted.');
    }

    private function findPage(string $slug, GetPage $getPage): Page
    {
        try {
            return $getPage->execute($slug);
        } catch (PageNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }
}
