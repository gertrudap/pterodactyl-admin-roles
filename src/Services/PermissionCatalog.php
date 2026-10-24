<?php

declare(strict_types=1);

namespace AdminRoles\Services;

/**
 * The explicit Pterodactyl 2 admin-permission snapshot this extension supports.
 *
 * Keys mirror Pterodactyl\Enum\Permissions. Any core route not listed below is
 * denied to delegated roles, so a Panel upgrade cannot accidentally grant access
 * to a new endpoint. A few capabilities remain root-only because they would let
 * a delegated user take over the Panel, Wings, or another administrator account.
 */
final class PermissionCatalog
{
    /** @var list<array{key: string, group: string, label: string, description: string, assignable: bool}> */
    private const array DEFINITIONS = [
        ['key' => 'admin:activity.read', 'group' => 'Activity', 'label' => 'View activity', 'description' => 'View the Panel activity log and its filters.', 'assignable' => true],

        ['key' => 'admin:users.read', 'group' => 'Users', 'label' => 'View users', 'description' => 'List users. Root-account detail and change routes remain protected.', 'assignable' => true],
        ['key' => 'admin:users.create', 'group' => 'Users', 'label' => 'Create users', 'description' => 'Create non-root Panel user accounts.', 'assignable' => true],
        ['key' => 'admin:users.update', 'group' => 'Users', 'label' => 'Update users', 'description' => 'Edit non-root Panel users and manage their avatars or two-factor reset.', 'assignable' => true],
        ['key' => 'admin:users.delete', 'group' => 'Users', 'label' => 'Delete users', 'description' => 'Delete non-root Panel user accounts.', 'assignable' => true],

        ['key' => 'admin:locations.read', 'group' => 'Locations', 'label' => 'View locations', 'description' => 'List and inspect locations and eligible nodes.', 'assignable' => true],
        ['key' => 'admin:locations.create', 'group' => 'Locations', 'label' => 'Create locations', 'description' => 'Create locations.', 'assignable' => true],
        ['key' => 'admin:locations.update', 'group' => 'Locations', 'label' => 'Update locations', 'description' => 'Edit locations.', 'assignable' => true],
        ['key' => 'admin:locations.delete', 'group' => 'Locations', 'label' => 'Delete locations', 'description' => 'Delete locations.', 'assignable' => true],

        ['key' => 'admin:nodes.read', 'group' => 'Nodes', 'label' => 'View nodes', 'description' => 'List nodes, utilization, and non-secret system information.', 'assignable' => true],
        ['key' => 'admin:nodes.create', 'group' => 'Nodes', 'label' => 'Create nodes', 'description' => 'Create nodes.', 'assignable' => true],
        ['key' => 'admin:nodes.update', 'group' => 'Nodes', 'label' => 'Update nodes', 'description' => 'Edit nodes and their tags.', 'assignable' => true],
        ['key' => 'admin:nodes.delete', 'group' => 'Nodes', 'label' => 'Delete nodes', 'description' => 'Delete nodes.', 'assignable' => true],

        ['key' => 'admin:allocations.read', 'group' => 'Allocations', 'label' => 'View allocations', 'description' => 'List allocations, free allocations, and IP addresses.', 'assignable' => true],
        ['key' => 'admin:allocations.create', 'group' => 'Allocations', 'label' => 'Create allocations', 'description' => 'Create allocations.', 'assignable' => true],
        ['key' => 'admin:allocations.update', 'group' => 'Allocations', 'label' => 'Update allocations', 'description' => 'Update allocation aliases.', 'assignable' => true],
        ['key' => 'admin:allocations.delete', 'group' => 'Allocations', 'label' => 'Delete allocations', 'description' => 'Delete one allocation, a block, or a selected group.', 'assignable' => true],

        ['key' => 'admin:servers.read', 'group' => 'Servers', 'label' => 'View servers', 'description' => 'List and inspect servers and transfer progress.', 'assignable' => true],
        ['key' => 'admin:servers.create', 'group' => 'Servers', 'label' => 'Create servers', 'description' => 'Create servers.', 'assignable' => true],
        ['key' => 'admin:servers.update', 'group' => 'Servers', 'label' => 'Update servers', 'description' => 'Edit server details, build, startup, suspension, reinstall, rebuild, transfer, and install state.', 'assignable' => true],
        ['key' => 'admin:servers.delete', 'group' => 'Servers', 'label' => 'Delete servers', 'description' => 'Delete or force-delete servers.', 'assignable' => true],

        ['key' => 'admin:server-databases.read', 'group' => 'Server databases', 'label' => 'View server databases', 'description' => 'List and inspect databases attached to servers.', 'assignable' => true],
        ['key' => 'admin:server-databases.create', 'group' => 'Server databases', 'label' => 'Create server databases', 'description' => 'Create databases for servers.', 'assignable' => true],
        ['key' => 'admin:server-databases.update', 'group' => 'Server databases', 'label' => 'Update server databases', 'description' => 'Rotate a server database password.', 'assignable' => true],
        ['key' => 'admin:server-databases.delete', 'group' => 'Server databases', 'label' => 'Delete server databases', 'description' => 'Delete server databases.', 'assignable' => true],

        ['key' => 'admin:server-backups.read', 'group' => 'Server backups', 'label' => 'View server backups', 'description' => 'List server backups.', 'assignable' => true],
        ['key' => 'admin:server-backups.update', 'group' => 'Server backups', 'label' => 'Update server backups', 'description' => 'Toggle backup locks.', 'assignable' => true],

        ['key' => 'admin:server-mounts.read', 'group' => 'Server mounts', 'label' => 'View server mounts', 'description' => 'View mounts attached to servers.', 'assignable' => true],
        ['key' => 'admin:server-mounts.create', 'group' => 'Server mounts', 'label' => 'Attach server mounts', 'description' => 'Attach mounts to servers.', 'assignable' => true],
        ['key' => 'admin:server-mounts.delete', 'group' => 'Server mounts', 'label' => 'Detach server mounts', 'description' => 'Detach mounts from servers.', 'assignable' => true],

        ['key' => 'admin:database-hosts.read', 'group' => 'Database hosts', 'label' => 'View database hosts', 'description' => 'List hosts and their databases.', 'assignable' => true],
        ['key' => 'admin:database-hosts.create', 'group' => 'Database hosts', 'label' => 'Create database hosts', 'description' => 'Create database hosts.', 'assignable' => true],
        ['key' => 'admin:database-hosts.update', 'group' => 'Database hosts', 'label' => 'Update database hosts', 'description' => 'Edit database hosts.', 'assignable' => true],
        ['key' => 'admin:database-hosts.delete', 'group' => 'Database hosts', 'label' => 'Delete database hosts', 'description' => 'Delete database hosts.', 'assignable' => true],

        ['key' => 'admin:mounts.read', 'group' => 'Mounts', 'label' => 'View mounts', 'description' => 'List and inspect mounts.', 'assignable' => true],
        ['key' => 'admin:mounts.create', 'group' => 'Mounts', 'label' => 'Create mounts', 'description' => 'Create mounts and attach eggs or nodes.', 'assignable' => true],
        ['key' => 'admin:mounts.update', 'group' => 'Mounts', 'label' => 'Update mounts', 'description' => 'Edit mounts.', 'assignable' => true],
        ['key' => 'admin:mounts.delete', 'group' => 'Mounts', 'label' => 'Delete mounts', 'description' => 'Delete mounts and detach eggs or nodes.', 'assignable' => true],

        ['key' => 'admin:eggs.read', 'group' => 'Eggs', 'label' => 'View eggs', 'description' => 'View eggs, catalog entries, scripts, variables, tags, and exports.', 'assignable' => true],
        ['key' => 'admin:eggs.create', 'group' => 'Eggs', 'label' => 'Create eggs', 'description' => 'Create and import eggs or catalog eggs.', 'assignable' => true],
        ['key' => 'admin:eggs.update', 'group' => 'Eggs', 'label' => 'Update eggs', 'description' => 'Edit eggs, scripts, tags, imports, and variables.', 'assignable' => true],
        ['key' => 'admin:eggs.delete', 'group' => 'Eggs', 'label' => 'Delete eggs', 'description' => 'Delete eggs and variables.', 'assignable' => true],

        ['key' => 'admin:egg-variables.read', 'group' => 'Egg variables', 'label' => 'View egg variables', 'description' => 'View egg variables.', 'assignable' => true],
        ['key' => 'admin:egg-variables.create', 'group' => 'Egg variables', 'label' => 'Create egg variables', 'description' => 'Create egg variables.', 'assignable' => true],
        ['key' => 'admin:egg-variables.update', 'group' => 'Egg variables', 'label' => 'Update egg variables', 'description' => 'Edit or reorder egg variables.', 'assignable' => true],
        ['key' => 'admin:egg-variables.delete', 'group' => 'Egg variables', 'label' => 'Delete egg variables', 'description' => 'Delete egg variables.', 'assignable' => true],

        ['key' => 'admin:tags.read', 'group' => 'Tags', 'label' => 'View tags', 'description' => 'List and inspect tags.', 'assignable' => true],
        ['key' => 'admin:tags.create', 'group' => 'Tags', 'label' => 'Create tags', 'description' => 'Create tags.', 'assignable' => true],
        ['key' => 'admin:tags.update', 'group' => 'Tags', 'label' => 'Update tags', 'description' => 'Edit tags and assign tags to eggs or nodes.', 'assignable' => true],
        ['key' => 'admin:tags.delete', 'group' => 'Tags', 'label' => 'Delete tags', 'description' => 'Delete tags.', 'assignable' => true],

        ['key' => 'admin:settings.read', 'group' => 'Settings', 'label' => 'View settings', 'description' => 'View general and mail settings.', 'assignable' => true],
        ['key' => 'admin:settings.update', 'group' => 'Settings', 'label' => 'Update settings', 'description' => 'Update general or mail settings and send a test email.', 'assignable' => true],

        ['key' => 'admin:version.read', 'group' => 'System', 'label' => 'View version', 'description' => 'View Panel version information.', 'assignable' => true],

        ['key' => 'admin:extensions.read', 'group' => 'Root-only', 'label' => 'View extensions', 'description' => 'Extensions are root-only because they execute Panel code.', 'assignable' => false],
        ['key' => 'admin:extensions.install', 'group' => 'Root-only', 'label' => 'Install extensions', 'description' => 'Only root administrators can install extensions.', 'assignable' => false],
        ['key' => 'admin:extensions.update', 'group' => 'Root-only', 'label' => 'Update extensions', 'description' => 'Only root administrators can change extension state or settings.', 'assignable' => false],
        ['key' => 'admin:extensions.delete', 'group' => 'Root-only', 'label' => 'Delete extensions', 'description' => 'Only root administrators can remove extensions.', 'assignable' => false],
        ['key' => 'admin:themes.read', 'group' => 'Root-only', 'label' => 'View themes', 'description' => 'Theme management remains root-only.', 'assignable' => false],
        ['key' => 'admin:themes.update', 'group' => 'Root-only', 'label' => 'Update themes', 'description' => 'Theme activation, preview, and settings remain root-only.', 'assignable' => false],
        ['key' => 'admin:api-keys.read', 'group' => 'Root-only', 'label' => 'View application API keys', 'description' => 'Application API keys can control the Panel and remain root-only.', 'assignable' => false],
        ['key' => 'admin:api-keys.create', 'group' => 'Root-only', 'label' => 'Create application API keys', 'description' => 'Application API keys can control the Panel and remain root-only.', 'assignable' => false],
        ['key' => 'admin:api-keys.update', 'group' => 'Root-only', 'label' => 'Update application API keys', 'description' => 'Application API keys can control the Panel and remain root-only.', 'assignable' => false],
        ['key' => 'admin:api-keys.delete', 'group' => 'Root-only', 'label' => 'Delete application API keys', 'description' => 'Application API keys can control the Panel and remain root-only.', 'assignable' => false],
    ];

