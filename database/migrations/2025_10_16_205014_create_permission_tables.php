<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names', [
            'roles' => 'roles',
            'permissions' => 'permissions',
            'model_has_permissions' => 'model_has_permissions',
            'model_has_roles' => 'model_has_roles',
            'role_has_permissions' => 'role_has_permissions',
        ]);

        $columnNames = config('permission.column_names', [
            'role_pivot_key' => null,
            'permission_pivot_key' => null,
            'model_morph_key' => 'model_id',
            'team_foreign_key' => 'team_id',
        ]);

        $teams = config('permission.teams', false);
        $tableNames['model_has_permissions'] = $tableNames['model_has_permissions'] ?? 'model_has_permissions';
        $tableNames['model_has_roles'] = $tableNames['model_has_roles'] ?? 'model_has_roles';
        $tableNames['role_has_permissions'] = $tableNames['role_has_permissions'] ?? 'role_has_permissions';
        $columnNames['role_pivot_key'] = $columnNames['role_pivot_key'] ?? 'role_id';
        $columnNames['permission_pivot_key'] = $columnNames['permission_pivot_key'] ?? 'permission_id';
        $columnNames['model_morph_key'] = $columnNames['model_morph_key'] ?? 'model_id';
        $columnNames['team_foreign_key'] = $columnNames['team_foreign_key'] ?? 'team_id';

        // Crear tabla roles primero
        if (!Schema::hasTable($tableNames['roles'])) {
            Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
                $table->bigIncrements('id');
                if ($teams) {
                    $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                    $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
                }
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();

                if ($teams) {
                    $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
                } else {
                    $table->unique(['name', 'guard_name']);
                }
            });
        }

        if (!Schema::hasTable($tableNames['permissions'])) {
            Schema::create($tableNames['permissions'], function (Blueprint $table) use ($teams, $columnNames) {
                $table->bigIncrements('id');
                if ($teams) {
                    $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                    $table->index($columnNames['team_foreign_key'], 'permissions_team_foreign_key_index');
                }
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();

                if ($teams) {
                    $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
                } else {
                    $table->unique(['name', 'guard_name']);
                }
            });
        }

        if (!Schema::hasTable($tableNames['model_has_permissions'])) {
            Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
                $table->unsignedBigInteger($columnNames['permission_pivot_key']);

                $table->string('model_type');
                $table->unsignedBigInteger($columnNames['model_morph_key']);
                $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

                $table->foreign($columnNames['permission_pivot_key'])
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade');
                if ($teams) {
                    $table->unsignedBigInteger($columnNames['team_foreign_key']);
                    $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                    $table->primary(
                        [$columnNames['team_foreign_key'], $columnNames['permission_pivot_key'], $columnNames['model_morph_key'], 'model_type'],
                        'model_has_permissions_permission_model_type_primary'
                    );
                } else {
                    $table->primary(
                        [$columnNames['permission_pivot_key'], $columnNames['model_morph_key'], 'model_type'],
                        'model_has_permissions_permission_model_type_primary'
                    );
                }
            });
        }

        if (!Schema::hasTable($tableNames['model_has_roles'])) {
            Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
                $table->unsignedBigInteger($columnNames['role_pivot_key']);

                $table->string('model_type');
                $table->unsignedBigInteger($columnNames['model_morph_key']);
                $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

                $table->foreign($columnNames['role_pivot_key'])
                    ->references('id')
                    ->on($tableNames['roles'])
                    ->onDelete('cascade');
                if ($teams) {
                    $table->unsignedBigInteger($columnNames['team_foreign_key']);
                    $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                    $table->primary(
                        [$columnNames['team_foreign_key'], $columnNames['role_pivot_key'], $columnNames['model_morph_key'], 'model_type'],
                        'model_has_roles_role_model_type_primary'
                    );
                } else {
                    $table->primary(
                        [$columnNames['role_pivot_key'], $columnNames['model_morph_key'], 'model_type'],
                        'model_has_roles_role_model_type_primary'
                    );
                }
            });
        }

        if (!Schema::hasTable($tableNames['role_has_permissions'])) {
            Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
                $table->unsignedBigInteger($columnNames['permission_pivot_key']);
                $table->unsignedBigInteger($columnNames['role_pivot_key']);

                $table->foreign($columnNames['permission_pivot_key'])
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade');

                $table->foreign($columnNames['role_pivot_key'])
                    ->references('id')
                    ->on($tableNames['roles'])
                    ->onDelete('cascade');

                $table->primary([$columnNames['permission_pivot_key'], $columnNames['role_pivot_key']], 'role_has_permissions_permission_id_role_id_primary');
            });
        }

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names', [
            'roles' => 'roles',
            'permissions' => 'permissions',
            'model_has_permissions' => 'model_has_permissions',
            'model_has_roles' => 'model_has_roles',
            'role_has_permissions' => 'role_has_permissions',
        ]);

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['permissions']);
        Schema::dropIfExists($tableNames['roles']);
    }
};
