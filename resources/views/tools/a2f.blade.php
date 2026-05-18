@extends('layouts.app')
@section('title', 'A2F Authenticator — Dexornit Tools')
@section('content')
<style>
:root { --green: #22c55e; --yellow: #f59e0b; --red: #ef4444; }
.a2f-wrap { background: var(--color-cream); min-height: 80vh; padding: 48px 0; }
.a2f-back { display:inline-flex; align-items:center; gap:8px; text-decoration:none; color:#666; font-size:0.9rem; font-weight:600; margin-bottom:32px; padding:8px 16px; background:#fff; border:2px solid var(--color-black); border-radius:8px; box-shadow:3px 3px 0 var(--color-black); transition:all .2s; }
.a2f-back:hover { transform:translate(-2px,-2px); box-shadow:5px 5px 0 var(--color-black); }
.a2f-header { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:36px; }
.a2f-title { font-family:var(--font-heading); font-size:clamp(1.6rem,4vw,2.4rem); font-weight:800; }
.a2f-title span { color:var(--color-coral); }
.a2f-subtitle { color:#666; font-size:0.95rem; margin-top:6px; }
.btn-add { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; background:var(--color-coral); color:#fff; border:3px solid var(--color-black); border-radius:10px; font-family:var(--font-heading); font-weight:700; font-size:0.95rem; cursor:pointer; box-shadow:4px 4px 0 var(--color-black); transition:all .2s; white-space:nowrap; }
.btn-add:hover { transform:translate(-2px,-2px); box-shadow:6px 6px 0 var(--color-black); }

/* Cards Grid */
.a2f-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(300px,1fr)); gap:20px; }
.a2f-card { background:#fff; border:3px solid var(--color-black); border-radius:16px; padding:24px; box-shadow:5px 5px 0 var(--color-black); position:relative; }
.a2f-card__header { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
.a2f-card__avatar { width:40px; height:40px; border-radius:10px; background:var(--color-pastel-yellow); border:2px solid var(--color-black); display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.a2f-card__info { flex:1; min-width:0; }
.a2f-card__name { font-family:var(--font-heading); font-weight:700; font-size:0.95rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.a2f-card__issuer { font-size:0.78rem; color:#888; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.a2f-card__del { background:none; border:none; cursor:pointer; color:#ccc; padding:4px; transition:color .2s; }
.a2f-card__del:hover { color:var(--red); }

/* Timer Ring */
.a2f-timer-wrap { display:flex; align-items:center; justify-content:space-between; gap:16px; }
.a2f-ring-box { position:relative; width:72px; height:72px; flex-shrink:0; }
.a2f-ring-bg { fill:none; stroke:#eee; stroke-width:5; }
.a2f-ring-progress { fill:none; stroke-width:5; stroke-linecap:round; transform:rotate(-90deg); transform-origin:center; transition:stroke-dashoffset .9s linear, stroke .5s; }
.a2f-ring-text { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; color:#555; }

/* Code display */
.a2f-code-box { flex:1; }
.a2f-code { font-family:'Space Grotesk', monospace; font-size:2rem; font-weight:800; letter-spacing:.15em; color:var(--color-black); line-height:1; }
.a2f-copy-btn { display:inline-flex; align-items:center; gap:6px; margin-top:10px; padding:7px 14px; background:var(--color-cream); border:2px solid var(--color-black); border-radius:7px; font-size:0.8rem; font-weight:700; cursor:pointer; box-shadow:2px 2px 0 var(--color-black); transition:all .2s; color:var(--color-black); }
.a2f-copy-btn:hover { background:var(--color-pastel-yellow); transform:translate(-1px,-1px); box-shadow:3px 3px 0 var(--color-black); }
.a2f-copy-btn.copied { background:var(--color-pastel-green); }

/* Progress bar */
.a2f-progress { height:4px; background:#eee; border-radius:2px; margin-top:16px; overflow:hidden; }
.a2f-progress-bar { height:100%; border-radius:2px; transition:width .9s linear, background .5s; }

/* Empty state */
.a2f-empty { text-align:center; padding:80px 20px; color:#888; }
.a2f-empty-icon { font-size:4rem; margin-bottom:16px; }
.a2f-empty-title { font-family:var(--font-heading); font-size:1.3rem; font-weight:700; margin-bottom:8px; color:var(--color-black); }

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:2000; align-items:center; justify-content:center; padding:20px; }
.modal-overlay.open { display:flex; }
.modal { background:#fff; border:3px solid var(--color-black); border-radius:16px; padding:32px; width:100%; max-width:480px; box-shadow:8px 8px 0 var(--color-black); }
.modal h3 { font-family:var(--font-heading); font-size:1.4rem; font-weight:800; margin-bottom:8px; }
.modal p { color:#666; font-size:0.9rem; margin-bottom:24px; }
.modal-field { margin-bottom:16px; }
.modal-field label { display:block; font-size:0.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#555; margin-bottom:6px; }
.modal-field input { width:100%; padding:12px 14px; border:2px solid var(--color-black); border-radius:8px; font-size:0.95rem; background:var(--color-cream); outline:none; font-family:var(--font-body); transition:box-shadow .2s; }
.modal-field input:focus { box-shadow:3px 3px 0 var(--color-coral); border-color:var(--color-coral); }
.modal-hint { font-size:0.78rem; color:#888; margin-top:5px; }
.modal-actions { display:flex; gap:12px; margin-top:8px; }
.btn-save { flex:1; padding:12px; background:var(--color-coral); color:#fff; border:2px solid var(--color-black); border-radius:8px; font-family:var(--font-heading); font-weight:700; font-size:0.95rem; cursor:pointer; box-shadow:3px 3px 0 var(--color-black); transition:all .2s; }
.btn-save:hover { transform:translate(-1px,-1px); box-shadow:4px 4px 0 var(--color-black); }
.btn-cancel { padding:12px 20px; background:#fff; border:2px solid var(--color-black); border-radius:8px; font-family:var(--font-heading); font-weight:700; font-size:0.95rem; cursor:pointer; }

@media(max-width:640px) {
    .a2f-grid { grid-template-columns:1fr; }
    .a2f-header { flex-direction:column; }
}
</style>

<div class="a2f-wrap">
<div class="container">
    <a href="{{ route('tools.index') }}" class="a2f-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Tools
    </a>

    <div class="a2f-header">
        <div>
            <h1 class="a2f-title">🔐 A2F <span>Authenticator</span></h1>
            <p class="a2f-subtitle">Generator kode TOTP 2FA — data tersimpan aman di browser kamu.</p>
        </div>
        <button class="btn-add" onclick="openModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Akun
        </button>
    </div>

    <div class="a2f-grid" id="accountsGrid"></div>
    <div class="a2f-empty" id="emptyState" style="display:none;">
        <div class="a2f-empty-icon">🔑</div>
        <div class="a2f-empty-title">Belum ada akun</div>
        <p>Tambahkan akun 2FA dengan klik tombol di atas dan masukkan secret key dari aplikasimu.</p>
    </div>
</div>
</div>

{{-- Add Account Modal --}}
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <h3>Tambah Akun 2FA</h3>
        <p>Masukkan secret key yang biasanya diberikan saat setup 2FA di sebuah platform.</p>
        <div class="modal-field">
            <label>Nama Akun *</label>
            <input type="text" id="inp-name" placeholder="cth: Google — user@gmail.com" maxlength="60">
        </div>
        <div class="modal-field">
            <label>Issuer / Platform</label>
            <input type="text" id="inp-issuer" placeholder="cth: Google, GitHub, Shopee" maxlength="40">
        </div>
        <div class="modal-field">
            <label>Secret Key *</label>
            <input type="text" id="inp-secret" placeholder="cth: JBSWY3DPEHPK3PXP" autocomplete="off" style="font-family:monospace; letter-spacing:.08em;">
            <p class="modal-hint">⚠️ Secret key tersimpan di localStorage browser ini saja, tidak dikirim ke server manapun.</p>
        </div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Batal</button>
            <button class="btn-save" onclick="saveAccount()">Tambah Akun</button>
        </div>
    </div>
</div>

<script>
/* ── Storage ── */
const STORAGE_KEY = 'dexornit_a2f_accounts';
let accounts = [];

function load() {
    try { accounts = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch { accounts = []; }
}
function save() { localStorage.setItem(STORAGE_KEY, JSON.stringify(accounts)); }

/* ── TOTP ── */
function base32Decode(str) {
    const alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    str = str.toUpperCase().replace(/\s/g, '').replace(/=+$/, '');
    let bits = 0, val = 0;
    const out = [];
    for (const c of str) {
        const idx = alpha.indexOf(c);
        if (idx === -1) continue;
        val = (val << 5) | idx; bits += 5;
        if (bits >= 8) { out.push((val >>> (bits - 8)) & 0xff); bits -= 8; }
    }
    return new Uint8Array(out);
}

async function totp(secret, digits = 6, period = 30) {
    try {
        const key = base32Decode(secret);
        if (!key.length) return '──────';
        const T = Math.floor(Date.now() / 1000 / period);
        const buf = new ArrayBuffer(8);
        new DataView(buf).setUint32(4, T, false);
        const ck = await crypto.subtle.importKey('raw', key, { name:'HMAC', hash:'SHA-1' }, false, ['sign']);
        const sig = new Uint8Array(await crypto.subtle.sign('HMAC', ck, buf));
        const off = sig[sig.length - 1] & 0xf;
        const code = ((sig[off] & 0x7f) << 24 | sig[off+1] << 16 | sig[off+2] << 8 | sig[off+3]) % Math.pow(10, digits);
        return String(code).padStart(digits, '0');
    } catch { return '──────'; }
}

function timeLeft(period = 30) { return period - (Math.floor(Date.now() / 1000) % period); }

/* ── Render ── */
const EMOJIS = { google:'🟦', github:'⬛', shopee:'🟧', instagram:'🟫', facebook:'🔵', twitter:'⬛', telegram:'🔷', discord:'🟣', default:'🔑' };

function emoji(name) {
    const n = (name||'').toLowerCase();
    for (const [k,v] of Object.entries(EMOJIS)) if (n.includes(k)) return v;
    return EMOJIS.default;
}

function renderCards() {
    const grid = document.getElementById('accountsGrid');
    const empty = document.getElementById('emptyState');
    if (!accounts.length) { grid.innerHTML=''; empty.style.display='block'; return; }
    empty.style.display = 'none';
    grid.innerHTML = accounts.map(a => `
    <div class="a2f-card" id="card-${a.id}">
        <div class="a2f-card__header">
            <div class="a2f-card__avatar">${emoji(a.name+' '+(a.issuer||''))}</div>
            <div class="a2f-card__info">
                <div class="a2f-card__name">${esc(a.name)}</div>
                <div class="a2f-card__issuer">${esc(a.issuer||'Authenticator')}</div>
            </div>
            <button class="a2f-card__del" onclick="deleteAccount('${a.id}')" title="Hapus akun">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
            </button>
        </div>
        <div class="a2f-timer-wrap">
            <div class="a2f-ring-box">
                <svg width="72" height="72" viewBox="0 0 72 72">
                    <circle class="a2f-ring-bg" cx="36" cy="36" r="29"/>
                    <circle class="a2f-ring-progress" id="ring-${a.id}" cx="36" cy="36" r="29"
                        stroke-dasharray="${2*Math.PI*29}" stroke-dashoffset="0" stroke="#22c55e"/>
                </svg>
                <div class="a2f-ring-text" id="timer-${a.id}">30s</div>
            </div>
            <div class="a2f-code-box">
                <div class="a2f-code" id="code-${a.id}">••• •••</div>
                <button class="a2f-copy-btn" id="copy-${a.id}" onclick="copyCode('${a.id}')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Salin Kode
                </button>
            </div>
        </div>
        <div class="a2f-progress"><div class="a2f-progress-bar" id="bar-${a.id}" style="width:100%; background:#22c55e;"></div></div>
    </div>`).join('');
}

function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

/* ── Update loop ── */
async function tick() {
    for (const a of accounts) {
        const code = await totp(a.secret);
        const left  = timeLeft();
        const pct   = (left / 30) * 100;
        const color = left <= 5 ? '#ef4444' : left <= 10 ? '#f59e0b' : '#22c55e';
        const circ  = 2 * Math.PI * 29;

        const codeEl = document.getElementById('code-'+a.id);
        if (codeEl) codeEl.textContent = code.slice(0,3) + ' ' + code.slice(3);

        const ring = document.getElementById('ring-'+a.id);
        if (ring) { ring.style.strokeDashoffset = circ * (1 - left/30); ring.style.stroke = color; }

        const timer = document.getElementById('timer-'+a.id);
        if (timer) { timer.textContent = left+'s'; timer.style.color = color; }

        const bar = document.getElementById('bar-'+a.id);
        if (bar) { bar.style.width = pct+'%'; bar.style.background = color; }
    }
}

/* ── Copy ── */
function copyCode(id) {
    const el = document.getElementById('code-'+id);
    if (!el) return;
    const code = el.textContent.replace(/\s/g,'');
    navigator.clipboard.writeText(code).then(() => {
        const btn = document.getElementById('copy-'+id);
        if (!btn) return;
        btn.classList.add('copied');
        btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Tersalin!';
        setTimeout(() => {
            btn.classList.remove('copied');
            btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Salin Kode';
        }, 2000);
    });
}

/* ── Delete ── */
function deleteAccount(id) {
    if (!confirm('Hapus akun ini? Pastikan kamu sudah menyimpan secret key-nya.')) return;
    accounts = accounts.filter(a => a.id !== id);
    save(); renderCards();
}

/* ── Modal ── */
function openModal() { document.getElementById('modalOverlay').classList.add('open'); document.getElementById('inp-name').focus(); }
function closeModal() { document.getElementById('modalOverlay').classList.remove('open'); ['inp-name','inp-issuer','inp-secret'].forEach(id => document.getElementById(id).value=''); }

function saveAccount() {
    const name   = document.getElementById('inp-name').value.trim();
    const issuer = document.getElementById('inp-issuer').value.trim();
    const secret = document.getElementById('inp-secret').value.trim().toUpperCase().replace(/\s/g,'');

    if (!name) { alert('Nama akun wajib diisi.'); return; }
    if (!secret) { alert('Secret key wajib diisi.'); return; }
    if (!/^[A-Z2-7]+=*$/.test(secret)) { alert('Secret key tidak valid. Pastikan format Base32 (A-Z, 2-7).'); return; }

    accounts.push({ id: Date.now().toString(36), name, issuer, secret });
    save(); renderCards(); closeModal();
}

/* ── Close modal on overlay click ── */
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

/* ── Keyboard ── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
    if (e.key === 'Enter' && document.getElementById('modalOverlay').classList.contains('open')) saveAccount();
});

/* ── Init ── */
load(); renderCards(); tick();
setInterval(tick, 1000);
</script>
@endsection
