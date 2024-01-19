<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

//        $permissions = [
//            'create user', 'read user', 'update user', 'delete user',
//            'role-list',
//            'role-create',
//            'role-edit',
//            'role-delete',
//        ];
//        $permissions = collect($permissions)->map(function ($permission) {
//            return ['name' => $permission, 'guard_name' => 'web'];
//        });
//        Permission::insert($permissions->toArray());

        Permission::create(['name' => 'create-users']);
        Permission::create(['name' => 'show-user']);
        Permission::create(['name' => 'edit-users']);
        Permission::create(['name' => 'delete-users']);
        Permission::create(['name' => 'list-users']);
        Permission::create(['name' => 'search-user']);

        Permission::create(['name' => 'role-list']);
        Permission::create(['name' => 'role-create']);
        Permission::create(['name' => 'role-edit']);
        Permission::create(['name' => 'role-delete']);

        Permission::create(['name' => 'document-list']);
        Permission::create(['name' => 'document-create']);
        Permission::create(['name' => 'document-edit']);
        Permission::create(['name' => 'document-delete']);

        Permission::create(['name' => 'academic-year-list']);
        Permission::create(['name' => 'academic-year-create']);
        Permission::create(['name' => 'academic-year-edit']);
        Permission::create(['name' => 'academic-year-delete']);
        Permission::create(['name' => 'academic-year-search']);

        Permission::create(['name' => 'document-type-list']);
        Permission::create(['name' => 'document-type-create']);
        Permission::create(['name' => 'document-type-edit']);
        Permission::create(['name' => 'document-type-delete']);
        Permission::create(['name' => 'document-type-search']);

        Permission::create(['name' => 'education-type-list']);
        Permission::create(['name' => 'education-type-create']);
        Permission::create(['name' => 'education-type-edit']);
        Permission::create(['name' => 'education-type-delete']);
        Permission::create(['name' => 'education-type-search']);

        Permission::create(['name' => 'level-list']);
        Permission::create(['name' => 'level-create']);
        Permission::create(['name' => 'level-edit']);
        Permission::create(['name' => 'level-delete']);
        Permission::create(['name' => 'level-search']);

        Permission::create(['name' => 'school-list']);
        Permission::create(['name' => 'school-create']);
        Permission::create(['name' => 'school-edit']);
        Permission::create(['name' => 'school-delete']);
        Permission::create(['name' => 'school-search']);

//        Permission::create(['name' => 'catalogs-list']);
//        Permission::create(['name' => 'catalogs-create']);
//        Permission::create(['name' => 'catalogs-edit']);
//        Permission::create(['name' => 'catalogs-delete']);

        Permission::create(['name' => 'interview-list']);
        Permission::create(['name' => 'interview-set']);
        Permission::create(['name' => 'interview-edit']);
        Permission::create(['name' => 'interview-delete']);
        Permission::create(['name' => 'interview-search']);

        Permission::create(['name' => 'reservation-invoice-details']);
        Permission::create(['name' => 'reservation-invoice-list']);
        Permission::create(['name' => 'reservation-invoice-search']);
        Permission::create(['name' => 'reservation-invoice-add']);
        Permission::create(['name' => 'reservation-invoice-edit']);

        Permission::create(['name' => 'academic-year-class-list']);
        Permission::create(['name' => 'academic-year-class-create']);
        Permission::create(['name' => 'academic-year-class-edit']);
        Permission::create(['name' => 'academic-year-class-delete']);
        Permission::create(['name' => 'academic-year-class-search']);

        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $parentFatherRole = Role::create(['name' => 'Parent(Father)']);
        $parentMotherRole = Role::create(['name' => 'Parent(Mother)']);
        $studentRole = Role::create(['name' => 'Student']);
        $interviewerRole = Role::create(['name' => 'Interviewer']);
        $financialManagerRole = Role::create(['name' => 'Financial Manager']);
        $admissionsOfficerRole = Role::create(['name' => 'Admissions Officer']);
        $principalRole = Role::create(['name' => 'Principal']);

        $superAdminRole->givePermissionTo([
            'create-users',
            'show-user',
            'edit-users',
            'delete-users',
            'list-users',
            'search-user',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'document-list',
            'document-create',
            'document-edit',
            'document-delete',
            'academic-year-list',
            'academic-year-create',
            'academic-year-edit',
            'academic-year-delete',
            'academic-year-search',
            'document-type-list',
            'document-type-create',
            'document-type-edit',
            'document-type-delete',
            'document-type-search',
            'education-type-list',
            'education-type-create',
            'education-type-edit',
            'education-type-delete',
            'education-type-search',
            'level-list',
            'level-create',
            'level-edit',
            'level-delete',
            'level-search',
            'school-list',
            'school-create',
            'school-edit',
            'school-delete',
            'school-search',
            'interview-list',
            'interview-set',
            'interview-edit',
            'interview-delete',
            'interview-search',
            'academic-year-class-list',
            'academic-year-class-create',
            'academic-year-class-edit',
            'academic-year-class-delete',
            'academic-year-class-search',
        ]);

        $principalRole->givePermissionTo([
            'create-users',
            'show-user',
            'edit-users',
            'delete-users',
            'document-list',
            'document-create',
            'document-edit',
            'document-delete',
        ]);

        $parentFatherRole->givePermissionTo([
            'document-list',
            'document-create',
            'document-edit',
            'document-delete',
        ]);

        $parentMotherRole->givePermissionTo([
            'document-list',
            'document-create',
            'document-edit',
            'document-delete',
        ]);

        $studentRole->givePermissionTo([
            'document-list',
            'document-create',
            'document-edit',
            'document-delete',
        ]);

        $interviewerRole->givePermissionTo([
            'interview-list',
            'interview-set',
            'interview-search',
        ]);

        $financialManagerRole->givePermissionTo([
            'reservation-invoice-details',
            'reservation-invoice-list',
            'reservation-invoice-search',
            'reservation-invoice-add',
            'reservation-invoice-edit',
            'reservation-invoice-search',
        ]);

        $admissionsOfficerRole->givePermissionTo([
            'interview-list',
            'interview-set',
            'interview-edit',
            'interview-delete',
            'interview-search',
            'academic-year-class-list',
            'academic-year-class-create',
            'academic-year-class-edit',
            'academic-year-class-delete',
            'academic-year-class-search',
        ]);
    }
}
