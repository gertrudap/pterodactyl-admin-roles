<?php

declare(strict_types=1);

namespace AdminRoles;

use AdminRoles\Services\RoleAuthorizer;
use Pterodactyl\Extensions\ExtensionProvider;
use Pterodactyl\Models\User;

final class AdminRolesProvider extends ExtensionProvider
{
    public function register(): void
    {
        // The version-pinned Panel integration discovers this binding without
        // depending on extension classes when the extension is not installed.
        $this->app->singleton('admin-roles.authorizer', RoleAuthorizer::class);
    }

    public function boot(): void
    {
        $this->loadExtensionMigrations();
        $this->registerApiRoutes();
        $this->registerFields(User::class, UserRoleFields::class);
    }
}
