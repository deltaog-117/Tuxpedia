<?php

declare(strict_types=1);

namespace App\Features\Wiki\Http\Livewire;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\Wiki\Actions\CreatePage;
use App\Features\Wiki\Actions\UpdatePage;
use App\Features\Wiki\Actions\GetPage;
use App\Features\Wiki\Actions\SyncPageCategories;
use App\Features\Wiki\Exceptions\InvalidSlugException;
use App\Features\Wiki\Exceptions\PageNotFoundException;
use App\Features\Wiki\Models\Page;
use App\Features\Wiki\ValueObjects\Content;
use App\Features\Wiki\ValueObjects\Slug;
use App\Features\Wiki\ValueObjects\Title;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class WikiEditor extends Component
{
    public ?Page $page = null;

    public string $slug = '';
    public string $title = '';
    public string $content = '';
    public string $author = '';
    public string $summary = '';
    // String, not ?int: the <select> posts '' for "no distribution".
    public string $distributionId = '';
    // Comma-separated names; categories are created on first use.
    public string $categories = '';

    public bool $isEditing = false;
    public string $errorMessage = '';

    protected $rules = [
        'slug' => 'required|string|max:255|regex:/^[a-z0-9\-_]+$/',
        'title' => 'required|string|max:255',
        'content' => 'nullable|string',
        'author' => 'nullable|string|max:255',
        'summary' => 'nullable|string|max:255',
        'distributionId' => 'nullable|integer|exists:distributions,id',
        'categories' => 'nullable|string|max:1000',
    ];

    public function mount(?string $slug = null, ?string $prefill = null): void
    {
        Gate::authorize('create', Page::class);

        if ($slug) {
            try {
                $getPage = app(GetPage::class);
                $this->page = $getPage->execute($slug);
                Gate::authorize('update', $this->page);
                $this->slug = $this->page->slug->toString();
                $this->title = $this->page->title->toString();
                $this->content = $this->page->content->toString();
                $this->author = $this->page->author ?? '';
                $this->distributionId = (string) ($this->page->distribution_id ?? '');
                $this->categories = $this->page->categories->pluck('name')->implode(', ');
                $this->isEditing = true;
            } catch (PageNotFoundException $e) {
                abort(404, $e->getMessage());
            }
        } else {
            $this->isEditing = false;
            $this->prefillFrom($prefill);
        }
    }

    /**
     * A red link opens the editor with its slug filled in; anything that is not a valid slug is ignored.
     */
    private function prefillFrom(?string $prefill): void
    {
        try {
            $slug = Slug::fromString((string) $prefill);
        } catch (InvalidSlugException) {
            return;
        }

        $this->slug = $slug->toString();
        $this->title = Str::headline($slug->toString());
    }

    public function save()
    {
        // Livewire calls skip the route middleware, so the rule is checked again on every save.
        Gate::authorize($this->isEditing ? 'update' : 'create', $this->isEditing ? $this->page : Page::class);

        $this->validate();

        try {
            $slugObj = Slug::fromString($this->slug);
            $titleObj = Title::fromString($this->title);
            $contentObj = Content::fromString($this->content);
            $author = $this->author ?: null;
            $distributionId = $this->distributionId === '' ? null : (int) $this->distributionId;

            if ($this->isEditing) {
                $updatePage = app(UpdatePage::class);
                $this->page = $updatePage->execute($this->page, $slugObj, $titleObj, $contentObj, $author, $distributionId, $this->summary);
                session()->flash('message', 'Page updated successfully.');
                Log::info('Wiki page edited via Livewire', ['slug' => $slugObj->toString()]);
            } else {
                $createPage = app(CreatePage::class);
                $this->page = $createPage->execute($slugObj, $titleObj, $contentObj, $author, $distributionId, $this->summary);
                session()->flash('message', 'Page created successfully.');
                Log::info('Wiki page created via Livewire', ['slug' => $slugObj->toString()]);
            }

            app(SyncPageCategories::class)->execute($this->page, $this->categories);

            return redirect()->route('wiki.show', $this->page->slug->toString());
        } catch (InvalidSlugException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('features.wiki.editor', [
            'distributions' => Distribution::orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.app');
    }
}
