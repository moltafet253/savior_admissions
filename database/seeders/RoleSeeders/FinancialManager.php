<?php

namespace Database\Seeders\RoleSeeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class FinancialManager extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $financialManagerRole = Role::create(['name' => 'Financial Manager']);
        $financialManagerRole->givePermissionTo([
            'finance-menu-access',
            'reservation-invoice-list',
            'reservation-invoice-create',
            'reservation-invoice-edit',
            'reservation-invoice-search',
            'reservation-invoice-show',
            'reservation-invoice-delete',
            'reservation-payment-details-show',
            'reservation-payment-status-change',
            'discounts-list',
            'discounts-edit',
            'discounts-change-status',
            'discounts-show',
            'tuition-list',
            'tuition-edit',
            'tuition-change-price',
            'tuition-show',
            'branch-info-menu-access',
            'interviews-menu-access',
            'interview-list',
            'interview-set',
            'interview-edit',
            'interview-delete',
            'interview-search',
            'interview-show',
        ]);
    }
}
