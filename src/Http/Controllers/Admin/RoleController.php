<?php

declare(strict_types=1);

namespace AdminRoles\Http\Controllers\Admin;

use AdminRoles\Models\AdminRole;
use AdminRoles\Services\PermissionCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Pterodactyl\Facades\Activity;
use Pterodactyl\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/** Native extension API for root administrators to maintain delegated roles. */
final class RoleController
{
    public function index(Request $request): JsonResponse
    {
        $this->rootUser($request);

        return new JsonResponse([
            'object' => 'list',
            'data' => AdminRole::query()
                ->withCount('assignments')
                ->orderBy('name')
                ->get()
                ->map(fn (AdminRole $role): array => $this->resource($role))
                ->values()
                ->all(),
            'meta' => [
                'permissions' => PermissionCatalog::definitions(),
                'root_only' => PermissionCatalog::rootOnlyCapabilities(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->rootUser($request);
        $role = AdminRole::query()->create($this->validated($request));

        Activity::event('admin:role.create')
            ->subject($role)
            ->property(['slug' => $role->slug, 'permissions' => $role->permissions])
            ->log();

        return new JsonResponse(['object' => 'admin_role', 'attributes' => $this->resource($role)], Response::HTTP_CREATED);
    }

    public function show(Request $request, AdminRole $role): JsonResponse
    {
        $this->rootUser($request);
        $role->loadCount('assignments');

        return new JsonResponse(['object' => 'admin_role', 'attributes' => $this->resource($role)]);
    }

    public function update(Request $request, AdminRole $role): JsonResponse
    {
        $this->rootUser($request);
        $role->fill($this->validated($request, $role))->save();
        $role->loadCount('assignments');

        Activity::event('admin:role.update')
            ->subject($role)
            ->property(['slug' => $role->slug, 'permissions' => $role->permissions])
            ->log();

        return new JsonResponse(['object' => 'admin_role', 'attributes' => $this->resource($role)]);
    }

    public function destroy(Request $request, AdminRole $role): JsonResponse
    {
        $this->rootUser($request);

        Activity::event('admin:role.delete')
            ->subject($role)
            ->property('slug', $role->slug)
            ->log();

        // The foreign key cascade deliberately removes assignments. A deleted role
        // therefore removes access immediately rather than leaving stale access data.
        $role->delete();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /** @return array{name: string, slug: string, description: string|null, permissions: list<string>} */
    private function validated(Request $request, ?AdminRole $role = null): array
    {
        /** @var array{name: string, slug: string, description?: string|null, permissions: list<string>} $validated */
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80', Rule::unique('admin_roles_roles', 'name')->ignore($role)],
            'slug' => ['required', 'string', 'regex:/^[a-z][a-z0-9-]{0,79}$/', Rule::unique('admin_roles_roles', 'slug')->ignore($role)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['required', 'array', 'min:1', 'max:64'],
            'permissions.*' => ['required', 'string', 'distinct:strict', Rule::in(PermissionCatalog::assignableKeys())],
        ]);

        $validated['description'] = isset($validated['description']) && trim($validated['description']) !== ''
            ? trim($validated['description'])
            : null;
        $validated['permissions'] = array_values(array_unique($validated['permissions']));
        sort($validated['permissions'], SORT_STRING);

        return $validated;
    }

    /** @return array{id: int, name: string, slug: string, description: string|null, permissions: list<string>, assigned_users: int} */
    private function resource(AdminRole $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'permissions' => $role->permissions,
            'assigned_users' => (int) ($role->assignments_count ?? $role->assignments()->count()),
        ];
    }

    private function rootUser(Request $request): User
    {
        $user = $request->user();
        if (! $user instanceof User || ! $user->root_admin) {
            throw new AccessDeniedHttpException('Only a root administrator can manage delegated roles.');
        }

        return $user;
    }
}
