<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_roles_roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('slug', 80)->unique();
            $table->string('description', 255)->nullable();
            $table->json('permissions');
            $table->timestamps();
        });

        Schema::create('admin_roles_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('admin_roles_roles')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_roles_assignments');
        Schema::dropIfExists('admin_roles_roles');
    }
};