    /** @var array<string, string> */
    private const array ROUTE_PERMISSIONS = [
        'api.admin.languages' => 'admin:users.read',
        'api.admin.activity' => 'admin:activity.read',
        'api.admin.activity.filters' => 'admin:activity.read',

        'api.admin.users' => 'admin:users.read',
        'api.admin.users.view' => 'admin:users.read',
        'api.admin.users.store' => 'admin:users.create',
        'api.admin.users.disable-2fa' => 'admin:users.update',
        'api.admin.users.update' => 'admin:users.update',
        'api.admin.users.avatar.update' => 'admin:users.update',
        'api.admin.users.avatar.delete' => 'admin:users.update',
        'api.admin.users.delete' => 'admin:users.delete',

        'api.admin.nodes' => 'admin:nodes.read',
        'api.admin.nodes.view' => 'admin:nodes.read',
        'api.admin.nodes.system-information' => 'admin:nodes.read',
        'api.admin.nodes.utilization' => 'admin:nodes.read',
        'api.admin.nodes.store' => 'admin:nodes.create',
        'api.admin.nodes.update' => 'admin:nodes.update',
        'api.admin.nodes.tags.sync' => 'admin:nodes.update',
        'api.admin.nodes.deployment-tags.sync' => 'admin:nodes.update',
        'api.admin.nodes.delete' => 'admin:nodes.delete',

        'api.admin.nodes.allocations' => 'admin:allocations.read',
        'api.admin.nodes.allocations.ips' => 'admin:allocations.read',
        'api.admin.nodes.allocations.available' => 'admin:allocations.read',
        'api.admin.nodes.allocations.store' => 'admin:allocations.create',
        'api.admin.nodes.allocations.update' => 'admin:allocations.update',
        'api.admin.nodes.allocations.block' => 'admin:allocations.delete',
        'api.admin.nodes.allocations.bulk-delete' => 'admin:allocations.delete',
        'api.admin.nodes.allocations.delete' => 'admin:allocations.delete',

        'api.admin.locations' => 'admin:locations.read',
        'api.admin.locations.view' => 'admin:locations.read',
        'api.admin.locations.eligible-nodes' => 'admin:locations.read',
        'api.admin.locations.store' => 'admin:locations.create',
        'api.admin.locations.update' => 'admin:locations.update',
        'api.admin.locations.delete' => 'admin:locations.delete',

        'api.admin.mounts' => 'admin:mounts.read',
        'api.admin.mounts.view' => 'admin:mounts.read',
        'api.admin.mounts.store' => 'admin:mounts.create',
        'api.admin.mounts.eggs' => 'admin:mounts.create',
        'api.admin.mounts.nodes' => 'admin:mounts.create',
        'api.admin.mounts.update' => 'admin:mounts.update',
        'api.admin.mounts.delete' => 'admin:mounts.delete',
        'api.admin.mounts.eggs.delete' => 'admin:mounts.delete',
        'api.admin.mounts.nodes.delete' => 'admin:mounts.delete',

        'api.admin.servers' => 'admin:servers.read',
        'api.admin.servers.external' => 'admin:servers.read',
        'api.admin.servers.view' => 'admin:servers.read',
        'api.admin.servers.store' => 'admin:servers.create',
        'api.admin.servers.details' => 'admin:servers.update',
        'api.admin.servers.build' => 'admin:servers.update',
        'api.admin.servers.startup' => 'admin:servers.update',
        'api.admin.servers.suspension' => 'admin:servers.update',
        'api.admin.servers.reinstall' => 'admin:servers.update',
        'api.admin.servers.rebuild' => 'admin:servers.update',
        'api.admin.servers.transfer-progress' => 'admin:servers.read',
        'api.admin.servers.transfer' => 'admin:servers.update',
        'api.admin.servers.toggle-install' => 'admin:servers.update',
        'api.admin.servers.delete' => 'admin:servers.delete',
        'api.admin.servers.delete.force' => 'admin:servers.delete',

        'api.admin.servers.mounts' => 'admin:server-mounts.read',
        'api.admin.servers.mounts.store' => 'admin:server-mounts.create',
        'api.admin.servers.mounts.delete' => 'admin:server-mounts.delete',
        'api.admin.servers.databases' => 'admin:server-databases.read',
        'api.admin.servers.databases.view' => 'admin:server-databases.read',
        'api.admin.servers.databases.store' => 'admin:server-databases.create',
        'api.admin.servers.databases.rotate-password' => 'admin:server-databases.update',
        'api.admin.servers.databases.delete' => 'admin:server-databases.delete',
        'api.admin.servers.backups' => 'admin:server-backups.read',
        'api.admin.servers.backups.toggle' => 'admin:server-backups.update',

        'api.admin.database-hosts' => 'admin:database-hosts.read',
        'api.admin.database-hosts.view' => 'admin:database-hosts.read',
        'api.admin.database-hosts.databases' => 'admin:database-hosts.read',
        'api.admin.database-hosts.store' => 'admin:database-hosts.create',
        'api.admin.database-hosts.update' => 'admin:database-hosts.update',
        'api.admin.database-hosts.delete' => 'admin:database-hosts.delete',

        'api.admin.eggs' => 'admin:eggs.read',
        'api.admin.eggs.view' => 'admin:eggs.read',
        'api.admin.eggs.export' => 'admin:eggs.read',
        'api.admin.eggs.store' => 'admin:eggs.create',
        'api.admin.eggs.import' => 'admin:eggs.create',
        'api.admin.eggs.update' => 'admin:eggs.update',
        'api.admin.eggs.update-import' => 'admin:eggs.update',
        'api.admin.eggs.tags.sync' => 'admin:eggs.update',
        'api.admin.eggs.delete' => 'admin:eggs.delete',
        'api.admin.eggs.catalog' => 'admin:eggs.read',
        'api.admin.eggs.catalog.refresh' => 'admin:eggs.create',
        'api.admin.eggs.catalog.import' => 'admin:eggs.create',
        'api.admin.eggs.variables' => 'admin:egg-variables.read',
        'api.admin.eggs.variables.store' => 'admin:egg-variables.create',
        'api.admin.eggs.variables.reorder' => 'admin:egg-variables.update',
        'api.admin.eggs.variables.update' => 'admin:egg-variables.update',
        'api.admin.eggs.variables.delete' => 'admin:egg-variables.delete',
        'api.admin.eggs.script' => 'admin:eggs.read',
        'api.admin.eggs.script.update' => 'admin:eggs.update',

        'api.admin.tags' => 'admin:tags.read',
        'api.admin.tags.view' => 'admin:tags.read',
        'api.admin.tags.store' => 'admin:tags.create',
        'api.admin.tags.update' => 'admin:tags.update',
        'api.admin.tags.delete' => 'admin:tags.delete',

        'api.admin.settings' => 'admin:settings.read',
        'api.admin.settings.general' => 'admin:settings.update',
        'api.admin.settings.mail' => 'admin:settings.update',
        'api.admin.settings.mail.test' => 'admin:settings.update',

        'api.admin.version' => 'admin:version.read',
    ];

