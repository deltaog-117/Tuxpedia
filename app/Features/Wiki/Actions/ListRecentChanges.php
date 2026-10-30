<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Revision;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;

class ListRecentChanges
{
    public const PER_PAGE = 50;

    /**
     * Revisions across every page, newest first. Each row carries `previous_sha`, the
     * sha of the revision of the same page just before it (null for the first one).
     *
     * @return LengthAwarePaginator<int, Revision>
     */
    public function execute(): LengthAwarePaginator
    {
        $previous = fn (Builder $query) => $query
            ->from('wiki_revisions as prev')
            ->select('prev.sha')
            ->whereColumn('prev.page_id', 'wiki_revisions.page_id')
            ->where(fn (Builder $older) => $older
                ->whereColumn('prev.created_at', '<', 'wiki_revisions.created_at')
                ->orWhere(fn (Builder $tie) => $tie
                    ->whereColumn('prev.created_at', '=', 'wiki_revisions.created_at')
                    ->whereColumn('prev.id', '<', 'wiki_revisions.id')))
            ->orderByDesc('prev.created_at')
            ->orderByDesc('prev.id')
            ->limit(1);

        return Revision::query()
            ->with(['page', 'user'])
            ->select('wiki_revisions.*')
            ->selectSub($previous, 'previous_sha')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE);
    }
}
