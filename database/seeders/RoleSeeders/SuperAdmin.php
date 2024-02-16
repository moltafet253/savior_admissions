<?php

namespace Database\Seeders\RoleSeeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo([
            'catalogs-menu-access',
            'branch-info-menu-access',
            'finance-menu-access',
            'users-menu-access',
            'create-users',
            'show-user',
            'edit-users',
            'delete-users',
            'list-users',
            'search-user',
            'access-user-role',
            'change-student-information',
            'change-principal-information',
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
            'reservation-invoice-list',
            'reservation-invoice-create',
            'reservation-invoice-edit',
            'reservation-invoice-search',
            'reservation-invoice-show',
            'reservation-invoice-delete',
            'reservation-payment-details-show',
            'application-timing-list',
            'application-timing-create',
            'application-timing-edit',
            'application-timing-delete',
            'application-timing-search',
            'application-timing-show',
            'applications-list',
            'new-application-reserve',
            'show-application-reserve',
            'edit-application-reserve',
            'remove-application',
            'change-status-of-application',
            'remove-application-from-reserve',
        ]);
    }
}
