import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useEffect, useMemo, useState, type ChangeEvent, type FormEvent } from 'react';
import {
    Alert,
    Button,
    Card,
    Checkbox,
    httpErrorToHuman,
    Input,
    PageContentBlock,
    PageHeading,
    Spinner,
    TextArea,
    toast,
    useCurrentUser,
    type ScreenComponentProps,
} from '@pterodactyl/sdk';
import {
    createRole,
    deleteRole,
    getRoles,
    rolesQueryKey,
    updateRole,
    type AdminRole,
    type PermissionDefinition,
    type RolesResponse,
    type RoleDraft,
} from './api';
import { setRolePermission, setRolePermissionGroup, slugifyRole } from './roleHelpers';

type EditorTarget = AdminRole | 'new' | null;

const emptyDraft = (): RoleDraft => ({ name: '', slug: '', description: '', permissions: [] });

const draftFromRole = (role: AdminRole | 'new'): RoleDraft =>
    role === 'new'
        ? emptyDraft()
        : {
              name: role.name,
              slug: role.slug,
              description: role.description ?? '',
              permissions: [...role.permissions],
          };

function PermissionEditor({
    permissions,
    selected,
    disabled,
    onChange,
}: {
    permissions: readonly PermissionDefinition[];
    selected: readonly string[];
    disabled: boolean;
    onChange: (permissions: readonly string[]) => void;
}) {
    const selectedKeys = new Set(selected);
    const groups = useMemo(() => {
        const grouped = new Map<string, PermissionDefinition[]>();

        for (const permission of permissions) {
            if (!permission.assignable) continue;

            const entries = grouped.get(permission.group) ?? [];
            entries.push(permission);
            grouped.set(permission.group, entries);
        }

        return [...grouped.entries()];
    }, [permissions]);

    const setPermission = (key: string, checked: boolean) => {
        onChange(setRolePermission(selected, key, checked));
    };

    const setGroup = (entries: readonly PermissionDefinition[], checked: boolean) => {
        onChange(setRolePermissionGroup(selected, entries.map((entry) => entry.key), checked));
    };

    return (
        <div className='ar:space-y-5'>
            <div>
                <h3 className='ar:text-sm ar:font-semibold ar:text-foreground'>Permissions</h3>
                <p className='ar:mt-1 ar:text-xs ar:text-muted-foreground'>
                    Only checked permissions are granted. Unknown future Panel endpoints remain denied by default.
                </p>
            </div>
            {groups.map(([group, entries]) => {
                const selectedCount = entries.filter((entry) => selectedKeys.has(entry.key)).length;
                const allSelected = selectedCount === entries.length;

                return (
                    <section key={group} className='ar:rounded-lg ar:border ar:border-border ar:bg-card'>
                        <label className='ar:flex ar:items-center ar:gap-3 ar:border-b ar:border-border ar:px-4 ar:py-3 ar:cursor-pointer'>
                            <Checkbox
                                checked={allSelected}
                                indeterminate={selectedCount > 0 && !allSelected}
                                disabled={disabled}
                                onChange={(checked) => setGroup(entries, checked)}
                                aria-label={`Select all ${group} permissions`}
                            />
                            <span className='ar:flex-1 ar:text-sm ar:font-medium ar:text-foreground'>{group}</span>
                            <span className='ar:text-xs ar:text-muted-foreground'>
                                {selectedCount}/{entries.length}
                            </span>
                        </label>
                        <div className='ar:divide-y ar:divide-border'>
                            {entries.map((permission) => (
                                <label
                                    key={permission.key}
                                    className='ar:flex ar:items-start ar:gap-3 ar:px-4 ar:py-3 ar:cursor-pointer ar:hover:bg-muted/40'
                                >
                                    <Checkbox
                                        checked={selectedKeys.has(permission.key)}
                                        disabled={disabled}
                                        onChange={(checked) => setPermission(permission.key, checked)}
                                        aria-label={permission.label}
                                    />
                                    <span className='ar:min-w-0'>
                                        <span className='ar:block ar:text-sm ar:text-foreground'>{permission.label}</span>
                                        <span className='ar:mt-0.5 ar:block ar:text-xs ar:text-muted-foreground'>
                                            {permission.description}
                                        </span>
                                    </span>
                                </label>
                            ))}
                        </div>
                    </section>
                );
            })}
        </div>
    );
}

