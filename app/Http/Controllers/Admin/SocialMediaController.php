<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function index()
    {
        $socialMedia = SocialMedia::orderBy('order')->get();
        return view('admin.social-media.index', compact('socialMedia'));
    }

    public function create()
    {
        return view('admin.social-media.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'icon' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? SocialMedia::max('order') + 1;

        SocialMedia::create($validated);

        return redirect()->route('admin.social-media.index')
            ->with('success', 'Social media berhasil ditambahkan!');
    }

    public function edit(SocialMedia $socialMedia)
    {
        return view('admin.social-media.edit', compact('socialMedia'));
    }

    public function update(Request $request, SocialMedia $socialMedia)
    {
        $validated = $request->validate([
            'icon' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $socialMedia->update($validated);

        return redirect()->route('admin.social-media.index')
            ->with('success', 'Social media berhasil diupdate!');
    }

    public function destroy(SocialMedia $socialMedia)
    {
        $socialMedia->delete();

        return redirect()->route('admin.social-media.index')
            ->with('success', 'Social media berhasil dihapus!');
    }
}
