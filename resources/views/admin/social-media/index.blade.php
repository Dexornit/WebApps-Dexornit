@extends('admin.layouts.admin')

@section('title', 'Social Media Management')
@section('page-title', 'Social Media')

@section('content')

<form method="POST" action="{{ route('admin.social-media.updateAll') }}" id="sm-form">
    @csrf

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <p style="color:#666; font-size:0.95rem;">
            Aktifkan platform dan isi link untuk ditampilkan di footer &amp; kontak website.
        </p>
        <button type="submit" class="admin-header__btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
            </svg>
            Simpan Semua
        </button>
    </div>

    {{-- Info banner --}}
    @if(session('success'))
        <div style="padding:14px 18px; background:var(--color-pastel-green); border:var(--border-width) solid var(--border-color); border-radius:10px; margin-bottom:20px; font-weight:600;">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:20px;">

        @foreach($platforms as $key => $meta)
            @php
                $record   = $socialMedia->get($key);
                $isActive = $record?->is_active ?? false;
                $url      = $record?->url ?? '';
            @endphp

            <div class="sm-card {{ $isActive ? 'sm-card--active' : '' }}" id="card-{{ $key }}"
                 style="background:var(--color-white); border:var(--border-width) solid var(--border-color); border-radius:14px; padding:20px; box-shadow:var(--shadow-brutal); transition:all 0.2s;">

                {{-- Header: icon + nama + toggle --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        {{-- Platform icon --}}
                        <div style="width:44px; height:44px; border-radius:12px; background:{{ $meta['color'] }}18; border:2px solid {{ $meta['color'] }}40; display:flex; align-items:center; justify-content:center; color:{{ $meta['color'] }}; flex-shrink:0;">
                            <div style="width:24px; height:24px;">
                                {!! $meta['svg'] !!}
                            </div>
                        </div>
                        <div>
                            <div style="font-family:var(--font-heading); font-weight:700; font-size:1rem;">{{ $meta['label'] }}</div>
                            <div style="font-size:0.75rem; color:{{ $isActive ? '#4caf50' : '#aaa' }}; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">
                                {{ $isActive ? '● Aktif' : '○ Non-aktif' }}
                            </div>
                        </div>
                    </div>

                    {{-- Toggle switch --}}
                    <label class="toggle-switch" style="cursor:pointer; display:flex; align-items:center; gap:8px; user-select:none;">
                        <input type="checkbox"
                               name="active[{{ $key }}]"
                               value="1"
                               {{ $isActive ? 'checked' : '' }}
                               onchange="handleToggle(this, '{{ $key }}')"
                               style="display:none;">
                        <div class="toggle-track" id="track-{{ $key }}"
                             style="width:48px; height:26px; border-radius:50px; border:2px solid var(--border-color); background:{{ $isActive ? $meta['color'] : '#ddd' }}; position:relative; transition:background 0.25s; box-shadow:2px 2px 0 var(--border-color);">
                            <div class="toggle-thumb" id="thumb-{{ $key }}"
                                 style="width:18px; height:18px; border-radius:50%; background:#fff; position:absolute; top:2px; left:{{ $isActive ? '24px' : '2px' }}; transition:left 0.25s; border:1px solid #ddd;">
                            </div>
                        </div>
                    </label>
                </div>

                {{-- URL input --}}
                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:600; color:#555; margin-bottom:6px; text-transform:uppercase; letter-spacing:.04em;">
                        Link / URL
                    </label>
                    <input type="url"
                           name="url[{{ $key }}]"
                           value="{{ $url }}"
                           placeholder="{{ $meta['placeholder'] }}"
                           style="width:100%; padding:10px 14px; border:2px solid var(--border-color); border-radius:8px; font-size:0.9rem; font-family:var(--font-body); outline:none; transition:border-color 0.2s, box-shadow 0.2s; background:var(--color-cream);"
                           onfocus="this.style.borderColor='{{ $meta['color'] }}'; this.style.boxShadow='3px 3px 0 {{ $meta['color'] }}40';"
                           onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='';">
                    @if($url)
                        <a href="{{ $url }}" target="_blank"
                           style="display:inline-flex; align-items:center; gap:4px; margin-top:8px; font-size:0.78rem; color:{{ $meta['color'] }}; text-decoration:none; font-weight:600;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            Buka link
                        </a>
                    @endif
                </div>
            </div>
        @endforeach

    </div>

    {{-- Sticky save button on mobile --}}
    <div style="margin-top:32px; display:flex; justify-content:flex-end;">
        <button type="submit" class="admin-header__btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
            </svg>
            Simpan Semua Perubahan
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
const platformColors = @json(collect($platforms)->map(fn($m) => $m['color']));

function handleToggle(checkbox, key) {
    const isChecked = checkbox.checked;
    const track = document.getElementById('track-' + key);
    const thumb = document.getElementById('thumb-' + key);
    const card  = document.getElementById('card-' + key);
    const color = platformColors[key] || '#999';

    // Update toggle visual
    track.style.background = isChecked ? color : '#ddd';
    thumb.style.left = isChecked ? '24px' : '2px';

    // Update card border
    card.style.borderColor = isChecked ? color : 'var(--color-black)';
    card.style.boxShadow   = isChecked ? `5px 5px 0 ${color}` : 'var(--shadow-brutal)';

    // Update status text
    const statusEl = card.querySelector('.sm-card div div:nth-child(2)');
    if (statusEl) {
        statusEl.textContent = isChecked ? '● Aktif' : '○ Non-aktif';
        statusEl.style.color = isChecked ? '#4caf50' : '#aaa';
    }
}

// Apply colors on load for active cards
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sm-card--active').forEach(card => {
        const key = card.id.replace('card-', '');
        const color = platformColors[key];
        if (color) {
            card.style.borderColor = color;
            card.style.boxShadow = `5px 5px 0 ${color}`;
        }
    });
});
</script>
@endpush
