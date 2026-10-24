import { http } from '@pterodactyl/sdk';

export interface AdminRole {
    readonly id: number;
    readonly name: string;
    readonly slug: string;
    readonly description: string | null;
    readonly permissions: readonly string[];
    readonly assigned_users: number;
}

export interface PermissionDefinition {
    readonly key: string;
    readonly group: string;
    readonly label: string;
    readonly description: string;
    readonly assignable: boolean;
}

export interface RolesResponse {
    readonly object: 'list';
    readonly data: readonly AdminRole[];
    readonly meta: {
        readonly permissions: readonly PermissionDefinition[];
        readonly root_only: readonly string[];
    };
}

export interface RoleDraft {
    readonly name: string;
    readonly slug: string;
    readonly description: string;
    readonly permissions: readonly string[];
}

const rolesUrl = '/api/admin/extensions/admin-roles/roles';

export const rolesQueryKey = ['admin-roles', 'roles'] as const;

export async function getRoles(): Promise<RolesResponse> {
    const { data } = await http.get<RolesResponse>(rolesUrl);

    return data;
}

export async function createRole(draft: RoleDraft): Promise<AdminRole> {
    const { data } = await http.post<{ object: 'admin_role'; attributes: AdminRole }>(rolesUrl, draft);

    return data.attributes;
}

export async function updateRole(id: number, draft: RoleDraft): Promise<AdminRole> {
    const { data } = await http.put<{ object: 'admin_role'; attributes: AdminRole }>(`${rolesUrl}/${id}`, draft);

    return data.attributes;
}

export async function deleteRole(id: number): Promise<void> {
    await http.delete(`${rolesUrl}/${id}`);
}
