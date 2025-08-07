<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем разрешения
        $permissions = [
            // Пользователи
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            
            // Дома
            'view_house',
            'view_any_house',
            'create_house',
            'update_house',
            'delete_house',
            'delete_any_house',
            
            // Категории
            'view_category',
            'view_any_category',
            'create_category',
            'update_category',
            'delete_category',
            'delete_any_category',
            
            // Страницы
            'view_page',
            'view_any_page',
            'create_page',
            'update_page',
            'delete_page',
            'delete_any_page',
            
            // Отзывы
            'view_review',
            'view_any_review',
            'create_review',
            'update_review',
            'delete_review',
            'delete_any_review',
            
            // Лиды
            'view_lead',
            'view_any_lead',
            'create_lead',
            'update_lead',
            'delete_lead',
            'delete_any_lead',
            
            // Настройки
            'view_settings',
            'update_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Создаем роли
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $editor = Role::firstOrCreate(['name' => 'editor']);

        // Назначаем разрешения ролям
        $superAdmin->givePermissionTo(Permission::all());
        
        $admin->givePermissionTo([
            'view_house', 'view_any_house', 'create_house', 'update_house', 'delete_house', 'delete_any_house',
            'view_category', 'view_any_category', 'create_category', 'update_category', 'delete_category', 'delete_any_category',
            'view_page', 'view_any_page', 'create_page', 'update_page', 'delete_page', 'delete_any_page',
            'view_review', 'view_any_review', 'create_review', 'update_review', 'delete_review', 'delete_any_review',
            'view_lead', 'view_any_lead', 'create_lead', 'update_lead', 'delete_lead', 'delete_any_lead',
            'view_settings', 'update_settings',
        ]);
        
        $editor->givePermissionTo([
            'view_house', 'view_any_house', 'create_house', 'update_house',
            'view_category', 'view_any_category', 'create_category', 'update_category',
            'view_page', 'view_any_page', 'create_page', 'update_page',
            'view_review', 'view_any_review', 'create_review', 'update_review',
            'view_lead', 'view_any_lead',
        ]);

        // Создаем супер-администратора
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@woodzavod.ru'],
            [
                'name' => 'Супер Администратор',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $superAdminUser->assignRole('super_admin');
        
        $this->command->info('Роли и разрешения созданы успешно!');
        $this->command->info('Супер-администратор: admin@woodzavod.ru / password123');
    }
}
