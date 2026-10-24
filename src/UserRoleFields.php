<?php

declare(strict_types=1);

namespace AdminRoles;

use AdminRoles\Models\AdminRoleAssignment;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Validation\Rule;
use Pterodactyl\Extensions\Fields;
use Pterodactyl\Models\User;

/** Adds the optional delegated-role selector to the native admin user form. */
final class UserRoleFields extends Fields
{
    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [
            'role_id' => ['nullable', 'integer', Rule::exists('admin_roles_roles', 'id')],
        ];
    }

    public function authorize(#[CurrentUser] User $admin): bool
    {
        // Delegated administrators can never create or assign roles. This keeps
        // Administrator/root_admin strictly above every role this extension stores.
        return $admin->root_admin;
    }

    /** @return array{role_id: int|null} */
    public function values(User $user): array
    {
        if ($user->root_admin) {
            return ['role_id' => null];
        }

        $roleId = AdminRoleAssignment::query()
            ->where('user_id', $user->id)
            ->value('role_id');

        return ['role_id' => is_numeric($roleId) ? (int) $roleId : null];
    }

    /** @param array{role_id?: int|null} $values */
    public function save(User $user, array $values): void
    {
        $assignment = AdminRoleAssignment::query()->where('user_id', $user->id);

        // A root account does not have a stored role. Removing a stale assignment
        // also means demoting and re-promoting an account cannot retain a hidden role.
        if ($user->root_admin || ! isset($values['role_id'])) {
            $assignment->delete();

            return;
        }

        AdminRoleAssignment::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['role_id' => $values['role_id']]
        );
    }
}
