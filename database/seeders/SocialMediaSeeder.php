<?php

namespace Database\Seeders;

use App\Models\SocialMedia;
use Illuminate\Database\Seeder;

class SocialMediaSeeder extends Seeder
{
    public function run(): void
    {
        // Insert semua platform preset dengan status non-aktif
        // Admin tinggal isi URL dan aktifkan yang diinginkan
        foreach (array_keys(SocialMedia::platforms()) as $platform) {
            SocialMedia::firstOrCreate(
                ['platform' => $platform],
                ['url' => null, 'is_active' => false]
            );
        }
    }
}