function RoleEditor({
    target,
    permissions,
    saving,
    onCancel,
    onSave,
}: {
    target: Exclude<EditorTarget, null>;
    permissions: readonly PermissionDefinition[];
    saving: boolean;
    onCancel: () => void;
    onSave: (draft: RoleDraft) => void;
}) {
    const [draft, setDraft] = useState<RoleDraft>(() => draftFromRole(target));

    useEffect(() => setDraft(draftFromRole(target)), [target]);

    const submit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        if (draft.permissions.length === 0) {
            toast.error('Choose at least one delegated permission.');
            return;
        }

        onSave({
            ...draft,
            name: draft.name.trim(),
            slug: draft.slug.trim(),
            description: draft.description.trim(),
            permissions: [...draft.permissions].sort(),
        });
    };

    return (
        <Card title={target === 'new' ? 'Create role' : `Edit ${target.name}`}>
            <form className='ar:space-y-6' onSubmit={submit}>
                <div className='ar:grid ar:gap-4 ar:md:grid-cols-2'>
                    <label className='ar:space-y-1.5'>
                        <span className='ar:block ar:text-sm ar:font-medium ar:text-foreground'>Name</span>
                        <Input
                            required
                            maxLength={80}
                            value={draft.name}
                            disabled={saving}
                            placeholder='Support staff'
                            onChange={(event: ChangeEvent<HTMLInputElement>) => {
                                const name = event.currentTarget.value;
                                setDraft((current) => ({
                                    ...current,
                                    name,
                                    slug: current.slug === slugifyRole(current.name) ? slugifyRole(name) : current.slug,
                                }));
                            }}
                        />
                    </label>
                    <label className='ar:space-y-1.5'>
                        <span className='ar:block ar:text-sm ar:font-medium ar:text-foreground'>Identifier</span>
                        <Input
                            required
                            maxLength={80}
                            pattern='[a-z][a-z0-9-]{0,79}'
                            value={draft.slug}
                            disabled={saving}
                            placeholder='support-staff'
                            onChange={(event: ChangeEvent<HTMLInputElement>) =>
                                setDraft((current) => ({ ...current, slug: event.currentTarget.value }))
                            }
                        />
                        <span className='ar:block ar:text-xs ar:text-muted-foreground'>Lowercase letters, numbers, and hyphens.</span>
                    </label>
                </div>
                <label className='ar:block ar:space-y-1.5'>
                    <span className='ar:block ar:text-sm ar:font-medium ar:text-foreground'>Description</span>
                    <TextArea
                        rows={2}
                        maxLength={255}
                        value={draft.description}
                        disabled={saving}
                        placeholder='What this role is allowed to do.'
                        onChange={(event: ChangeEvent<HTMLTextAreaElement>) =>
                            setDraft((current) => ({ ...current, description: event.currentTarget.value }))
                        }
                    />
                </label>
                <PermissionEditor
                    permissions={permissions}
                    selected={draft.permissions}
                    disabled={saving}
                    onChange={(selected) => setDraft((current) => ({ ...current, permissions: [...selected] }))}
                />
                <div className='ar:flex ar:flex-wrap ar:justify-end ar:gap-3'>
                    <Button type='button' isSecondary disabled={saving} onClick={onCancel}>
                        Cancel
                    </Button>
                    <Button type='submit' color='green' isLoading={saving}>
                        {target === 'new' ? 'Create role' : 'Save role'}
                    </Button>
                </div>
            </form>
        </Card>
    );
}

