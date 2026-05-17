<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    /**
     * Halaman utama social media management.
     * Tampilkan semua platform preset; yang belum ada di DB dibuat otomatis (inactive, url null).
     */
    public function index()
    {
        $platforms = SocialMedia::platforms();

        // Seed platform yang belum ada ke DB agar bisa ditampilkan
        foreach (array_keys($platforms) as $key) {
            SocialMedia::firstOrCreate(['platform' => $key], ['url' => null, 'is_active' => false]);
        }

        $socialMedia = SocialMedia::whereIn('platform', array_keys($platforms))->get()->keyBy('platform');

        return view('admin.social-media.index', compact('socialMedia', 'platforms'));
    }

    /**
     * Simpan semua perubahan (satu form besar untuk semua platform).
     */
    public function updateAll(Request $request)
    {
        $platforms = SocialMedia::platforms();

        foreach (array_keys($platforms) as $key) {
            $url      = $request->input("url.{$key}");
            $isActive = $request->boolean("active.{$key}");

            // Jika aktif tapi URL kosong, paksa non-aktif
            if ($isActive && empty($url)) {
                $isActive = false;
            }

            SocialMedia::updateOrCreate(
                ['platform' => $key],
                ['url' => $url ?: null, 'is_active' => $isActive]
            );
        }

        return redirect()->route('admin.social-media.index')
            ->with('success', 'Social media berhasil disimpan!');
    }

    /**
     * Toggle aktif/non-aktif satu platform via AJAX.
     */
    public function toggle(Request $request, string $platform)
    {
        $social = SocialMedia::where('platform', $platform)->firstOrFail();

        // Tidak bisa aktifkan jika URL belum diisi
        if (!$social->url && !$social->is_active) {
            return response()->json(['success' => false, 'message' => 'Isi URL terlebih dahulu.'], 422);
        }

        $social->update(['is_active' => !$social->is_active]);

        return response()->json(['success' => true, 'is_active' => $social->is_active]);
    }
}
