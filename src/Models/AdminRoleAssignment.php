<?php

declare(strict_types=1);

namespace AdminRoles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Pterodactyl\Models\User;

/** One optional delegated role per non-root Panel user. */
final class AdminRoleAssignment extends Model
{
    protected $table = 'admin_roles_assignments';

    /** @var list<string> */
    protected $fillable = ['user_id', 'role_id'];

    /** @return BelongsTo<AdminRole, $this> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(AdminRole::class, 'role_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
