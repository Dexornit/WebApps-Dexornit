@extends('layouts.app')
@section('title', 'A2F Authenticator — Dexornit Tools')
@section('content')
<style>
.a2f-wrap {
    background: var(--color-cream);
    min-height: 80vh;
    padding: 110px 0 64px; /* top padding untuk fixed navbar */
}
.a2f-back {
    display: inline-flex; align-items: center; gap: 8px;
    text-decoration: none; color: #555; font-size: 0.88rem; font-weight: 600;
    margin-bottom: 36px; padding: 8px 16px;
    background: #fff; border: 2px solid var(--color-black);
    border-radius: 8px; box-shadow: 3px 3px 0 var(--color-black);
    transition: all .2s;
}
.a2f-back:hover { transform: translate(-2px,-2px); box-shadow: 5px 5px 0 var(--color-black); color: var(--color-black); }

.a2f-card-main {
    background: #fff;
    border: 3px solid var(--color-black);
    border-radius: 18px;
    padding: 36px;
    box-shadow: 7px 7px 0 var(--color-black);
    max-width: 680px;
    margin: 0 auto;
}
.a2f-head { display: flex; align-items: center; gap: 14px; margin-bottom: 28px; }
.a2f-head-icon {
    width: 48px; height: 48px; border-radius: 12px;
    background: var(--color-pastel-yellow); border: 2px solid var(--color-black);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.a2f-head-title { font-family: var(--font-heading); font-size: 1.4rem; font-weight: 800; }
.a2f-head-sub { color: #666; font-size: 0.88rem; margin-top: 2px; }

.a2f-label {
    display: block; font-size: 0.78rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em; color: #555; margin-bottom: 8px;
}
.a2f-input {
    width: 100%; padding: 14px 18px;
    border: 2px solid var(--color-black); border-radius: 10px;
    font-size: 1.05rem; font-family: 'Space Grotesk', monospace;
    letter-spacing: .08em; background: var(--color-cream);
    outline: none; transition: box-shadow .2s, border-color .2s;
    color: var(--color-black);
}
.a2f-input:focus { box-shadow: 4px 4px 0 var(--color-coral); border-color: var(--color-coral); }
.a2f-hint { font-size: 0.78rem; color: #888; margin-top: 6px; }

.a2f-btn-generate {
    width: 100%; padding: 15px;
    background: var(--color-coral); color: #fff;
    border: 3px solid var(--color-black); border-radius: 10px;
    font-family: var(--font-heading); font-size: 1rem; font-weight: 700;
    cursor: pointer; margin-top: 20px;
    box-shadow: 4px 4px 0 var(--color-black); transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 10px;
}
.a2f-btn-generate:hover { transform: translate(-2px,-2px); box-shadow: 6px 6px 0 var(--color-black); }
.a2f-btn-generate:disabled { opacity: .6; cursor: not-allowed; transform: none; }

/* Result box */
.a2f-result {
    display: none;
    margin-top: 28px;
    padding: 24px;
    background: var(--color-cream);
    border: 2px solid var(--color-black);
    border-radius: 12px;
}
.a2f-result.visible { display: block; }
.a2f-result-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.a2f-code-display {
    font-family: 'Space Grotesk', monospace;
    font-size: 2.4rem; font-weight: 800; letter-spacing: .2em;
    color: var(--color-black); line-height: 1;
}
.a2f-timer-wrap { display: flex; align-items: center; gap: 12px; }
.a2f-ring-box { position: relative; width: 64px; height: 64px; flex-shrink: 0; }
.a2f-ring-bg { fill: none; stroke: #ddd; stroke-width: 5; }
.a2f-ring-prog { fill: none; stroke-width: 5; stroke-linecap: round; transform: rotate(-90deg); transform-origin: center; transition: stroke-dashoffset .9s linear, stroke .4s; }
.a2f-ring-label { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; }
.a2f-copy {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 18px; background: #fff;
    border: 2px solid var(--color-black); border-radius: 8px;
    font-family: var(--font-heading); font-weight: 700; font-size: 0.88rem;
    cursor: pointer; box-shadow: 3px 3px 0 var(--color-black); transition: all .2s;
    color: var(--color-black);
}
.a2f-copy:hover { transform: translate(-1px,-1px); box-shadow: 4px 4px 0 var(--color-black); background: var(--color-pastel-yellow); }
.a2f-copy.copied { background: var(--color-pastel-green); }
.a2f-progress { height: 5px; background: #ddd; border-radius: 3px; margin-top: 16px; overflow: hidden; }
.a2f-progress-fill { height: 100%; border-radius: 3px; transition: width .9s linear, background .4s; }
.a2f-reset {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 16px; background: #fff;
    border: 2px solid #ccc; border-radius: 8px;
    font-size: 0.85rem; font-weight: 600; cursor: pointer; color: #666;
    transition: all .2s; margin-top: 16px;
}
.a2f-reset:hover { border-color: var(--color-black); color: var(--color-black); }

/* Bulk mode */
.a2f-bulk-toggle {
    float: right; padding: 6px 14px; background: #fff;
    border: 2px solid var(--color-black); border-radius: 6px;
    font-size: 0.78rem; font-weight: 700; cursor: pointer;
    box-shadow: 2px 2px 0 var(--color-black); transition: all .2s;
}
.a2f-bulk-toggle:hover { background: var(--color-pastel-yellow); }
.a2f-bulk-results { margin-top: 20px; display: flex; flex-direction: column; gap: 10px; }
.bulk-item {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    padding: 12px 16px; background: #fff;
    border: 2px solid var(--color-black); border-radius: 10px; flex-wrap: wrap;
}
.bulk-item__key { font-family: monospace; font-size: 0.82rem; color: #888; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bulk-item__code { font-family: 'Space Grotesk', monospace; font-size: 1.25rem; font-weight: 800; letter-spacing: .15em; }
.bulk-item__copy { padding: 6px 12px; background: var(--color-cream); border: 2px solid var(--color-black); border-radius: 6px; font-size: 0.78rem; font-weight: 700; cursor: pointer; }

@media(max-width: 580px) {
    .a2f-card-main { padding: 22px 16px; }
    .a2f-code-display { font-size: 1.8rem; }
    .a2f-result-row { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="a2f-wrap">
<div class="container">
    <a href="{{ route('tools.index') }}" class="a2f-back">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Tools
    </a>

    <div class="a2f-card-main">
        {{-- Header --}}
        <div class="a2f-head">
            <div class="a2f-head-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div>
                <div class="a2f-head-title">A2F Authenticator</div>
                <div class="a2f-head-sub">Generate kode TOTP 2FA langsung — tidak ada data yang disimpan</div>
            </div>
        </div>

        {{-- Mode toggle --}}
        <div style="overflow:hidden; margin-bottom: 8px;">
            <button class="a2f-bulk-toggle" id="toggleBulk" onclick="switchMode()">
                <span id="bulkLabel">☰ Bulk Mode</span>
            </button>
            <label class="a2f-label" id="inputLabel">Secret Key</label>
        </div>

        {{-- Single input --}}
        <div id="singleMode">
            <input type="text" class="a2f-input" id="secretInput"
                placeholder="Contoh: Q5OH OT7D G73T AAAA BBBB CCCC DDDD"
                autocomplete="off" spellcheck="false">
            <p class="a2f-hint">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Secret key berasal dari platform saat setup 2FA (biasanya format Base32). Kode tidak disimpan di manapun.
            </p>
        </div>

        {{-- Bulk input --}}
        <div id="bulkMode" style="display:none;">
            <textarea class="a2f-input" id="bulkInput" rows="5"
                placeholder="Satu secret key per baris:&#10;JBSWY3DPEHPK3PXP&#10;Q5OHOROT7DG73TAA&#10;..."
                style="resize:vertical; letter-spacing:0.04em; line-height:1.7;"></textarea>
            <p class="a2f-hint">Masukkan banyak secret key sekaligus, satu per baris.</p>
        </div>

        <button class="a2f-btn-generate" id="generateBtn" onclick="handleGenerate()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
            </svg>
            Generate Kode 2FA
        </button>

        {{-- Single Result --}}
        <div class="a2f-result" id="singleResult">
            <div class="a2f-result-row">
                <div>
                    <p style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#888; margin-bottom:6px;">Kode Saat Ini</p>
                    <div class="a2f-code-display" id="codeDisplay">••• •••</div>
                </div>
                <div class="a2f-timer-wrap">
                    <div class="a2f-ring-box">
                        <svg width="64" height="64" viewBox="0 0 64 64">
                            <circle class="a2f-ring-bg" cx="32" cy="32" r="26"/>
                            <circle class="a2f-ring-prog" id="ringCircle" cx="32" cy="32" r="26"
                                stroke-dasharray="{{ 2 * pi() * 26 }}"
                                stroke-dashoffset="0" stroke="#22c55e"/>
                        </svg>
                        <div class="a2f-ring-label" id="ringTimer" style="color:#22c55e;">30s</div>
                    </div>
                    <button class="a2f-copy" id="copyBtn" onclick="copyCode()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Salin
                    </button>
                </div>
            </div>
            <div class="a2f-progress">
                <div class="a2f-progress-fill" id="progressBar" style="width:100%; background:#22c55e;"></div>
            </div>
            <div>
                <button class="a2f-reset" onclick="resetForm()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                    Reset / Ganti Key
                </button>
            </div>
        </div>

        {{-- Bulk Results --}}
        <div id="bulkResults" class="a2f-bulk-results"></div>
    </div>
</div>
</div>

<script>
let isBulk = false;
let ticker = null;
let currentSecret = '';

/* ── Mode switch ── */
function switchMode() {
    isBulk = !isBulk;
    document.getElementById('singleMode').style.display = isBulk ? 'none' : 'block';
    document.getElementById('bulkMode').style.display   = isBulk ? 'block' : 'none';
    document.getElementById('bulkLabel').textContent    = isBulk ? '✕ Single Mode' : '☰ Bulk Mode';
    document.getElementById('inputLabel').textContent   = isBulk ? 'Secret Keys (satu per baris)' : 'Secret Key';
    resetForm();
}

/* ── Generate handler ── */
async function handleGenerate() {
    if (isBulk) {
        await generateBulk();
    } else {
        await generateSingle();
    }
}

async function generateSingle() {
    const raw = document.getElementById('secretInput').value.trim();
    if (!raw) { alert('Masukkan secret key terlebih dahulu.'); return; }
    currentSecret = raw.toUpperCase().replace(/\s/g,'');
    if (!/^[A-Z2-7]+=*$/.test(currentSecret)) { alert('Format secret key tidak valid (Base32: A-Z, 2-7).'); return; }

    document.getElementById('singleResult').classList.add('visible');
    document.getElementById('bulkResults').innerHTML = '';
    document.getElementById('secretInput').disabled = true;
    document.getElementById('generateBtn').disabled = true;

    clearInterval(ticker);
    await tickSingle();
    ticker = setInterval(tickSingle, 1000);
}

async function generateBulk() {
    const lines = document.getElementById('bulkInput').value
        .split('\n').map(l => l.trim().toUpperCase().replace(/\s/g,'')).filter(Boolean);
    if (!lines.length) { alert('Masukkan minimal satu secret key.'); return; }

    document.getElementById('singleResult').classList.remove('visible');
    const container = document.getElementById('bulkResults');
    container.innerHTML = '<p style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#888;margin-bottom:8px;">Hasil Generate</p>';

    for (const key of lines) {
        if (!/^[A-Z2-7]+=*$/.test(key)) {
            container.innerHTML += `<div class="bulk-item"><span class="bulk-item__key">${key}</span><span style="color:#ef4444;font-size:0.82rem;font-weight:700;">❌ Key tidak valid</span></div>`;
            continue;
        }
        const code = await totp(key);
        const id = 'bk_' + Math.random().toString(36).slice(2);
        container.innerHTML += `
        <div class="bulk-item">
            <span class="bulk-item__key">${key.slice(0,12)}…</span>
            <span class="bulk-item__code" id="${id}">${code.slice(0,3)} ${code.slice(3)}</span>
            <button class="bulk-item__copy" onclick="copyBulk('${id}','${code}')">Salin</button>
        </div>`;
    }
}

async function tickSingle() {
    if (!currentSecret) return;
    const code = await totp(currentSecret);
    const left  = timeLeft();
    const pct   = (left / 30) * 100;
    const color = left <= 5 ? '#ef4444' : left <= 10 ? '#f59e0b' : '#22c55e';
    const circ  = 2 * Math.PI * 26;

    const cd = document.getElementById('codeDisplay');
    if (cd) cd.textContent = code.slice(0,3) + ' ' + code.slice(3);

    const ring = document.getElementById('ringCircle');
    if (ring) { ring.style.strokeDashoffset = circ * (1 - left/30); ring.style.stroke = color; }

    const rt = document.getElementById('ringTimer');
    if (rt) { rt.textContent = left+'s'; rt.style.color = color; }

    const bar = document.getElementById('progressBar');
    if (bar) { bar.style.width = pct+'%'; bar.style.background = color; }
}

function resetForm() {
    clearInterval(ticker); ticker = null; currentSecret = '';
    document.getElementById('singleResult').classList.remove('visible');
    document.getElementById('bulkResults').innerHTML = '';
    document.getElementById('secretInput').disabled = false;
    document.getElementById('secretInput').value = '';
    document.getElementById('bulkInput').value = '';
    document.getElementById('generateBtn').disabled = false;
}

/* ── TOTP core ── */
function base32Decode(str) {
    const alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    str = str.replace(/=+$/,'');
    let bits = 0, val = 0;
    const out = [];
    for (const c of str) {
        const idx = alpha.indexOf(c);
        if (idx < 0) continue;
        val = (val << 5) | idx; bits += 5;
        if (bits >= 8) { out.push((val >>> (bits-8)) & 0xff); bits -= 8; }
    }
    return new Uint8Array(out);
}

async function totp(secret, digits = 6, period = 30) {
    try {
        const key = base32Decode(secret);
        if (!key.length) return '------';
        const T = Math.floor(Date.now() / 1000 / period);
        const buf = new ArrayBuffer(8);
        new DataView(buf).setUint32(4, T, false);
        const ck = await crypto.subtle.importKey('raw', key, {name:'HMAC',hash:'SHA-1'}, false, ['sign']);
        const sig = new Uint8Array(await crypto.subtle.sign('HMAC', ck, buf));
        const off = sig[sig.length-1] & 0xf;
        const code = ((sig[off]&0x7f)<<24 | sig[off+1]<<16 | sig[off+2]<<8 | sig[off+3]) % Math.pow(10,digits);
        return String(code).padStart(digits,'0');
    } catch { return '------'; }
}

function timeLeft(period = 30) { return period - (Math.floor(Date.now()/1000) % period); }

/* ── Copy ── */
function copyCode() {
    const txt = (document.getElementById('codeDisplay').textContent || '').replace(/\s/g,'');
    navigator.clipboard.writeText(txt).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.classList.add('copied');
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Tersalin!';
        setTimeout(() => {
            btn.classList.remove('copied');
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Salin';
        }, 2000);
    });
}

function copyBulk(id, code) {
    navigator.clipboard.writeText(code).then(() => {
        const el = document.querySelector(`[onclick="copyBulk('${id}','${code}')"]`);
        if (el) { el.textContent = '✓ Tersalin'; setTimeout(() => el.textContent = 'Salin', 1500); }
    });
}

/* ── Enter key ── */
document.getElementById('secretInput').addEventListener('keydown', e => { if(e.key==='Enter') handleGenerate(); });
</script>
@endsection