    /** @var list<string> */
    private const array ROOT_ONLY_ROUTES = [
        // Lookup by external ID returns a single user without route-model binding;
        // keep it root-only so a delegated reader cannot bypass the root-account
        // detail safeguard below.
        'api.admin.users.external',
        'api.admin.extensions',
        'api.admin.extensions.forms',
        'api.admin.extensions.settings',
        'api.admin.extensions.icon',
        'api.admin.extensions.install',
        'api.admin.extensions.settings.update',
        'api.admin.extensions.settings.file',
        'api.admin.extensions.settings.file.clear',
        'api.admin.extensions.enable',
        'api.admin.extensions.disable',
        'api.admin.extensions.delete',
        'api.admin.themes',
        'api.admin.themes.settings',
        'api.admin.themes.preview.stop',
        'api.admin.themes.activate',
        'api.admin.themes.preview',
        'api.admin.themes.settings.update',
        'api.admin.themes.settings.image',
        'api.admin.themes.settings.image.clear',
        'api.admin.nodes.configuration',
        'api.admin.nodes.deploy-token',
        'api.admin.settings.advanced',
        'api.admin.api-keys',
        'api.admin.api-keys.view',
        'api.admin.api-keys.store',
        'api.admin.api-keys.update',
        'api.admin.api-keys.delete',
    ];

    /** @return list<array{key: string, group: string, label: string, description: string, assignable: bool}> */
    public static function definitions(): array
    {
        return self::DEFINITIONS;
    }

    /** @return list<string> */
    public static function assignableKeys(): array
    {
        $keys = [];

        foreach (self::DEFINITIONS as $definition) {
            if ($definition['assignable']) {
                $keys[] = $definition['key'];
            }
        }

        return $keys;
    }

    public static function isAssignable(string $permission): bool
    {
        return in_array($permission, self::assignableKeys(), true);
    }

    /** Returns the one delegated permission that may access a named core route, or null to deny. */
    public static function permissionForRoute(?string $route): ?string
    {
        if ($route === null || in_array($route, self::ROOT_ONLY_ROUTES, true)) {
            return null;
        }

        return self::ROUTE_PERMISSIONS[$route] ?? null;
    }

    /** @return list<string> */
    public static function rootOnlyCapabilities(): array
    {
        return [
            'Role creation and assignment',
            'Extension and theme management',
            'Application API keys',
            'Node configuration and deploy tokens',
            'Advanced settings',
            'Root-administrator accounts',
            'Any unknown or newly added admin API route',
        ];
    }
}
