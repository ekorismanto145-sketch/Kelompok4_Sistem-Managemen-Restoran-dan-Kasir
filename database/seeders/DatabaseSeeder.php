<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'manager@pesanaja.com'],
            [
                'name' => 'Manajer PesanAja',
                'password' => Hash::make('Manager123'),
                'role' => Role::Manajer,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@pesanaja.com'],
            [
                'name' => 'Kasir 1',
                'password' => Hash::make('Kasir123'),
                'role' => Role::Kasir,
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@pesanaja.com'],
            [
                'name' => 'Customer Demo',
                'password' => Hash::make('Customer123'),
                'role' => Role::Customer,
            ]
        );

        $mie = Category::firstOrCreate(['name' => 'Mie']);
        $dimsum = Category::firstOrCreate(['name' => 'Dimsum']);
        $minuman = Category::firstOrCreate(['name' => 'Minuman']);

        $menus = [
            ['category_id' => $mie->id, 'name' => 'Mie Pedas Manis Nusantara', 'description' => 'Mie kenyal dengan bumbu manis pedas khas', 'price' => 12000, 'image_url' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=600&q=80', 'has_spicy_level' => true, 'is_available' => true],
            ['category_id' => $mie->id, 'name' => 'Mie Pedas Gurih Original', 'description' => 'Mie gurih dengan racikan cabai pilihan', 'price' => 12000, 'image_url' => 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?auto=format&fit=crop&w=600&q=80', 'has_spicy_level' => true, 'is_available' => true],
            ['category_id' => $mie->id, 'name' => 'Mie Ayam Klasik', 'description' => 'Mie gurih lembut tanpa cabai', 'price' => 11000, 'image_url' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80', 'has_spicy_level' => false, 'is_available' => true],
            ['category_id' => $dimsum->id, 'name' => 'Udang Keju Lumer', 'description' => 'Isi 3 pcs, keju mozzarella', 'price' => 10000, 'image_url' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=600&q=80', 'has_spicy_level' => false, 'is_available' => true],
            ['category_id' => $dimsum->id, 'name' => 'Siomay Ayam Kukus', 'description' => 'Isi 3 pcs, saus kacang/chili oil', 'price' => 9500, 'image_url' => 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?auto=format&fit=crop&w=600&q=80', 'has_spicy_level' => false, 'is_available' => true],
            ['category_id' => $dimsum->id, 'name' => 'Lumpia Udang Renyah', 'description' => 'Isi 3 pcs, kulit renyah', 'price' => 10000, 'image_url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80', 'has_spicy_level' => false, 'is_available' => true],
            ['category_id' => $minuman->id, 'name' => 'Es Cincau Segar', 'description' => 'Cincau hitam dengan gula aren', 'price' => 7000, 'image_url' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=600&q=80', 'has_spicy_level' => false, 'is_available' => true],
            ['category_id' => $minuman->id, 'name' => 'Es Jeruk Nipis', 'description' => 'Perasan jeruk nipis segar', 'price' => 6500, 'image_url' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80', 'has_spicy_level' => false, 'is_available' => false],
        ];

        foreach ($menus as $menu) {
            Menu::firstOrCreate(
                ['name' => $menu['name']],
                $menu
            );
        }
    }
}
