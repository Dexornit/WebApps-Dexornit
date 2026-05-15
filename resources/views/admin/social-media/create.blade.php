@extends('admin.layouts.admin')

@section('title', 'Tambah Social Media')
@section('page-title', 'Tambah Social Media')

@section('content')

<div style="max-width: 800px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.social-media.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: var(--color-coral); text-decoration: none; font-weight: 600; font-size: 0.9rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 32px; box-shadow: var(--shadow-brutal);">
        <form action="{{ route('admin.social-media.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-family: var(--font-heading); font-weight: 600; margin-bottom: 8px; font-size: 0.95rem;">
                    Icon SVG <span style="color: var(--color-coral);">*</span>
                </label>
                <textarea 
                    name="icon" 
                    rows="4" 
                    placeholder='<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>'
                    style="width: 100%; padding: 12px 16px; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: monospace; font-size: 0.85rem; resize: vertical;"
                    required>{{ old('icon') }}</textarea>
                <small style="color: #666; font-size: 0.85rem;">Paste SVG code dari icon pack yang digunakan (lihat contoh di footer)</small>
                @error('icon')
                    <div style="color: #dc2626; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-family: var(--font-heading); font-weight: 600; margin-bottom: 8px; font-size: 0.95rem;">
                    Link URL <span style="color: var(--color-coral);">*</span>
                </label>
                <input 
                    type="url" 
                    name="link" 
                    value="{{ old('link') }}" 
                    placeholder="https://instagram.com/dexornit.store"
                    style="width: 100%; padding: 12px 16px; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-size: 0.95rem;"
                    required>
                @error('link')
                    <div style="color: #dc2626; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-family: var(--font-heading); font-weight: 600; margin-bottom: 8px; font-size: 0.95rem;">
                    Order (Urutan Tampil)
                </label>
                <input 
                    type="number" 
                    name="order" 
                    value="{{ old('order', 0) }}" 
                    min="0"
                    style="width: 100%; padding: 12px 16px; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-size: 0.95rem;">
                <small style="color: #666; font-size: 0.85rem;">Semakin kecil angka, semakin awal ditampilkan</small>
                @error('order')
                    <div style="color: #dc2626; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 32px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1" 
                        {{ old('is_active', true) ? 'checked' : '' }}
                        style="width: 20px; height: 20px; cursor: pointer;">
                    <span style="font-weight: 600; font-size: 0.95rem;">Aktifkan social media ini</span>
                </label>
            </div>

            <div style="display: flex; gap: 12px;">
                <button 
                    type="submit" 
                    style="padding: 12px 28px; background: var(--color-coral); color: white; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; font-size: 0.95rem; cursor: pointer; box-shadow: var(--shadow-brutal); transition: all 0.2s;">
                    Simpan
                </button>
                <a 
                    href="{{ route('admin.social-media.index') }}" 
                    style="padding: 12px 28px; background: var(--color-white); color: var(--color-black); border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; font-size: 0.95rem; text-decoration: none; display: inline-block; box-shadow: var(--shadow-brutal); transition: all 0.2s;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
