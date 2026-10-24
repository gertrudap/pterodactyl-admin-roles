import { describe, expect, it } from 'vitest';
import { setRolePermission, setRolePermissionGroup, slugifyRole } from '../src/client/roleHelpers';

describe('role editor helpers', () => {
    it('creates a stable, Panel-safe role identifier', () => {
        expect(slugifyRole('  Support & Moderation Team!  ')).toBe('support-moderation-team');
        expect(slugifyRole(`Role ${'x'.repeat(100)}`)).toHaveLength(80);
    });

    it('adds and removes exactly one permission without duplicates', () => {
        expect(setRolePermission(['admin:users.read'], 'admin:users.update', true)).toEqual([
            'admin:users.read',
            'admin:users.update',
        ]);
        expect(setRolePermission(['admin:users.read', 'admin:users.read'], 'admin:users.read', false)).toEqual([]);
    });

    it('toggles every permission in a group while preserving other groups', () => {
        expect(
            setRolePermissionGroup(
                ['admin:servers.read'],
                ['admin:users.read', 'admin:users.update'],
                true
            )
        ).toEqual(['admin:servers.read', 'admin:users.read', 'admin:users.update']);
        expect(
            setRolePermissionGroup(
                ['admin:servers.read', 'admin:users.read', 'admin:users.update'],
                ['admin:users.read', 'admin:users.update'],
                false
            )
        ).toEqual(['admin:servers.read']);
    });
});
