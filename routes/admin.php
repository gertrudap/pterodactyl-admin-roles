<?php

declare(strict_types=1);

use AdminRoles\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('/roles')->name('roles.')->group(function (): void {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::post('/', [RoleController::class, 'store'])->name('store');
    Route::get('/{role}', [RoleController::class, 'show'])->name('show');
    Route::put('/{role}', [RoleController::class, 'update'])->name('update');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('delete');
});
