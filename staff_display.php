<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_check.php';
$machineDisplayName = function_exists('getMachineDisplayName') ? getMachineDisplayName() : '';
$_pageCid = isset($_REQUEST['cid']) ? (int)$_REQUEST['cid'] : (int)CURRENT_COMPUTER_ID;
writeUsageLog('PAGE_LOAD', ['cid' => $_pageCid]);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo h(APP_TITLE); ?> - Staff Display</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="apple-touch-icon" href="logo.svg">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#1683ff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Staff Display">
    <meta name="mobile-web-app-capable" content="yes">
    <style>
        :root{
            --bg:#edf5ff;--bg2:#fff7ed;
            --line:#dbe8f7;
            --primary:#1683ff;
            --secondary:#ff8a1f;
            --success:#12a150;
            --danger:#e44c3a;
            --muted:#6b7a90;
            --text:#122033;
            --shadow:0 8px 24px rgba(15,23,42,.10);
        }
        *{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
        html,body{margin:0;min-height:100%}
        body{
            font-family:Tahoma,Arial,sans-serif;color:var(--text);
            background:
                radial-gradient(circle at top left,rgba(22,131,255,.12),transparent 28%),
                radial-gradient(circle at top right,rgba(255,138,31,.14),transparent 24%),
                linear-gradient(180deg,var(--bg),var(--bg2));
        }

        /* Topbar */
        .topbar{
            position:sticky;top:0;z-index:30;
            padding:9px 14px 8px;
            backdrop-filter:blur(12px);
            background:linear-gradient(135deg,rgba(8,58,112,.92),rgba(22,131,255,.88),rgba(255,138,31,.84));
            color:#fff;box-shadow:0 6px 18px rgba(8,58,112,.18);
        }
        .topbar-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:10px}
        .brand{display:flex;align-items:center;gap:8px;min-width:0}
        .brand-name{font-size:20px;font-weight:bold;white-space:nowrap}
        .machine-chip{
            display:inline-flex;align-items:center;padding:2px 10px;border-radius:999px;
            background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);
            font-size:11px;font-weight:700;white-space:nowrap;flex-shrink:0
        }
        .topbar-actions{display:flex;align-items:center;gap:7px;flex-shrink:0}
        .status-dot{width:8px;height:8px;border-radius:50%;background:var(--success);flex-shrink:0;transition:background .3s}
        .status-dot.loading{background:#f5a623}
        .status-dot.error{background:var(--danger)}
        .btn-action{
            appearance:none;height:34px;padding:0 13px;border-radius:9px;
            font-size:12px;font-weight:bold;cursor:pointer;white-space:nowrap;
            background:rgba(255,255,255,.16);color:#fff;
            border:1px solid rgba(255,255,255,.2);transition:background .14s
        }
        .btn-action:active{background:rgba(255,255,255,.32)}
        .btn-fs{
            appearance:none;width:34px;height:34px;border-radius:9px;border:none;cursor:pointer;
            background:rgba(255,255,255,.16);color:#fff;
            border:1px solid rgba(255,255,255,.2);
            display:inline-flex;align-items:center;justify-content:center;flex-shrink:0
        }
        .btn-fs svg{width:15px;height:15px}

        /* Zone bar */
        .zone-bar{background:rgba(255,255,255,.95);border-bottom:1px solid var(--line);backdrop-filter:blur(8px)}
        .zone-bar-inner{max-width:1200px;margin:0 auto;padding:8px 12px;display:flex;gap:7px;overflow-x:auto;scrollbar-width:none}
        .zone-bar-inner::-webkit-scrollbar{display:none}
        .btn-zone{
            appearance:none;height:32px;padding:0 16px;border-radius:999px;
            border:1px solid var(--line);background:#fff;color:var(--muted);
            font-size:13px;font-weight:bold;cursor:pointer;white-space:nowrap;flex-shrink:0;
            transition:background .14s,color .14s,border-color .14s
        }
        .btn-zone.active{background:var(--primary);color:#fff;border-color:var(--primary)}

        /* Table grid */
        .page{max-width:1200px;margin:0 auto;padding:14px 12px 28px}
        .table-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px}
        .table-card{
            background:#fff;border:2px solid var(--line);border-radius:18px;
            padding:14px 10px 12px;text-align:center;cursor:pointer;
            display:flex;flex-direction:column;align-items:center;gap:6px;
            box-shadow:var(--shadow);transition:transform .12s;
            user-select:none
        }
        .table-card:active{transform:scale(.94)}
        .table-card.s-yellow{border-color:#ffe066;background:linear-gradient(180deg,#fffde7,#fff);box-shadow:0 0 0 3px rgba(255,214,0,.2)}
        .table-card.s-red{border-color:#ffb3ab;background:linear-gradient(180deg,#fff2f0,#fff);box-shadow:0 0 0 3px rgba(228,76,58,.15)}
        .table-card.s-done{border-color:#6edda0;background:linear-gradient(180deg,#edfff5,#fff);box-shadow:0 0 0 3px rgba(18,161,80,.18)}
        .table-card.s-empty{border-color:#e5e7eb;background:#f9fafb;box-shadow:none;opacity:.6;cursor:default}
        .tc-name{font-size:22px;font-weight:bold;color:#0f2945;line-height:1.1}
        .table-card.s-done .tc-name{color:#0b7a3e}
        .table-card.s-empty .tc-name{color:#9ca3af}
        .tc-badge{font-size:12px;font-weight:bold;padding:2px 0}
        .tc-badge.kitchen{color:var(--secondary)}
        .tc-badge.done{color:var(--success)}
        .tc-badge.empty{color:#9ca3af}

        /* Modal */
        .modal-overlay{
            display:none;position:fixed;inset:0;z-index:100;
            background:rgba(8,30,60,.55);backdrop-filter:blur(4px);
            align-items:flex-end;justify-content:center
        }
        .modal-overlay.open{display:flex}
        .modal-box{
            background:#fff;border-radius:24px 24px 0 0;width:100%;max-width:600px;
            max-height:88dvh;display:flex;flex-direction:column;
            box-shadow:0 -14px 44px rgba(8,30,60,.22);animation:slideUp .2s ease
        }
        @keyframes slideUp{from{transform:translateY(50px);opacity:0}to{transform:translateY(0);opacity:1}}
        .modal-head{
            display:flex;align-items:center;justify-content:space-between;gap:12px;
            padding:16px 18px 12px;border-bottom:1px solid var(--line);flex-shrink:0
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
        .order-row:only-child{border-radius:14px}
        .order-row.r-done{background:#f0fff6}
        .order-row.r-active{background:#fff8f2}
        .order-row.r-voided{background:#f3f4f6;opacity:.6}
        .or-name{font-weight:bold;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .r-done   .or-name{color:#0b7a3e}
        .r-active .or-name{color:#0f2945}
        .r-voided .or-name{color:#9ca3af;text-decoration:line-through}
        .or-time{font-size:11px;color:var(--muted);margin-top:2px}
        .or-right{text-align:right;flex-shrink:0}
        .or-qty{font-size:18px;font-weight:bold}
        .r-done   .or-qty{color:var(--success)}
        .r-active .or-qty{color:var(--secondary)}
        .r-voided .or-qty{color:#9ca3af}
        .or-status{font-size:11px;font-weight:bold;margin-top:2px;white-space:nowrap}
        .r-done   .or-status{color:var(--success)}
        .r-active .or-status{color:var(--secondary)}
        .r-voided .or-status{color:#9ca3af}

        .hidden{display:none!important}

        @media(min-width:600px){
            .modal-overlay{align-items:center}
            .modal-box{border-radius:24px;max-height:80dvh}
        }
        @media(max-width:480px){
            .table-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}
            .table-card{padding:12px 8px 10px}
            .tc-name{font-size:18px}
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <img src="logo.svg" alt="" style="width:26px;height:26px;border-radius:6px;flex-shrink:0">
            <span class="brand-name">Staff Display</span>
            <?php if ($machineDisplayName !== ''): ?>
                <span class="machine-chip"><?php echo h($machineDisplayName); ?></span>
            <?php endif; ?>
        </div>
        <div class="topbar-actions">
            <div class="status-dot" id="statusDot"></div>
            <button class="btn-action" id="refreshBtn">รีเฟรช</button>
            <button class="btn-fs" id="fsBtn" title="เต็มจอ">
                <svg class="fs-enter" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
                <svg class="fs-exit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" style="display:none"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/></svg>
            </button>
        </div>
    </div>
</div>

<div id="zoneBar" class="zone-bar hidden">
    <div class="zone-bar-inner" id="zoneInner"></div>
</div>

<div class="page">
    <div class="table-grid" id="tableGrid">
        <div class="modal-msg" style="grid-column:1/-1">กำลังโหลด...</div>
    </div>
</div>

<div class="modal-overlay" id="tableModal">
    <div class="modal-box">
        <div class="modal-head">
            <div>
                <div class="modal-title" id="modalTitle">โต๊ะ -</div>
                <div class="modal-sub"  id="modalSub"></div>
            </div>
            <button class="modal-close" id="modalClose">×</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<script>
const REFRESH_MS = <?php echo (int)APP_REFRESH_MS; ?>;
const T_YELLOW   = <?php echo (int)(defined('ALERT_THRESHOLD_YELLOW_DEFAULT') ? ALERT_THRESHOLD_YELLOW_DEFAULT : 10); ?>;
const T_RED      = <?php echo (int)(defined('ALERT_THRESHOLD_RED_DEFAULT')    ? ALERT_THRESHOLD_RED_DEFAULT    : 20); ?>;
const PAGE_CID   = <?php echo (int)(isset($_GET['cid']) && (int)$_GET['cid'] > 0 ? (int)$_GET['cid'] : 0); ?>;
const cidParam   = PAGE_CID > 0 ? '&cid=' + PAGE_CID : '';
const PS_DONE    = 1;
const PS_VOIDED  = 98;

const state = {
    active:   [],
    finished: [],
    zones:    [],
    zoneId:   null,
    zoneTables:      null,   // Set<string> | null
    allowedPrinters: null,   // Set<number> | null (null = no filter)
};

/* ── Utilities ── */
function safeArray(v){ return Array.isArray(v) ? v : []; }
function esc(s){ return String(s??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[m]); }
function fmtTime(v){
    if(!v) return '-';
    const d = new Date(String(v).replace(' ','T'));
    return isNaN(d) ? esc(String(v).slice(11,16)||String(v))
        : d.toLocaleTimeString('th-TH',{hour12:false,hour:'2-digit',minute:'2-digit'});
}
function fmtQty(v){
    const n = Number(v||0);
    return Number.isInteger(n) ? String(n) : n.toFixed(2).replace(/\.00$/,'');
}
function waitMin(row){
    const d = new Date(String(row.SubmitOrderDateTime||'').replace(' ','T'));
    return isNaN(d) ? 0 : Math.max(0,Math.floor((Date.now()-d)/60000));
}
function tKey(row){ return String(row.TableID || row.DisplayTableName || '-'); }
// สำหรับ order ย้ายโต๊ะ: ถ้า TableID ว่าง ให้ใช้ moved_to (ปลายทาง) แทน DisplayTableName "2->4"
function tKeyEff(row){
    if(!row.TableID && row.is_moved && row.moved_to) return String(row.moved_to);
    return tKey(row);
}
// row.PrinterID ไม่อยู่ใน printerSet → ไม่ใช่ station นี้ → auto-done
// ถ้า set=null (ไม่ได้ config printer) → ไม่กรอง
function nonKds(row, set){
    if(!set || set.size === 0) return false;
    return !row.is_voided && !set.has(parseInt(row.PrinterID, 10));
}
function isNonKds(row){ return nonKds(row, state.allowedPrinters); }
function byZone(rows){
    if(!state.zoneTables) return safeArray(rows);
    return safeArray(rows).filter(r => state.zoneTables.has(tKeyEff(r)));
}

/* ── Group rows by table → card data ── */
function groupTables(active, finished){
    const map = new Map();
    function get(key, name){
        if(!map.has(key)) map.set(key,{key,name,pending:0,done:0,worst:0});
        return map.get(key);
    }
    safeArray(active).forEach(r => {
        const key  = tKeyEff(r);
        const name = r.is_moved && r.moved_to ? String(r.moved_to) : (r.DisplayTableName || r.TableID || '-');
        const g    = get(key, name);
        if(!r.is_voided && !r.is_combined && !isNonKds(r)){
            g.pending++;
            g.worst = Math.max(g.worst, waitMin(r));
        }
    });
    safeArray(finished).forEach(r => {
        const key  = tKeyEff(r);
        const name = r.is_moved && r.moved_to ? String(r.moved_to) : (r.DisplayTableName || r.TableID || '-');
        get(key, name).done++;
    });
    return Array.from(map.values());
}

/* ── Table Card ── */
function cardCls(g){
    if(g.isEmpty)          return 's-empty';
    if(g.pending === 0)    return 's-done';
    if(g.worst >= T_RED)   return 's-red';
    if(g.worst >= T_YELLOW)return 's-yellow';
    return '';
}
function buildCard(g){
    if(g.isEmpty) return `<div class="table-card s-empty">
        <div class="tc-name">${esc(String(g.name))}</div>
        <div class="tc-badge empty">ว่าง</div>
    </div>`;
    const cls = cardCls(g);
    const badges = [];
    if(g.pending > 0) badges.push(`<div class="tc-badge kitchen">🍳 ${g.pending} กำลังทำ</div>`);
    if(g.done    > 0) badges.push(`<div class="tc-badge done">✅ ${g.done} เสร็จแล้ว</div>`);
    return `<div class="table-card ${cls}" data-key="${esc(g.key)}" data-name="${esc(String(g.name))}">
        <div class="tc-name">${esc(String(g.name))}</div>
        ${badges.join('')}
    </div>`;
}
function renderGrid(){
    const wrap = document.getElementById('tableGrid');
    const groups = groupTables(byZone(state.active), byZone(state.finished));
    const seen   = new Set(groups.map(g => g.key));

    if(state.zoneTables){
        state.zoneTables.forEach(tid => {
            if(!seen.has(tid)) groups.push({key:tid,name:tid,pending:0,done:0,worst:0,isEmpty:true});
        });
        groups.sort((a,b) => {
            if(a.isEmpty !== b.isEmpty) return a.isEmpty ? 1 : -1;
            return String(a.name).localeCompare(String(b.name),'th');
        });
    }

    if(!groups.length){
        wrap.innerHTML = '<div class="modal-msg" style="grid-column:1/-1">ไม่มีโต๊ะที่มีออเดอร์</div>';
        return;
    }
    wrap.innerHTML = groups.map(buildCard).join('');
}

/* ── Modal ── */
function getTransactionId(key){
    const row = safeArray(state.active).find(r => tKeyEff(r) === key)
             || safeArray(state.finished).find(r => tKeyEff(r) === key);
    return row && row.TransactionID ? parseInt(row.TransactionID, 10) : 0;
}
function getOrderDate(key){
    const row = safeArray(state.active).find(r => tKeyEff(r) === key)
             || safeArray(state.finished).find(r => tKeyEff(r) === key);
    return row && row.OrderDate ? String(row.OrderDate).slice(0,10) : '';
}
function buildRow(row, printerSet){
    const st       = parseInt(row.ProcessStatus, 10);
    const autoDone = nonKds(row, printerSet);
    const moved    = !!row.is_moved;
    const done     = st === PS_DONE || autoDone;
    const voided   = !autoDone && !moved && st === PS_VOIDED;
    const cls    = moved ? 'r-voided' : done ? 'r-done' : voided ? 'r-voided' : 'r-active';
    const lbl    = moved ? `🔄 ย้ายโต๊ะ → ${esc(row.moved_to||'')}` : done ? '✅ เสร็จแล้ว' : voided ? '🚫 ยกเลิก' : '🍳 กำลังทำ';
    const name = row.parent_name
        ? `${esc(row.parent_name)} · ${esc(row.ProductName||'-')}`
        : esc(row.ProductName||'-');
    const time = done
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
function openModal(key, name){
    if(_modalController) _modalController.abort();
    _modalController = new AbortController();
    const msig = _modalController.signal;

    document.getElementById('modalTitle').textContent = 'โต๊ะ ' + name;
    document.getElementById('modalSub').textContent   = '';
    document.getElementById('modalBody').innerHTML    = '<div class="modal-msg">กำลังโหลด...</div>';
    document.getElementById('tableModal').classList.add('open');
    document.body.style.overflow = 'hidden';

    const txId    = getTransactionId(key);
    const txParam = txId > 0 ? '&transaction_id=' + txId : '';
    const od      = getOrderDate(key);
    const odParam = !txParam && od ? '&order_date=' + encodeURIComponent(od) : '';
    fetch('api_checker.php?action=list_table_orders&table_id=' + encodeURIComponent(key) + txParam + odParam + cidParam + '&_=' + Date.now(), {cache:'no-store', signal:msig})
        .then(r => r.json())
        .then(json => {
            if(!json.success) throw new Error(json.error||'error');
            const rows       = safeArray(json.rows);
            const pids       = Array.isArray(json.allowed_printer_ids) ? json.allowed_printer_ids : [];
            const printerSet = pids.length > 0 ? new Set(pids.map(Number)) : null;
            const nDone   = rows.filter(r => !r.is_moved&&(parseInt(r.ProcessStatus,10)===PS_DONE||nonKds(r,printerSet))).length;
            const nActive = rows.filter(r => { const s=parseInt(r.ProcessStatus,10); return !r.is_moved&&!nonKds(r,printerSet)&&s!==PS_DONE&&s!==PS_VOIDED; }).length;
            const nVoid   = rows.filter(r => !r.is_moved&&!nonKds(r,printerSet)&&parseInt(r.ProcessStatus,10)===PS_VOIDED).length;
            document.getElementById('modalSub').textContent = `✅ เสร็จ ${nDone}  ·  🍳 กำลังทำ ${nActive}  ·  🚫 ยกเลิก ${nVoid}`;
            document.getElementById('modalBody').innerHTML  = rows.length
                ? rows.map(r => buildRow(r, printerSet)).join('')
                : '<div class="modal-msg">ไม่มีออเดอร์วันนี้</div>';
        })
        .catch(err => {
            if(err.name === 'AbortError') return;
            document.getElementById('modalBody').innerHTML = '<div class="modal-msg">โหลดไม่สำเร็จ</div>';
            console.error(err);
        });
}
function closeModal(){
    document.getElementById('tableModal').classList.remove('open');
    document.body.style.overflow = '';
}

/* ── Zones ── */
async function loadZones(){
    try {
        const res  = await fetch('api_checker.php?action=list_zones' + cidParam + '&_=' + Date.now(), {cache:'no-store'});
        const json = await res.json();
        if(!json.success || !safeArray(json.zones).length) return;
        state.zones = safeArray(json.zones);
        const inner = document.getElementById('zoneInner');
        inner.innerHTML = `<button class="btn-zone active" data-zid="">ทั้งหมด</button>`
            + state.zones.map(z => `<button class="btn-zone" data-zid="${esc(String(z.ZoneID))}">${esc(z.ZoneName||String(z.ZoneID))}</button>`).join('');
        document.getElementById('zoneBar').classList.remove('hidden');
    } catch(e){ console.error('loadZones',e); }
}
async function setZone(zid){
    state.zoneId = zid||null;
    document.querySelectorAll('.btn-zone').forEach(b => b.classList.toggle('active', b.dataset.zid===(zid||'')));
    if(zid){
        try {
            const res  = await fetch('api_checker.php?action=list_tables_in_zone&zone_id='+encodeURIComponent(zid)+cidParam+'&_='+Date.now(),{cache:'no-store'});
            const json = await res.json();
            state.zoneTables = json.success ? new Set(safeArray(json.tables).map(t=>String(t.TableID))) : null;
        } catch(e){ state.zoneTables=null; }
    } else {
        state.zoneTables = null;
    }
    renderGrid();
}

/* ── Data ── */
function setDot(s){ document.getElementById('statusDot').className='status-dot'+(s?' '+s:''); }
let _loadController  = null;
let _modalController = null;
async function loadAll(){
    if(_loadController) _loadController.abort();
    _loadController = new AbortController();
    const sig = _loadController.signal;
    setDot('loading');
    try {
        const [ar,fr] = await Promise.all([
            fetch('api_checker.php?action=list_active'  +cidParam+'&_='+Date.now(),{cache:'no-store',signal:sig}).then(r=>r.json()),
            fetch('api_checker.php?action=list_finished'+cidParam+'&_='+Date.now(),{cache:'no-store',signal:sig}).then(r=>r.json()),
        ]);
        if(sig.aborted) return;
        if(!ar.success) throw new Error(ar.error);
        if(!fr.success) throw new Error(fr.error);
        state.active   = safeArray(ar.active_rows);
        state.finished = safeArray(fr.recent_finished_rows);
        const pids = Array.isArray(ar.allowed_printer_ids) ? ar.allowed_printer_ids : [];
        state.allowedPrinters = pids.length > 0 ? new Set(pids.map(Number)) : null;
        setDot('');
        const pending = state.active.filter(r=>!r.is_voided&&!r.is_moved&&!r.is_combined&&!isNonKds(r)).length;
        document.title = pending > 0 ? `(${pending}) Staff Display` : 'Staff Display';
        renderGrid();
    } catch(e){
        if(e.name === 'AbortError') return;
        setDot('error'); console.error(e);
    }
}

/* ── Events ── */
document.getElementById('tableGrid').addEventListener('click', e => {
    const c = e.target.closest('.table-card:not(.s-empty)');
    if(c) openModal(c.dataset.key, c.dataset.name);
});
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('tableModal').addEventListener('click', e => { if(e.target===e.currentTarget) closeModal(); });
document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });
document.getElementById('refreshBtn').addEventListener('click', loadAll);
document.getElementById('zoneInner').addEventListener('click', e => {
    const b = e.target.closest('.btn-zone');
    if(b) setZone(b.dataset.zid||'');
});
document.addEventListener('visibilitychange', () => { if(!document.hidden) loadAll(); });

/* ── Fullscreen ── */
(function(){
    const btn = document.getElementById('fsBtn');
    function upd(){
        const f = !!document.fullscreenElement;
        btn.querySelector('.fs-enter').style.display = f?'none':'';
        btn.querySelector('.fs-exit').style.display  = f?'':'none';
    }
    btn.addEventListener('click', () => {
        document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen();
    });
    document.addEventListener('fullscreenchange', upd);
    upd();
})();

/* ── Init ── */
loadAll();
loadZones();
setInterval(() => { if(!document.hidden) loadAll(); }, REFRESH_MS);
</script>
</body>
</html>
