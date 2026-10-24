<?php

declare(strict_types=1);

namespace AdminRoles\Services;

use AdminRoles\Models\AdminRoleAssignment;
use Illuminate\Http\Request;
use Pterodactyl\Models\User;
use Throwable;

/**
 * Service consumed by the small Panel integration patch through the
 * `admin-roles.authorizer` container binding. It is deliberately stateless so
 * it is safe under long-running PHP workers.
 */
final class RoleAuthorizer
{
    public function hasAdminAccess(User $user): bool
    {
        return $user->root_admin || $this->permissions($user) !== [];
    }

    /**
     * Resolves an admin API route to one exact delegated permission. Unknown and
     * root-only routes resolve to null and are therefore denied.
     */
    public function allows(User $user, Request $request): bool
    {
        if ($user->root_admin) {
            return true;
        }

        $route = $request->route();
        $name = is_object($route) && method_exists($route, 'getName') ? $route->getName() : null;
        $permission = PermissionCatalog::permissionForRoute(is_string($name) ? $name : null);

        return $permission !== null && $this->allowsPermission($user, $permission);
    }

    /** @param list<string> $required */
    public function allowsPermissions(User $user, array $required): bool
    {
        if ($user->root_admin) {
            return true;
        }

        if ($required === []) {
            return false;
        }

        foreach ($required as $permission) {
            if (! $this->allowsPermission($user, $permission)) {
                return false;
            }
        }

        return true;
    }

    public function allowsPermission(User $user, string $permission): bool
    {
        return $user->root_admin || in_array($permission, $this->permissions($user), true);
    }

    /** @return list<string> */
    public function permissions(User $user): array
    {
        if ($user->root_admin) {
            return ['*'];
        }

        try {
            $permissions = [];
            $assignments = AdminRoleAssignment::query()
                ->with('role:id,permissions')
                ->where('user_id', $user->id)
                ->get();

            foreach ($assignments as $assignment) {
                $rolePermissions = $assignment->role?->permissions;
                if (! is_array($rolePermissions)) {
                    continue;
                }

                foreach ($rolePermissions as $permission) {
                    if (is_string($permission) && PermissionCatalog::isAssignable($permission)) {
                        $permissions[$permission] = true;
                    }
                }
            }

            $keys = array_keys($permissions);
            sort($keys, SORT_STRING);

            return $keys;
        } catch (Throwable) {
            // A missing migration or an unavailable extension table must never turn
            // into access. Root administrators remain unaffected by this fallback.
            return [];
        }
    }
}
