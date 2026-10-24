import './styles.css';
import { definePterodactylExtension } from '@pterodactyl/sdk';
import UserRoleField from './UserRoleField';

export default definePterodactylExtension({
    setup({ forms, screens }) {
        // Role definitions and assignments are intentionally root-only. The runtime
        // predicate prevents the navigation item from appearing for delegated admins;
        // the backend repeats the check and is the security boundary.
        screens.register('roles', () => import('./RolesScreen'), {
            visible: ({ user }) => user.rootAdmin,
        });
        forms.extend('admin.user', UserRoleField);
    },
});
