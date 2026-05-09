<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product 1: Netflix Premium
        $netflix = Product::create([
            'name' => 'Netflix Premium',
            'emoji' => '🎬',
            'category' => 'streaming',
            'short_description' => 'Akun Netflix Premium UHD 4K, sharing atau private.',
            'full_description' => 'Nikmati streaming film dan series favorit Anda dengan kualitas UHD 4K. Akun Netflix Premium dengan garansi penuh. Tersedia pilihan sharing atau private account sesuai kebutuhan Anda.',
            'warranty' => 'Garansi 30 hari full. Jika ada masalah akan diganti dengan akun baru.',
            'terms_conditions' => 'Dilarang mengganti password atau profile. Gunakan sesuai slot yang diberikan.',
            'status' => true,
        ]);

        ProductVariant::create([
            'product_id' => $netflix->id,
            'variant_name' => '1 Bulan Sharing',
            'price' => 35000,
            'wholesale_price' => 30000,
            'description' => 'Sharing account 1 bulan (1 slot profile)',
            'stock' => 10,
        ]);

        ProductVariant::create([
            'product_id' => $netflix->id,
            'variant_name' => '3 Bulan Sharing',
            'price' => 95000,
            'wholesale_price' => 85000,
            'description' => 'Sharing account 3 bulan (1 slot profile)',
            'stock' => 5,
        ]);

        ProductVariant::create([
            'product_id' => $netflix->id,
            'variant_name' => 'Private Account',
            'price' => 50000,
            'wholesale_price' => null,
            'description' => 'Private account 1 bulan (full akses)',
            'stock' => 3,
        ]);

        // Product 2: Spotify Premium
        $spotify = Product::create([
            'name' => 'Spotify Premium',
            'emoji' => '🎵',
            'category' => 'streaming',
            'short_description' => 'Spotify Premium Individual & Family plan, garansi full.',
            'full_description' => 'Dengarkan musik tanpa iklan dengan kualitas audio terbaik. Spotify Premium dengan akses unlimited skip dan download offline. Tersedia paket Individual dan Family.',
            'warranty' => 'Garansi 30 hari. Penggantian gratis jika ada masalah.',
            'terms_conditions' => 'Jangan ubah email atau password. Gunakan sesuai ketentuan.',
            'status' => true,
        ]);

        ProductVariant::create([
            'product_id' => $spotify->id,
            'variant_name' => 'Individual 1 Bulan',
            'price' => 15000,
            'wholesale_price' => 12000,
            'description' => 'Spotify Premium Individual 1 bulan',
            'stock' => 20,
        ]);

        ProductVariant::create([
            'product_id' => $spotify->id,
            'variant_name' => 'Family 1 Bulan',
            'price' => 25000,
            'wholesale_price' => 22000,
            'description' => 'Spotify Premium Family 1 bulan (1 slot)',
            'stock' => 15,
        ]);

        // Product 3: YouTube Premium
        $youtube = Product::create([
            'name' => 'YouTube Premium',
            'emoji' => '▶️',
            'category' => 'streaming',
            'short_description' => 'YouTube Premium tanpa iklan + YouTube Music.',
            'full_description' => 'Nikmati YouTube tanpa iklan, background play, dan akses YouTube Music Premium. Cocok untuk yang sering nonton YouTube dan dengerin musik.',
            'warranty' => 'Garansi 30 hari full replacement.',
            'terms_conditions' => 'Tidak boleh mengganti data akun. Gunakan dengan bijak.',
            'status' => true,
        ]);

        ProductVariant::create([
            'product_id' => $youtube->id,
            'variant_name' => '1 Bulan',
            'price' => 20000,
            'wholesale_price' => 18000,
            'description' => 'YouTube Premium 1 bulan',
            'stock' => 12,
        ]);

        ProductVariant::create([
            'product_id' => $youtube->id,
            'variant_name' => '3 Bulan',
            'price' => 55000,
            'wholesale_price' => 50000,
            'description' => 'YouTube Premium 3 bulan',
            'stock' => 8,
        ]);

        // Product 4: Canva Pro
        $canva = Product::create([
            'name' => 'Canva Pro',
            'emoji' => '🎨',
            'category' => 'tools',
            'short_description' => 'Canva Pro team invite, akses semua fitur premium.',
            'full_description' => 'Akses penuh ke Canva Pro dengan semua template premium, background remover, brand kit, dan fitur kolaborasi tim. Cocok untuk content creator dan designer.',
            'warranty' => 'Garansi 30 hari. Jika keluar dari team akan di-invite ulang.',
            'terms_conditions' => 'Jangan keluar dari team. Gunakan untuk keperluan pribadi atau bisnis.',
            'status' => true,
        ]);

        ProductVariant::create([
            'product_id' => $canva->id,
            'variant_name' => '1 Bulan',
            'price' => 25000,
            'wholesale_price' => 22000,
            'description' => 'Canva Pro team invite 1 bulan',
            'stock' => 25,
        ]);

        ProductVariant::create([
            'product_id' => $canva->id,
            'variant_name' => '3 Bulan',
            'price' => 70000,
            'wholesale_price' => 65000,
            'description' => 'Canva Pro team invite 3 bulan',
            'stock' => 10,
        ]);

        // Product 5: ChatGPT Plus
        $chatgpt = Product::create([
            'name' => 'ChatGPT Plus',
            'emoji' => '🤖',
            'category' => 'tools',
            'short_description' => 'Akun ChatGPT Plus dengan akses GPT-4 & plugins.',
            'full_description' => 'Akses ChatGPT Plus dengan GPT-4, response lebih cepat, dan akses ke plugins. Cocok untuk produktivitas, coding, dan research.',
            'warranty' => 'Garansi 30 hari full. Penggantian akun jika ada masalah.',
            'terms_conditions' => 'Jangan ubah password atau email. Gunakan sesuai fair use policy.',
            'status' => true,
        ]);

        ProductVariant::create([
            'product_id' => $chatgpt->id,
            'variant_name' => '1 Bulan',
            'price' => 85000,
            'wholesale_price' => 80000,
            'description' => 'ChatGPT Plus 1 bulan',
            'stock' => 5,
        ]);

        // Product 6: Top-Up ML
        $ml = Product::create([
            'name' => 'Top-Up ML',
            'emoji' => '🎮',
            'category' => 'gaming',
            'short_description' => 'Top-up diamond Mobile Legends, proses cepat & aman.',
            'full_description' => 'Top-up diamond Mobile Legends dengan harga termurah dan proses instan. Cukup berikan ID dan Server, diamond langsung masuk ke akun Anda.',
            'warranty' => 'Garansi uang kembali jika diamond tidak masuk dalam 1x24 jam.',
            'terms_conditions' => 'Pastikan ID dan Server benar. Kesalahan input bukan tanggung jawab kami.',
            'status' => true,
        ]);

        ProductVariant::create([
            'product_id' => $ml->id,
            'variant_name' => '100 Diamond',
            'price' => 10000,
            'wholesale_price' => 9000,
            'description' => '100 Diamond Mobile Legends',
            'stock' => null,
        ]);

        ProductVariant::create([
            'product_id' => $ml->id,
            'variant_name' => '500 Diamond',
            'price' => 50000,
            'wholesale_price' => 47000,
            'description' => '500 Diamond Mobile Legends',
            'stock' => null,
        ]);

        ProductVariant::create([
            'product_id' => $ml->id,
            'variant_name' => '1000 Diamond',
            'price' => 95000,
            'wholesale_price' => 90000,
            'description' => '1000 Diamond Mobile Legends',
            'stock' => null,
        ]);

        // Product 7: Valorant VP
        $valorant = Product::create([
            'name' => 'Valorant VP',
            'emoji' => '🏰',
            'category' => 'gaming',
            'short_description' => 'Top-up Valorant Points dengan harga termurah.',
            'full_description' => 'Top-up Valorant Points (VP) untuk beli skin, battle pass, dan item premium lainnya. Proses cepat dan aman dengan harga terjangkau.',
            'warranty' => 'Garansi VP masuk atau uang kembali 100%.',
            'terms_conditions' => 'Berikan username dan tag dengan benar. Proses 5-30 menit.',
            'status' => true,
        ]);

        ProductVariant::create([
            'product_id' => $valorant->id,
            'variant_name' => '1000 VP',
            'price' => 15000,
            'wholesale_price' => 13000,
            'description' => '1000 Valorant Points',
            'stock' => null,
        ]);

        ProductVariant::create([
            'product_id' => $valorant->id,
            'variant_name' => '2000 VP',
            'price' => 28000,
            'wholesale_price' => 26000,
            'description' => '2000 Valorant Points',
            'stock' => null,
        ]);

        // Product 8: Adobe Creative Cloud
        $adobe = Product::create([
            'name' => 'Adobe CC',
            'emoji' => '☁️',
            'category' => 'tools',
            'short_description' => 'Adobe Creative Cloud All Apps, lisensi full 1 tahun.',
            'full_description' => 'Akses semua aplikasi Adobe Creative Cloud termasuk Photoshop, Illustrator, Premiere Pro, After Effects, dan lainnya. Lisensi resmi dengan cloud storage 100GB.',
            'warranty' => 'Garansi 1 tahun full. Jika ada masalah akan diganti dengan lisensi baru.',
            'terms_conditions' => 'Akun pribadi, jangan share ke orang lain. Gunakan untuk 1 device saja.',
            'status' => true,
        ]);

        ProductVariant::create([
            'product_id' => $adobe->id,
            'variant_name' => '1 Tahun',
            'price' => 150000,
            'wholesale_price' => 140000,
            'description' => 'Adobe Creative Cloud All Apps 1 tahun',
            'stock' => 3,
        ]);
    }
}
