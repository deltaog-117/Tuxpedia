<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Auth\Models\User;
use App\Features\Auth\ValueObjects\Role;
use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Policies\PagePolicy;
use Tests\TestCase;

class PagePolicyTest extends TestCase
{
    private PagePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PagePolicy();
    }

    public function test_only_editors_and_admins_may_create(): void
    {
        $this->assertFalse($this->policy->create($this->userWith(Role::Reader)));
        $this->assertTrue($this->policy->create($this->userWith(Role::Editor)));
        $this->assertTrue($this->policy->create($this->userWith(Role::Admin)));
    }

    public function test_editing_an_open_page_needs_an_editor(): void
    {
        $page = $this->page(protected: false);

        $this->assertFalse($this->policy->update($this->userWith(Role::Reader), $page));
        $this->assertTrue($this->policy->update($this->userWith(Role::Editor), $page));
        $this->assertTrue($this->policy->update($this->userWith(Role::Admin), $page));
    }

    public function test_a_protected_page_can_only_be_edited_by_an_admin(): void
    {
        $page = $this->page(protected: true);

        $this->assertFalse($this->policy->update($this->userWith(Role::Reader), $page));
        $this->assertFalse($this->policy->update($this->userWith(Role::Editor), $page));
        $this->assertTrue($this->policy->update($this->userWith(Role::Admin), $page));
    }

    public function test_restoring_and_protecting_are_admin_only_even_on_open_pages(): void
    {
        $page = $this->page(protected: false);

        foreach ([Role::Reader, Role::Editor] as $role) {
            $this->assertFalse($this->policy->revert($this->userWith($role), $page), $role->value);
            $this->assertFalse($this->policy->protect($this->userWith($role), $page), $role->value);
        }

        $admin = $this->userWith(Role::Admin);
        $this->assertTrue($this->policy->revert($admin, $page));
        $this->assertTrue($this->policy->protect($admin, $page));
    }

    private function userWith(Role $role): User
    {
        return (new User())->forceFill(['role' => $role]);
    }

    private function page(bool $protected): Page
    {
        $page = new Page();
        $page->protected_at = $protected ? now() : null;

        return $page;
    }
}
