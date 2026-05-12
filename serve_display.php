<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_check.php';
writeUsageLog('SERVE_PAGE_LOAD');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Serve Display</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="apple-touch-icon" href="logo.svg">
    <meta name="theme-color" content="#0d9488">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <style>
        :root{
            --bg:#f0fdfa;--bg2:#fff7ed;
            --line:#ccfbf1;
            --primary:#0d9488;
            --ready:#16a34a;
            --cooking:#ea580c;
            --muted:#6b7a90;
            --text:#0f2945;
            --shadow:0 8px 24px rgba(15,23,42,.10);
        }
        *{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
        html,body{margin:0;min-height:100%}
        body{
            font-family:Tahoma,Arial,sans-serif;color:var(--text);
            background:
                radial-gradient(circle at top left,rgba(13,148,136,.12),transparent 28%),
                radial-gradient(circle at top right,rgba(234,88,12,.10),transparent 24%),
                linear-gradient(180deg,var(--bg),var(--bg2));
        }

        /* Topbar */
        .topbar{
            position:sticky;top:0;z-index:30;
            padding:9px 14px 8px;
            backdrop-filter:blur(12px);
            background:linear-gradient(135deg,rgba(4,78,70,.92),rgba(13,148,136,.88),rgba(234,88,12,.80));
            color:#fff;box-shadow:0 6px 18px rgba(4,78,70,.20);
        }
        .topbar-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:10px}
        .brand-name{font-size:20px;font-weight:bold}
        .topbar-right{display:flex;align-items:center;gap:10px}
        .status-dot{width:8px;height:8px;border-radius:50%;background:#4ade80;flex-shrink:0;transition:background .3s}
        .status-dot.loading{background:#fb923c}
        .status-dot.error{background:#f87171}
        .last-update{font-size:11px;opacity:.75}
        .btn-fs{
            appearance:none;width:34px;height:34px;border-radius:9px;border:none;cursor:pointer;
            background:rgba(255,255,255,.16);color:#fff;
            border:1px solid rgba(255,255,255,.2);
            display:inline-flex;align-items:center;justify-content:center;
        }
        .btn-fs svg{width:15px;height:15px}

        /* Grid */
        .page{max-width:1200px;margin:0 auto;padding:14px 12px 28px}
        .table-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px}

        /* Cards */
        .table-card{
            background:#fff;border:2.5px solid var(--line);border-radius:18px;
            padding:16px 10px 14px;text-align:center;cursor:pointer;
            display:flex;flex-direction:column;align-items:center;gap:7px;
            box-shadow:var(--shadow);transition:transform .12s;user-select:none
        }
        .table-card:active{transform:scale(.94)}

        /* สีเขียวสว่าง = พร้อมเสิร์ฟทั้งหมด */
        .table-card.s-ready{
            border-color:#86efac;
            background:linear-gradient(180deg,#dcfce7,#fff);
            box-shadow:0 0 0 3px rgba(22,163,74,.20),var(--shadow);
            animation:pulse-ready 2s ease-in-out infinite;
        }
        @keyframes pulse-ready{
            0%,100%{box-shadow:0 0 0 3px rgba(22,163,74,.20),var(--shadow)}
            50%{box-shadow:0 0 0 6px rgba(22,163,74,.35),var(--shadow)}
        }

        /* สีส้ม = ยังมีบางรายการทำอยู่ */
        .table-card.s-cooking{
            border-color:#fed7aa;
            background:linear-gradient(180deg,#fff7ed,#fff);
            box-shadow:0 0 0 3px rgba(234,88,12,.12),var(--shadow);
        }

        .tc-name{font-size:24px;font-weight:bold;color:#0f2945;line-height:1.1}
        .table-card.s-ready .tc-name{color:#15803d}
        .table-card.s-cooking .tc-name{color:#c2410c}

        .tc-badge{font-size:12px;font-weight:bold;padding:2px 0}
        .tc-badge.ready{color:var(--ready)}
        .tc-badge.cooking{color:var(--cooking)}

        /* Modal */
        .modal-overlay{
            display:none;position:fixed;inset:0;z-index:100;
            background:rgba(4,30,24,.55);backdrop-filter:blur(4px);
            align-items:flex-end;justify-content:center
        }
        .modal-overlay.open{display:flex}
        .modal-box{
            background:#fff;border-radius:24px 24px 0 0;width:100%;max-width:600px;
            max-height:88dvh;display:flex;flex-direction:column;
            box-shadow:0 -14px 44px rgba(4,30,24,.22);animation:slideUp .2s ease
        }
        @keyframes slideUp{from{transform:translateY(50px);opacity:0}to{transform:translateY(0);opacity:1}}
        .modal-head{
            display:flex;align-items:center;justify-content:space-between;gap:12px;
            padding:16px 18px 12px;border-bottom:1px solid #e5e7eb;flex-shrink:0
        }
        .modal-title{font-size:22px;font-weight:bold;color:#0f2945}
        .modal-sub{font-size:13px;color:var(--muted);margin-top:3px}
        .modal-close{
            width:36px;height:36px;border-radius:50%;border:none;background:#f0f4f8;
            color:#4a6080;font-size:22px;cursor:pointer;
            display:flex;align-items:center;justify-content:center;flex-shrink:0
        }
        .modal-body{overflow-y:auto;padding:10px 12px 28px;display:flex;flex-direction:column;gap:1px}
        .modal-msg{padding:32px;text-align:center;color:var(--muted);font-weight:bold}

        /* Order rows */
        .order-row{
            display:grid;grid-template-columns:minmax(0,1fr) auto;
            align-items:center;gap:8px;padding:12px 14px
        }
        .order-row:first-child{border-radius:14px 14px 0 0}
        .order-row:last-child{border-radius:0 0 14px 14px}
        .order-row+.order-row{border-top:1px solid #f1f5f9}
        .order-row.r-active{background:#fff}
        .order-row.r-ready{background:linear-gradient(90deg,#f0fdf4,#fff)}
        .order-row.r-served{background:#f9fafb;opacity:.6}
        .order-row.r-voided{background:#f3f4f6;opacity:.5}
        .or-name{font-size:15px;font-weight:bold}
        .order-row.r-served .or-name{color:#9ca3af;text-decoration:line-through}
        .order-row.r-voided .or-name{color:#9ca3af;text-decoration:line-through}
        .or-time{font-size:11px;color:var(--muted);margin-top:3px}
        .or-right{text-align:right;flex-shrink:0}
        .or-qty{font-size:16px;font-weight:bold}
        .order-row.r-served .or-qty{color:#9ca3af}
        .or-status{font-size:12px;font-weight:bold;margin-top:2px;color:var(--muted)}
        .order-row.r-ready .or-status{color:var(--ready)}
        .order-row.r-active .or-status{color:var(--cooking)}

        .empty-state{
            text-align:center;padding:60px 24px;color:var(--muted);
        }
        .empty-state .icon{font-size:48px;margin-bottom:12px}
        .empty-state .msg{font-size:16px;font-weight:bold}
        .empty-state .sub{font-size:13px;margin-top:4px}
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-inner">
        <div class="brand-name">🍽️ Serve Display</div>
        <div class="topbar-right">
            <span class="last-update" id="lastUpdate"></span>
            <span class="status-dot" id="statusDot"></span>
            <button class="btn-fs" id="btnFs" title="Fullscreen">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<div class="page">
    <div class="table-grid" id="tableGrid"></div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="tableModal">
    <div class="modal-box">
        <div class="modal-head">
            <div>
                <div class="modal-title" id="modalTitle"></div>
                <div class="modal-sub" id="modalSub"></div>
            </div>
            <button class="modal-close" id="modalClose">×</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<script>
const REFRESH_MS = <?php echo max(5000,(int)APP_REFRESH_MS); ?>;

const PS_DONE     = <?php echo (int)PROCESS_STATUS_FINISHED; ?>;
const PS_RESOLVED = <?php echo (int)PROCESS_STATUS_RESOLVED; ?>;
const PS_VOIDED   = <?php echo (int)PROCESS_STATUS_VOIDED; ?>;

const state = { tables: [] };
let _timer = null;
let _modalController = null;

/* ── Utilities ── */
function esc(s){ return String(s??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[m]); }
function safeArray(v){ return Array.isArray(v) ? v : []; }
function fmtTime(v){
    if(!v) return '-';
    const d = new Date(String(v).replace(' ','T'));
    if(isNaN(d)) return esc(v);
    return d.toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit'});
}
function fmtQty(v){ const n=parseFloat(v); return Number.isInteger(n)?String(n):n.toFixed(2).replace(/\.?0+$/,''); }

/* ── Status dot ── */
function setDot(state){ document.getElementById('statusDot').className='status-dot'+(state?' '+state:''); }

/* ── Render Grid ── */
function renderGrid(){
    const wrap = document.getElementById('tableGrid');
    const tables = state.tables;

    if(!tables.length){
        wrap.innerHTML = `<div class="empty-state" style="grid-column:1/-1">
            <div class="icon">✅</div>
            <div class="msg">ไม่มีรายการรออยู่</div>
            <div class="sub">อาหารทุกโต๊ะเสิร์ฟเรียบร้อยแล้ว</div>
        </div>`;
        return;
    }

    // เรียงโต๊ะ: พร้อมเสิร์ฟก่อน แล้ว natural sort ตามชื่อ
    const sorted = [...tables].sort((a,b) => {
        const aReady = a.cooking === 0;
        const bReady = b.cooking === 0;
        if(aReady !== bReady) return aReady ? -1 : 1;
        return String(a.name).localeCompare(String(b.name), undefined, {numeric:true, sensitivity:'base'});
    });

    wrap.innerHTML = sorted.map(t => {
        const isReady   = t.cooking === 0 && t.ready > 0;
        const isCooking = t.cooking > 0;
        const cls = isReady ? 's-ready' : isCooking ? 's-cooking' : '';
        const badges = [];
        if(t.ready > 0)   badges.push(`<div class="tc-badge ready">✅ ${t.ready} พร้อมเสิร์ฟ</div>`);
        if(t.cooking > 0) badges.push(`<div class="tc-badge cooking">🍳 ${t.cooking} กำลังทำ</div>`);
        return `<div class="table-card ${cls}" data-key="${esc(t.key)}" data-name="${esc(t.name)}" data-txid="${t.transaction_id}" data-date="${esc(t.order_date)}">
            <div class="tc-name">${esc(t.name)}</div>
            ${badges.join('')}
        </div>`;
    }).join('');

    wrap.querySelectorAll('.table-card').forEach(el => {
        el.addEventListener('click', () => openModal(
            el.dataset.key,
            el.dataset.name,
            parseInt(el.dataset.txid||'0',10),
            el.dataset.date||''
        ));
    });
}

/* ── Fetch ── */
async function refresh(){
    setDot('loading');
    try {
        const res  = await fetch('api_checker.php?action=list_serve_view&_='+Date.now(),{cache:'no-store'});
        const json = await res.json();
        if(!json.success) throw new Error(json.error||'error');
        state.tables = safeArray(json.rows);
        document.getElementById('lastUpdate').textContent = 'อัปเดต '+fmtTime(json.generated_at||new Date().toISOString());
        setDot('');
    } catch(e){
        setDot('error');
    }
    renderGrid();
    _timer = setTimeout(refresh, REFRESH_MS);
}

/* ── Modal ── */
function openModal(key, name, txId, orderDate){
    if(_modalController) _modalController.abort();
    _modalController = new AbortController();
    const sig = _modalController.signal;

    document.getElementById('modalTitle').textContent = 'โต๊ะ ' + name;
    document.getElementById('modalSub').textContent   = '';
    document.getElementById('modalBody').innerHTML    = '<div class="modal-msg">กำลังโหลด...</div>';
    document.getElementById('tableModal').classList.add('open');
    document.body.style.overflow = 'hidden';

    const txParam = txId > 0 ? '&transaction_id='+txId : '';
    const odParam = !txParam && orderDate ? '&order_date='+encodeURIComponent(orderDate) : '';
    fetch('api_checker.php?action=list_serve_table_orders&table_id='+encodeURIComponent(key)+txParam+odParam+'&_='+Date.now(),{cache:'no-store',signal:sig})
        .then(r => r.json())
        .then(json => {
            if(!json.success) throw new Error(json.error||'error');
            const rows    = safeArray(json.rows).filter(r => parseInt(r.ProcessStatus,10) !== PS_VOIDED);
            const nReady  = rows.filter(r => {const s=parseInt(r.ProcessStatus,10); return (s===PS_DONE||s===PS_RESOLVED) && !r.ServingDateTime;}).length;
            const nCook   = rows.filter(r => {const s=parseInt(r.ProcessStatus,10); return s!==PS_DONE&&s!==PS_RESOLVED&&s!==PS_VOIDED;}).length;
            const nServed = rows.filter(r => !!r.ServingDateTime).length;
            document.getElementById('modalSub').textContent =
                `✅ พร้อมเสิร์ฟ ${nReady}  ·  🍳 ทำอยู่ ${nCook}  ·  🚚 เสิร์ฟแล้ว ${nServed}`;
            document.getElementById('modalBody').innerHTML = rows.length
                ? rows.map(buildRow).join('')
                : '<div class="modal-msg">ไม่มีรายการ</div>';
        })
        .catch(err => {
            if(err.name==='AbortError') return;
            document.getElementById('modalBody').innerHTML='<div class="modal-msg">โหลดไม่สำเร็จ</div>';
        });
}

function buildRow(row){
    const st     = parseInt(row.ProcessStatus, 10);
    const served = !!row.ServingDateTime;
    const isDone = st === PS_DONE || st === PS_RESOLVED;
    const isCook = !isDone && st !== PS_VOIDED;

    let cls, lbl;
    if(served){
        cls = 'r-served'; lbl = '🚚 เสิร์ฟแล้ว';
    } else if(isDone){
        cls = 'r-ready';  lbl = '✅ พร้อมเสิร์ฟ';
    } else if(isCook){
        cls = 'r-active'; lbl = '🍳 กำลังทำ';
    } else {
        cls = 'r-voided'; lbl = '🚫 ยกเลิก';
    }

    const name = row.parent_name && String(row.parent_name).trim()
        ? `${esc(row.parent_name)} · ${esc(row.ProductName||'-')}`
        : esc(row.ProductName||'-');

    const finishOk = isDone && row.FinishDateTime && row.FinishDateTime !== '0000-00-00 00:00:00';
    const time = finishOk
        ? `ส่ง ${esc(fmtTime(row.SubmitOrderDateTime))} · เสร็จ ${esc(fmtTime(row.FinishDateTime))}`
        : `ส่ง ${esc(fmtTime(row.SubmitOrderDateTime))}`;

    return `<div class="order-row ${cls}">
        <div>
            <div class="or-name">${name}</div>
            <div class="or-time">${time}</div>
        </div>
        <div class="or-right">
            <div class="or-qty">x${fmtQty(row.ProductAmount)}</div>
            <div class="or-status">${lbl}</div>
        </div>
    </div>`;
}

function closeModal(){
    if(_modalController){ _modalController.abort(); _modalController=null; }
    document.getElementById('tableModal').classList.remove('open');
    document.body.style.overflow='';
}

/* ── Events ── */
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('tableModal').addEventListener('click', e => { if(e.target===e.currentTarget) closeModal(); });
document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });

document.getElementById('btnFs').addEventListener('click', () => {
    if(!document.fullscreenElement) document.documentElement.requestFullscreen?.();
    else document.exitFullscreen?.();
});

document.addEventListener('visibilitychange', () => {
    if(document.hidden){ clearTimeout(_timer); }
    else { clearTimeout(_timer); refresh(); }
});

/* ── Init ── */
refresh();
</script>
</body>
</html>
