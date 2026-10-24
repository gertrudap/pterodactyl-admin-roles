<?php

declare(strict_types=1);

namespace AdminRoles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A delegated Panel-administrator role.
 *
 * Root administrators are deliberately not represented by this model. The Panel's
 * root_admin flag remains the immutable highest authority and bypasses this table.
 */
final class AdminRole extends Model
{
    protected $table = 'admin_roles_roles';

    /** @var list<string> */
    protected $fillable = ['name', 'slug', 'description', 'permissions'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['permissions' => 'array'];
    }

    /** @return HasMany<AdminRoleAssignment, $this> */
    public function assignments(): HasMany
    {
        return $this->hasMany(AdminRoleAssignment::class, 'role_id');
    }
}
