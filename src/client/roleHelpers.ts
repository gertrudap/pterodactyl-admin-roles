/** Normalizes the stable identifier shown in the role editor. */
export const slugifyRole = (value: string): string =>
    value
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .slice(0, 80);

/** Returns a sorted, duplicate-free permission selection after toggling one key. */
export const setRolePermission = (selected: readonly string[], key: string, checked: boolean): string[] => {
    const next = new Set(selected);
    checked ? next.add(key) : next.delete(key);

    return [...next].sort();
};

/** Returns a sorted, duplicate-free permission selection after toggling a group. */
export const setRolePermissionGroup = (selected: readonly string[], keys: readonly string[], checked: boolean): string[] => {
    const next = new Set(selected);
    for (const key of keys) {
        checked ? next.add(key) : next.delete(key);
    }

    return [...next].sort();
};
