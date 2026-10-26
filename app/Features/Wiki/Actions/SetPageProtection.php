<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use Illuminate\Support\Facades\Log;

final class SetPageProtection
{
    /**
     * @return bool true when the state changed
     */
    public function execute(Page $page, bool $protected, ?string $by = null): bool
    {
        if ($page->isProtected() === $protected) {
            return false;
        }

        $page->protected_at = $protected ? now() : null;
        $page->save();

        Log::info($protected ? 'Wiki page protected' : 'Wiki page unprotected', [
            'slug' => $page->slug->toString(),
            'by' => $by,
        ]);

        return true;
    }
}
