import { useQuery } from '@tanstack/react-query';
import type { ChangeEvent } from 'react';
import { Alert, Spinner, type FormExtensionProps } from '@pterodactyl/sdk';
import { getRoles, rolesQueryKey, type AdminRole, type RolesResponse } from './api';

type UserRoleValues = { role_id: number | null };

/** Rendered by the Panel inside its normal admin user create/edit form. */
export default function UserRoleField({ field, resource }: FormExtensionProps<UserRoleValues, 'admin.user'>) {
    const roleId = field('role_id');
    const roles = useQuery<RolesResponse>({ queryKey: rolesQueryKey, queryFn: getRoles });
    const isRootUser = resource?.attributes.root_admin === true;

    if (isRootUser) {
        return (
            <Alert type='info' title='Administrator account'>
                Root administrators are always highest priority and do not receive a delegated role.
            </Alert>
        );
    }

    if (roles.isLoading) {
        return <Spinner centered>Loading available roles</Spinner>;
    }

    if (roles.isError) {
        return (
            <Alert type='danger' title='Roles unavailable'>
                The available delegated roles could not be loaded. The user will not be assigned a role until this is
                resolved.
            </Alert>
        );
    }

    return (
        <div className='ar:space-y-2'>
            <label className='ar:block ar:text-sm ar:font-medium ar:text-foreground' htmlFor={roleId.id}>
                Delegated admin role
            </label>
            <select
                id={roleId.id}
                value={roleId.value ?? ''}
                className='ar:block ar:w-full ar:rounded-md ar:border ar:border-input ar:bg-background ar:px-3 ar:py-2 ar:text-sm ar:text-foreground ar:shadow-sm ar:outline-none ar:focus:ring-2 ar:focus:ring-ring'
                aria-invalid={roleId.error ? true : undefined}
                aria-describedby={roleId.error ? `${roleId.id}-error` : undefined}
                onChange={(event: ChangeEvent<HTMLSelectElement>) => {
                    const value = event.currentTarget.value;
                    roleId.setValue(value === '' ? null : Number(value));
                }}
            >
                <option value=''>No delegated admin role</option>
                {roles.data?.data.map((role: AdminRole) => (
                    <option key={role.id} value={role.id}>
                        {role.name}
                    </option>
                ))}
            </select>
            <p className='ar:text-xs ar:text-muted-foreground'>
                Roles grant only their checked permissions. They never make this user a root Administrator.
            </p>
            {roleId.error && (
                <p id={`${roleId.id}-error`} className='ar:text-xs ar:text-destructive'>
                    {roleId.error}
                </p>
            )}
        </div>
    );
}
