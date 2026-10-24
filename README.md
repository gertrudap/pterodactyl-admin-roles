# Admin Roles for Pterodactyl 2

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Pterodactyl](https://img.shields.io/badge/Pterodactyl-Panel%202-7c3aed)](https://pterodactyl.io/)
[![Status: Experimental](https://img.shields.io/badge/status-experimental-f59e0b)](#compatibility)

**Admin Roles** is an unofficial, least-privilege role extension for Pterodactyl Panel 2. It gives non-root staff only the individual administrative capabilities they need while the built-in root **Administrator** always remains the highest authority.

> [!WARNING]
> Pterodactyl 2 currently protects the administration area behind `root_admin`. This extension therefore requires a small, version-pinned Panel patch in addition to the extension package. Do not skip the patch or apply it to a different Panel revision.

## Highlights

- Create named delegated administrator roles from **Admin → Roles**.
- Select every current Pterodactyl 2 `admin:*` capability individually.
- Assign one optional role to a non-root account through the native **Admin → Users** form.
- Revoke access immediately by removing an assignment or deleting a role.
- Deny unknown future admin routes by default, preventing a Panel update from silently broadening access.
- Enforce permissions on the server; hiding a page is never treated as the security boundary.

## Permission boundary

The current Panel capability catalog contains 63 administrative permission keys. This release makes 53 of them independently assignable. The remaining operations stay root-only because they could elevate a delegated account or expose sensitive Panel control data.

| Access | Examples |
| --- | --- |
| Delegated role | Users, servers, nodes, allocations, locations, mounts, eggs, tags, database hosts, backups, activity, ordinary settings, and version information. |
| Root Administrator only | Role creation and assignment, extensions, themes, application API keys, node configuration and deploy tokens, advanced settings, root accounts, and unknown future admin routes. |

Roles never set `root_admin`. A delegated user also cannot inspect, modify, delete, or promote a root account.

## Compatibility

This release is pinned to Pterodactyl Panel `2.0-develop` commit:

```text
9845fc65cfd26def7affd57bc2afcb1913e9a070
```

It was checked against the Panel's current `2.0-develop` head when this release was built. Panel 2 is still evolving: after every update, use `git apply --check`, review the upstream admin routes and permission enum, and update the route catalog before re-enabling delegated roles.

## Installation

### 1. Download the release assets

From [Releases](../../releases), download both files from the same release:

- `admin-roles-1.0.0-final.pteroext`
- `pterodactyl-2.0-develop-9845fc65-admin-roles.patch`

### 2. Patch the Panel

Back up the Panel database and source tree first. Then, on the Panel server:

```bash
cd /var/www/pterodactyl

git rev-parse HEAD
# Must print: 9845fc65cfd26def7affd57bc2afcb1913e9a070

git apply --check /path/to/pterodactyl-2.0-develop-9845fc65-admin-roles.patch
git apply /path/to/pterodactyl-2.0-develop-9845fc65-admin-roles.patch

npm ci
npm run build
php artisan optimize:clear
```

If the revision or `git apply --check` result differs, stop and rebase the patch for that exact Panel revision. Never force it through.

### 3. Install the extension

Sign in as the built-in root Administrator, then:

1. Open **Admin → Extensions**.
2. Click **Install** and select `admin-roles-1.0.0-final.pteroext`.
3. Check **Enable after install**, then select **Install**.

The Panel installer runs the extension migration when it enables the package. The `.pteroext` file is already a ZIP-format package; upload it directly and do not re-zip the source folder.

### 4. Create the first delegated role

1. Open **Admin → Roles**.
2. Create a role with only the required permissions. Give the first test role **View version** so its Panel overview can load.
3. Open **Admin → Users**, edit a non-root user, and choose the role in **Delegated admin role**.
4. Sign in as that user and test both an allowed action and a denied action.

## Upgrade checklist

1. Disable the extension before upgrading the Panel.
2. Inspect upstream `app/Enum/Permissions.php` and `routes/api-admin.php` at the new revision.
3. Update `src/Services/PermissionCatalog.php` for new or changed permissions and routes.
4. Rebase and test the integration patch. An `apply --check` failure is protective, not something to bypass.
5. Rebuild the Panel frontend, enable the extension, and repeat the first-user check.

## Development

Place this repository at `extensions/admin-roles` inside a compatible Panel checkout. Its SDK dependency is intentionally relative to that location.

```bash
cd /path/to/pterodactyl/extensions/admin-roles
npm install
npm run build
```

Then apply [`patches/pterodactyl-2.0-develop-9845fc65.patch`](patches/pterodactyl-2.0-develop-9845fc65.patch) from the Panel root and install the extension with the normal Pterodactyl extension command or the Extensions screen.

## Verification

This release passed:

- TypeScript type checking;
- three unit tests for permission-editor helpers;
- a production Vite build;
- `git apply --check --whitespace=error` against the pinned Panel commit;
- release-archive validation, including exclusion of dependencies, tests, and source maps.

PHP runtime and live Panel checks must still be performed on the target Pterodactyl installation before staff access is granted.

## License

This project is licensed under the [MIT License](LICENSE).