export default function RolesScreen(_props: ScreenComponentProps) {
    const user = useCurrentUser();
    const queryClient = useQueryClient();
    const [editor, setEditor] = useState<EditorTarget>(null);
    const roles = useQuery<RolesResponse>({ queryKey: rolesQueryKey, queryFn: getRoles, enabled: user.rootAdmin });

    const save = useMutation<AdminRole, unknown, RoleDraft>({
        mutationFn: (draft: RoleDraft) =>
            editor !== null && editor !== 'new' ? updateRole(editor.id, draft) : createRole(draft),
        onSuccess: async (role: AdminRole) => {
            await queryClient.invalidateQueries({ queryKey: rolesQueryKey });
            toast.success(`Role "${role.name}" saved.`);
            setEditor(null);
        },
        onError: (error: unknown) => toast.error(httpErrorToHuman(error)),
    });

    const remove = useMutation({
        mutationFn: deleteRole,
        onSuccess: async () => {
            await queryClient.invalidateQueries({ queryKey: rolesQueryKey });
            toast.success('Role deleted and its users no longer have delegated access.');
        },
        onError: (error: unknown) => toast.error(httpErrorToHuman(error)),
    });

    if (!user.rootAdmin) {
        return (
            <PageContentBlock>
                <Alert type='danger' title='Administrator access required'>
                    Only the built-in root Administrator can create, change, assign, or delete delegated roles.
                </Alert>
            </PageContentBlock>
        );
    }

    if (roles.isLoading) {
        return (
            <PageContentBlock>
                <Spinner centered>Loading roles</Spinner>
            </PageContentBlock>
        );
    }

    if (roles.isError || !roles.data) {
        return (
            <PageContentBlock>
                <Alert type='danger' title='Unable to load roles'>
                    The role service did not respond. Check that the Admin Roles extension migration and its required Panel
                    integration patch are installed.
                </Alert>
            </PageContentBlock>
        );
    }

    return (
        <PageContentBlock>
            <PageHeading
                title='Admin Roles'
                description='Create least-privilege roles for the Panel administration area. Root Administrator remains above every role.'
                actions={
                    <Button color='green' onClick={() => setEditor('new')}>
                        New role
                    </Button>
                }
            />
            <div className='ar:mt-6 ar:space-y-6'>
                <Alert type='info' title='Administrator is always highest'>
                    Roles never set <code>root_admin</code>. Role management, extension management, application API keys,
                    sensitive node credentials, advanced settings, root accounts, and unknown future admin routes stay
                    root-only.
                </Alert>

                {editor !== null && (
                    <RoleEditor
                        target={editor}
                        permissions={roles.data.meta.permissions}
                        saving={save.isPending}
                        onCancel={() => setEditor(null)}
                        onSave={(draft) => save.mutate(draft)}
                    />
                )}

                <Card title='Delegated roles'>
                    {roles.data.data.length === 0 ? (
                        <p className='ar:text-sm ar:text-muted-foreground'>No delegated roles exist yet.</p>
                    ) : (
                        <div className='ar:divide-y ar:divide-border'>
                            {roles.data.data.map((role: AdminRole) => (
                                <article key={role.id} className='ar:flex ar:flex-col ar:gap-4 ar:py-4 ar:sm:flex-row ar:sm:items-start'>
                                    <div className='ar:min-w-0 ar:flex-1'>
                                        <div className='ar:flex ar:flex-wrap ar:items-center ar:gap-x-3 ar:gap-y-1'>
                                            <h2 className='ar:text-base ar:font-semibold ar:text-foreground'>{role.name}</h2>
                                            <code className='ar:rounded ar:bg-muted ar:px-1.5 ar:py-0.5 ar:text-xs ar:text-muted-foreground'>
                                                {role.slug}
                                            </code>
                                        </div>
                                        {role.description && (
                                            <p className='ar:mt-1 ar:text-sm ar:text-muted-foreground'>{role.description}</p>
                                        )}
                                        <p className='ar:mt-2 ar:text-xs ar:text-muted-foreground'>
                                            {role.permissions.length} permission{role.permissions.length === 1 ? '' : 's'} · {role.assigned_users}{' '}
                                            assigned user{role.assigned_users === 1 ? '' : 's'}
                                        </p>
                                    </div>
                                    <div className='ar:flex ar:shrink-0 ar:gap-2'>
                                        <Button isSecondary onClick={() => setEditor(role)}>
                                            Edit
                                        </Button>
                                        <Button
                                            color='red'
                                            isSecondary
                                            disabled={remove.isPending}
                                            onClick={() => {
                                                if (window.confirm(`Delete role "${role.name}"? Assigned users will lose delegated access.`)) {
                                                    remove.mutate(role.id);
                                                }
                                            }}
                                        >
                                            Delete
                                        </Button>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </Card>

                <Card title='Root-only safeguards'>
                    <ul className='ar:list-disc ar:space-y-1 ar:pl-5 ar:text-sm ar:text-muted-foreground'>
                        {roles.data.meta.root_only.map((capability: string) => (
                            <li key={capability}>{capability}</li>
                        ))}
                    </ul>
                </Card>
            </div>
        </PageContentBlock>
    );
}
