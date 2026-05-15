@extends('admin.layouts.admin')

@section('title', 'Social Media Management')
@section('page-title', 'Social Media')

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <p style="color: #666; font-size: 0.95rem;">Kelola link social media yang ditampilkan di footer website</p>
    </div>
    <a href="{{ route('admin.social-media.create') }}" class="admin-header__btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Social Media
    </a>
</div>

<div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-brutal);">
    @if($socialMedia->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color);">
                        <th style="text-align: left; padding: 12px; font-family: var(--font-heading); font-weight: 700; text-transform: uppercase; font-size: 0.8rem; color: #777;">Order</th>
                        <th style="text-align: left; padding: 12px; font-family: var(--font-heading); font-weight: 700; text-transform: uppercase; font-size: 0.8rem; color: #777;">Icon</th>
                        <th style="text-align: left; padding: 12px; font-family: var(--font-heading); font-weight: 700; text-transform: uppercase; font-size: 0.8rem; color: #777;">Link</th>
                        <th style="text-align: left; padding: 12px; font-family: var(--font-heading); font-weight: 700; text-transform: uppercase; font-size: 0.8rem; color: #777;">Status</th>
                        <th style="text-align: right; padding: 12px; font-family: var(--font-heading); font-weight: 700; text-transform: uppercase; font-size: 0.8rem; color: #777;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($socialMedia as $social)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 14px;">
                            <span style="font-weight: 700; color: var(--color-coral);">{{ $social->order }}</span>
                        </td>
                        <td style="padding: 14px;">
                            <div style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 8px;">
                                {!! $social->icon !!}
                            </div>
                        </td>
                        <td style="padding: 14px;">
                            <a href="{{ $social->link }}" target="_blank" style="color: var(--color-coral); text-decoration: none; font-weight: 500;">
                                {{ Str::limit($social->link, 50) }}
                            </a>
                        </td>
                        <td style="padding: 14px;">
                            @if($social->is_active)
                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: var(--color-pastel-green); border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                                    <span style="width: 6px; height: 6px; background: #4caf50; border-radius: 50%;"></span>
                                    Active
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                                    <span style="width: 6px; height: 6px; background: #f44336; border-radius: 50%;"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.social-media.edit', $social->id) }}" style="display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 7px; font-size: 0.82rem; font-weight: 600; text-decoration: none; color: var(--color-black); box-shadow: 2px 2px 0 var(--color-black); transition: all 0.2s;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('admin.social-media.destroy', $social->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus social media ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 7px; font-size: 0.82rem; font-weight: 600; color: var(--color-black); box-shadow: 2px 2px 0 var(--color-black); transition: all 0.2s; cursor: pointer;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; color: #888;">
            <div style="font-size: 4rem; margin-bottom: 16px;">📱</div>
            <p style="font-size: 1.2rem; font-weight: 600; margin-bottom: 8px;">Belum ada social media</p>
            <p style="font-size: 0.95rem; margin-bottom: 24px;">Tambahkan link social media untuk ditampilkan di footer website</p>
            <a href="{{ route('admin.social-media.create') }}" class="admin-header__btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tambah Social Media
            </a>
        </div>
    @endif
</div>

@endsection
