<?php require_once __DIR__ . '/config.php'; require_once __DIR__ . '/auth_check.php'; $machineDisplayName = function_exists('getMachineDisplayName') ? getMachineDisplayName() : ''; ?>
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
        :root {
            --bg:#edf5ff;
            --bg-2:#fff7ed;
            --surface:#ffffff;
            --surface-soft:#f8fbff;
            --text:#122033;
            --muted:#6b7a90;
            --line:#dbe8f7;
            --primary:#1683ff;
            --primary-dark:#0f69cf;
            --secondary:#ff8a1f;
            --secondary-soft:#fff1e4;
            --success:#12a150;
            --success-soft:#e6f8ee;
            --danger:#e44c3a;
            --danger-soft:#ffe8e4;
            --warn-yellow:#f59e0b;
            --warn-yellow-soft:#fffbeb;
            --shadow:0 8px 24px rgba(15,23,42,.13);
            --shadow-card:0 4px 16px rgba(15,23,42,.10);
            --radius:18px;
            --radius-sm:10px;
        }
        *{box-sizing:border-box;-webkit-tap-highlight-color:transparent;margin:0;padding:0}
        html,body{height:100%;overflow:hidden}
        body{
            font-family:Tahoma,'Sarabun',Arial,sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left, rgba(22,131,255,.12), transparent 28%),
                radial-gradient(circle at top right, rgba(255,138,31,.14), transparent 24%),
                linear-gradient(180deg, var(--bg), var(--bg-2));
            display:flex;flex-direction:column;
        }

        /* ── Topbar ── */
        .topbar{
            flex-shrink:0;
            padding:7px 14px;
            backdrop-filter:blur(12px);
            background:linear-gradient(135deg, rgba(8,58,112,.95), rgba(22,131,255,.92), rgba(255,138,31,.88));
            color:#fff;
            box-shadow:0 4px 16px rgba(8,58,112,.22);
            z-index:30;
        }
        .topbar-inner{
            max-width:100%;
            display:flex;align-items:center;gap:10px;justify-content:space-between;flex-wrap:nowrap;
        }
        .topbar-left{display:flex;align-items:center;gap:8px;flex-shrink:0}
        .brand-title{font-size:18px;font-weight:bold;white-space:nowrap;letter-spacing:.3px}
        .brand-sub{font-size:11px;opacity:.88;white-space:nowrap}
        .machine-chip{
            display:inline-flex;align-items:center;padding:2px 10px;border-radius:999px;
            background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.2);font-size:11px;font-weight:bold
        }
        .topbar-stats{display:flex;gap:6px;align-items:center}
        .tstat{
            display:flex;flex-direction:column;align-items:center;
            background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.18);
            border-radius:8px;padding:3px 11px;min-width:52px;
        }
        .tstat-num{font-size:17px;font-weight:bold;line-height:1.1}
        .tstat-label{font-size:10px;opacity:.85;white-space:nowrap}
        .tstat.highlight{background:rgba(255,255,255,.22)}
        .topbar-right{display:flex;align-items:center;gap:8px;flex-shrink:0}
        .topbar-time{font-size:13px;opacity:.9;white-space:nowrap}
        .topbar-status{display:flex;align-items:center;gap:5px;font-size:12px;background:rgba(255,255,255,.14);border-radius:999px;padding:3px 11px}
        .status-dot{width:7px;height:7px;border-radius:50%;background:#4ade80}
        .status-dot.loading{background:var(--secondary);animation:pulse 1s infinite}
        .status-dot.error{background:#f87171}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

        .btn{
            appearance:none;border:none;cursor:pointer;border-radius:9px;font-weight:bold;font-size:12px;
            padding:5px 12px;min-height:32px;touch-action:manipulation;
            transition:transform .1s,opacity .1s;
        }
        .btn:active{transform:scale(.97)}
        .btn-ghost{background:rgba(255,255,255,.16);color:#fff;border:1px solid rgba(255,255,255,.18)}
        .btn-ghost.active{background:#fff;color:var(--primary-dark)}
        .btn-icon{background:rgba(255,255,255,.16);color:#fff;border:1px solid rgba(255,255,255,.18);min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center}
        .btn-icon svg{width:14px;height:14px}

        /* ── Filter bar ── */
        .filterbar{
            flex-shrink:0;
            display:flex;align-items:center;gap:8px;
            padding:6px 14px;
            background:linear-gradient(180deg,rgba(255,255,255,.72),rgba(248,251,255,.68));
            border-bottom:1px solid var(--line);
            backdrop-filter:blur(8px);
        }
        .filter-label{font-size:12px;color:var(--muted);font-weight:bold;white-space:nowrap}
        .btn-filter{background:rgba(22,131,255,.08);color:var(--primary-dark);border:1px solid var(--line);border-radius:999px;padding:4px 14px;font-size:12px;font-weight:bold;cursor:pointer;transition:all .15s}
        .btn-filter.active{background:var(--primary);color:#fff;border-color:var(--primary)}
        .btn-filter:active{transform:scale(.97)}

        /* ── KDS Board ── */
        .kds-board{
            flex:1;
            overflow-x:auto;
            overflow-y:hidden;
            padding:10px 12px 12px;
            display:flex;
            gap:9px;
            align-items:flex-start;
        }
        .kds-board::-webkit-scrollbar{height:6px}
        .kds-board::-webkit-scrollbar-track{background:rgba(22,131,255,.06);border-radius:3px}
        .kds-board::-webkit-scrollbar-thumb{background:rgba(22,131,255,.25);border-radius:3px}

        .kds-empty{
            flex:1;display:flex;align-items:center;justify-content:center;
            font-size:16px;font-weight:bold;color:var(--muted);padding:40px;
        }

        /* ── KDS Card ── */
        .kds-card{
            flex-shrink:0;
            width:210px;
            border-radius:var(--radius);
            background:var(--surface);
            box-shadow:var(--shadow-card);
            overflow:hidden;
            border:1px solid rgba(255,255,255,.7);
            display:flex;flex-direction:column;
            max-height:calc(100vh - 120px);
        }
        .kds-card-head{
            padding:10px 12px 9px;
            display:flex;justify-content:space-between;align-items:flex-start;
            flex-shrink:0;
        }
        .kds-card-head.state-active{background:linear-gradient(135deg,#b02a1e,var(--danger))}
        .kds-card-head.state-yellow{background:linear-gradient(135deg,#b45309,var(--warn-yellow))}
        .kds-card-head.state-ready{background:linear-gradient(135deg,#0b7a3e,var(--success))}
        .kds-card-head.state-voided{background:linear-gradient(135deg,#374151,#6b7280)}
        .kds-card-head.state-moved{background:linear-gradient(135deg,#1d4ed8,#3b82f6)}
        .kds-card-head.state-combined{background:linear-gradient(135deg,#6d28d9,#8b5cf6)}
        .card-head-left{}
        .card-table{font-size:18px;font-weight:bold;color:#fff;line-height:1.1}
        .card-queue{font-size:11px;color:rgba(255,255,255,.82);margin-top:2px}
        .card-time-in{font-size:11px;color:rgba(255,255,255,.78);margin-top:1px}
        .card-head-right{display:flex;flex-direction:column;align-items:flex-end;gap:4px}
        .card-wait-badge{
            font-size:12px;font-weight:bold;
            background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.28);
            border-radius:999px;padding:2px 9px;color:#fff;white-space:nowrap;
        }
        .card-wait-badge.urgent{background:rgba(0,0,0,.25)}
        .card-salemode{font-size:10px;color:rgba(255,255,255,.75);text-align:right}

        .kds-card-body{
            flex:1;
            overflow-y:auto;
            padding:10px 12px;
        }
        .kds-card-body::-webkit-scrollbar{width:3px}
        .kds-card-body::-webkit-scrollbar-thumb{background:var(--line);border-radius:2px}

        .card-status-badge{
            display:inline-flex;align-items:center;gap:4px;
            padding:3px 10px;border-radius:999px;font-size:11px;font-weight:bold;margin-bottom:7px;
        }
        .card-status-badge.ready{background:var(--success-soft);color:#0b5e30;border:1px solid #bfeacc}
        .card-status-badge.voided{background:#f3f4f6;color:#374151;border:1px solid #d1d5db}
        .card-status-badge.moved{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe}
        .card-status-badge.combined{background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe}

        .card-parent-label{
            display:inline-block;margin-bottom:4px;font-size:10px;font-weight:bold;
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            color:#fff;padding:2px 9px;border-radius:999px;
        }
        .card-product-name{font-size:15px;font-weight:bold;line-height:1.3;word-break:break-word;color:var(--text)}
        .card-product-name.striked{text-decoration:line-through;color:var(--success)}
        .card-qty{
            display:inline-flex;align-items:center;justify-content:center;
            min-width:36px;min-height:36px;
            border-radius:10px;
            background:linear-gradient(135deg,var(--secondary-soft),#fff);
            color:#b35e00;border:1px solid #ffd8b0;
            font-size:16px;font-weight:bold;
            float:right;margin-left:8px;
        }
        .card-qty.ready{background:linear-gradient(135deg,var(--success-soft),#fff);border-color:#bfeacc;color:#0b5e30}

        .card-comment-list{margin-top:6px;display:flex;flex-direction:column;gap:4px}
        .card-comment{
            font-size:11px;padding:4px 8px;border-radius:6px;line-height:1.45;
            background:var(--secondary-soft);border-left:3px solid var(--secondary);color:#7a4200;
        }
        .card-comment.priced{background:#fff1f0;border-left-color:var(--danger);color:#7a1500}

        .card-divider{border:none;border-top:1px solid var(--line);margin:8px 0}
        .card-orderline{font-size:10.5px;color:var(--muted);line-height:1.6}

        .kds-card-foot{
            flex-shrink:0;
            padding:7px 12px;
            background:var(--surface-soft);
            border-top:1px solid var(--line);
            display:grid;grid-template-columns:1fr 1fr;gap:2px 8px;
        }
        .foot-row{display:flex;flex-direction:column}
        .foot-label{font-size:10px;color:var(--muted)}
        .foot-val{font-size:11px;font-weight:bold;color:var(--text);word-break:break-all}
        .foot-timer{font-size:13px;font-weight:bold}
        .foot-timer.normal{color:var(--success)}
        .foot-timer.warn{color:var(--warn-yellow)}
        .foot-timer.urgent{color:var(--danger)}

        /* ── Finished panel (right side drawer) ── */
        .finished-col{
            flex-shrink:0;width:240px;
            background:rgba(255,255,255,.88);
            border:1px solid rgba(255,255,255,.75);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            display:flex;flex-direction:column;
            max-height:calc(100vh - 120px);
            overflow:hidden;
        }
        .finished-head{
            padding:9px 12px;border-bottom:1px solid var(--line);
            background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(248,251,255,.9));
            flex-shrink:0;
        }
        .finished-head-title{font-size:14px;font-weight:bold;color:#0f2945}
        .finished-head-count{
            display:inline-flex;align-items:center;margin-top:4px;
            background:var(--secondary-soft);color:#9a5200;
            border-radius:999px;padding:1px 10px;font-size:11px;font-weight:bold
        }
        .finished-list{flex:1;overflow-y:auto;padding:8px}
        .finished-list::-webkit-scrollbar{width:3px}
        .finished-list::-webkit-scrollbar-thumb{background:var(--line);border-radius:2px}
        .finished-item{
            border:1px solid var(--line);border-radius:12px;padding:9px 10px;
            background:#fff;margin-bottom:7px;
        }
        .fi-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px}
        .fi-name{font-size:13px;font-weight:bold;line-height:1.3;word-break:break-word}
        .fi-qty{font-size:15px;font-weight:bold;color:var(--success)}
        .fi-meta{font-size:10.5px;color:var(--muted);line-height:1.6}

        /* ── Item rows inside card ── */
        .item-row{padding:6px 0}
        .item-row-top{display:flex;align-items:flex-start;gap:8px}
        .item-name-block{flex:1;min-width:0}
        .item-badge{
            display:inline-flex;align-items:center;
            padding:1px 8px;border-radius:999px;font-size:10px;font-weight:bold;margin-top:3px;
        }
        .item-badge.ready{background:var(--success-soft);color:#0b5e30;border:1px solid #bfeacc}
        .item-badge.voided{background:#f3f4f6;color:#374151;border:1px solid #d1d5db}
        .item-badge.moved{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe}
        .item-badge.combined{background:#f5f3ff;color:#6d28d9;border:1px solid #ddd6fe}
        .item-divider{border:none;border-top:1px dashed var(--line);margin:2px 0}
        .fi-product{font-size:12px;color:var(--text);padding:2px 0;display:flex;align-items:baseline;gap:5px}
        .fi-qty-small{font-size:11px;font-weight:bold;color:var(--success);white-space:nowrap}

        .hidden{display:none !important}
        .panel-empty{padding:20px 14px;text-align:center;color:var(--muted);font-size:13px;font-weight:bold}

        /* ── Fullscreen btn ── */
        .btn-fullscreen{
            display:inline-flex;align-items:center;gap:4px;padding:4px 10px;
            border-radius:8px;border:none;cursor:pointer;font-size:12px;font-weight:bold;
            background:rgba(255,255,255,.16);color:#fff;border:1px solid rgba(255,255,255,.18);
            transition:background .15s,transform .1s;flex-shrink:0;
        }
        .btn-fullscreen:hover{background:rgba(255,255,255,.28)}
        .btn-fullscreen:active{transform:scale(.97)}
        .btn-fullscreen svg{width:13px;height:13px;flex-shrink:0}

        @media(max-width:600px){
            .topbar-stats{display:none}
            .kds-card{width:175px}
            .finished-col{width:175px}
        }
    </style>
</head>
<body>

<!-- ── Topbar ── -->
<div class="topbar">
    <div class="topbar-inner">
        <div class="topbar-left">
            <img src="logo.svg" alt="Staff Display" style="width:36px;height:36px;border-radius:9px;flex-shrink:0">
            <div>
                <div class="brand-title">Staff Display</div>
                <div class="brand-sub">
                    <?php echo h(APP_TITLE); ?>
                    <?php if ($machineDisplayName !== ''): ?>
                    &nbsp;·&nbsp;<span class="machine-chip"><?php echo h($machineDisplayName); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="topbar-stats">
            <div class="tstat">
                <div class="tstat-num" id="statActiveRows">0</div>
                <div class="tstat-label">คิวค้าง</div>
            </div>
            <div class="tstat">
                <div class="tstat-num" id="statActiveQty">0</div>
                <div class="tstat-label">รายการค้าง</div>
            </div>
            <div class="tstat highlight">
                <div class="tstat-num" id="statFinishedRows">0</div>
                <div class="tstat-label">พร้อมเสิร์ฟ</div>
            </div>
        </div>

        <div class="topbar-right">
            <div class="topbar-status">
                <div class="status-dot loading" id="statusDot"></div>
                <span id="statusText">กำลังโหลด</span>
            </div>
            <button type="button" class="btn btn-ghost" id="refreshBtn">↺ รีเฟรช</button>
            <button type="button" class="btn-fullscreen" id="fsBtn" title="เต็มจอ">
                <svg class="fs-ico-enter" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
                <svg class="fs-ico-exit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" style="display:none"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/></svg>
                เต็มจอ
            </button>
        </div>
    </div>
</div>

<!-- ── Filter bar ── -->
<div class="filterbar">
    <span class="filter-label">แสดง:</span>
    <button type="button" class="btn-filter active" data-filter="all">ทั้งหมด</button>
    <button type="button" class="btn-filter" data-filter="active">คิวค้าง</button>
    <button type="button" class="btn-filter" data-filter="ready">พร้อมเสิร์ฟ</button>
</div>

<!-- ── KDS Board ── -->
<div class="kds-board" id="kdsBoard">
    <div class="kds-empty">กำลังโหลดข้อมูล...</div>
</div>

<script>
const REFRESH_MS = <?php echo (int)APP_REFRESH_MS; ?>;
const thresholdYellow = <?php echo (int)(defined('ALERT_THRESHOLD_YELLOW_DEFAULT') ? ALERT_THRESHOLD_YELLOW_DEFAULT : 10); ?>;
const thresholdRed    = <?php echo (int)(defined('ALERT_THRESHOLD_RED_DEFAULT')    ? ALERT_THRESHOLD_RED_DEFAULT    : 20); ?>;
const PAGE_CID = <?php echo (int)(isset($_GET['cid']) && (int)$_GET['cid'] > 0 ? (int)$_GET['cid'] : 0); ?>;
const cidParam = PAGE_CID > 0 ? '&cid=' + PAGE_CID : '';
const endpointActive   = 'api_checker.php?action=list_active'   + cidParam;
const endpointFinished = 'api_checker.php?action=list_finished' + cidParam;

const state = {
    stats: { active_rows:0, active_qty:0, recent_finished_rows:0 },
    active_rows: [],
    recent_finished_rows: [],
    filter: 'all'
};

function safeArray(v){ return Array.isArray(v) ? v : []; }

function esc(str){
    return String(str ?? '').replace(/[&<>"']/g, function(m){
        return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[m];
    });
}

function fmtQty(v){
    const n = Number(v || 0);
    return Number.isInteger(n) ? String(n) : n.toFixed(2).replace(/\.00$/,'');
}

function fmtTime(value){
    if (!value) return '-';
    const d = new Date(String(value).replace(' ','T'));
    if (isNaN(d)) return String(value).slice(11,16) || String(value);
    return d.toLocaleTimeString('th-TH',{hour12:false,hour:'2-digit',minute:'2-digit'});
}

function fmtDateTime(value){
    if (!value) return '-';
    const d = new Date(String(value).replace(' ','T'));
    if (isNaN(d)) return String(value);
    return d.toLocaleString('th-TH',{hour12:false,year:'numeric',month:'2-digit',day:'2-digit',hour:'2-digit',minute:'2-digit'});
}

function calcWait(row){
    if (!row || !row.SubmitOrderDateTime) return 0;
    const d = new Date(String(row.SubmitOrderDateTime).replace(' ','T'));
    if (isNaN(d)) return 0;
    return Math.max(0, Math.floor((Date.now() - d.getTime()) / 60000));
}

function fmtWait(min){
    if (min < 60) return min + ' นาที';
    const h = Math.floor(min/60), m = min%60;
    return h + 'ชม.' + (m > 0 ? m+'น.' : '');
}

function renderComments(comments){
    const items = safeArray(comments).filter(Boolean);
    if (!items.length) return '';
    const normal = [], priced = [];
    items.forEach(function(c){
        const amt = Number(c.amount || 0);
        const lbl = amt > 1 ? `${esc(c.text||'-')} x${fmtQty(amt)}` : esc(c.text||'-');
        if (c.is_priced) priced.push(lbl); else normal.push(lbl);
    });
    let out = '<div class="card-comment-list">';
    if (normal.length) out += `<div class="card-comment">💬 ${normal.join(', ')}</div>`;
    if (priced.length) out += `<div class="card-comment priced">💰 ${priced.join(', ')}</div>`;
    return out + '</div>';
}

// group rows by TableID+OrderNo → 1 card per table
function groupByTable(rows){
    const map = {};
    const order = [];
    safeArray(rows).forEach(function(row){
        const key = String(row.TableID || '');
        if (!map[key]) {
            map[key] = {
                tableText: row.DisplayTableName || row.TableID || '-',
                tableID: row.TableID,
                orderNo: row.OrderNo || '-',
                saleModeName: row.SaleModeName || '-',
                submitTime: row.SubmitOrderDateTime,
                items: []
            };
            order.push(key);
        }
        if (row.SubmitOrderDateTime && row.SubmitOrderDateTime < map[key].submitTime) {
            map[key].submitTime = row.SubmitOrderDateTime;
        }
        map[key].items.push(row);
    });
    return order.map(function(k){ return map[k]; });
}

function tableHeadState(items){
    // worst-case priority: voided < moved < combined < active < yellow < red < ready
    let hasReady = false, hasUrgent = false, hasWarn = false,
        hasVoided = false, hasMoved = false, hasCombined = false;
    items.forEach(function(row){
        if (row.is_voided)   hasVoided   = true;
        else if (row.is_moved)    hasMoved    = true;
        else if (row.is_combined) hasCombined = true;
        else {
            const w = calcWait(row);
            if (w >= thresholdRed)    hasUrgent = true;
            else if (w >= thresholdYellow) hasWarn   = true;
            else hasReady = true; // normal active
        }
    });
    if (hasUrgent)   return { state:'state-active', timerClass:'urgent', waitClass:'urgent' };
    if (hasWarn)     return { state:'state-yellow', timerClass:'warn',   waitClass:'warn'   };
    if (hasMoved)    return { state:'state-moved',  timerClass:'normal', waitClass:''       };
    if (hasCombined) return { state:'state-combined', timerClass:'normal', waitClass:''     };
    if (hasVoided)   return { state:'state-voided', timerClass:'normal', waitClass:''       };
    return           { state:'state-active', timerClass:'normal', waitClass:'' };
}

function buildItemRow(row, isReady){
    const isVoided   = !!row.is_voided;
    const isMoved    = !!row.is_moved;
    const isCombined = !!row.is_combined;
    let badge = '';
    if (isReady)    badge = `<span class="item-badge ready">✓ เสร็จ</span>`;
    if (isVoided)   badge = `<span class="item-badge voided">🚫 ยกเลิก</span>`;
    if (isMoved)    badge = `<span class="item-badge moved">🔀 ย้าย</span>`;
    if (isCombined) badge = `<span class="item-badge combined">🔗 รวม</span>`;

    const qtyClass = isReady ? 'ready' : '';
    const nameClass = isReady ? 'striked' : '';

    return `
    <div class="item-row">
        <div class="item-row-top">
            <div class="card-qty ${qtyClass}">x${fmtQty(row.ProductAmount)}</div>
            <div class="item-name-block">
                ${row.parent_name ? `<div class="card-parent-label">${esc(row.parent_name)}</div>` : ''}
                <div class="card-product-name ${nameClass}">${esc(row.ProductName||'-')}</div>
                ${badge}
            </div>
        </div>
        ${renderComments(row.comments||[])}
    </div>`;
}

function buildTableCard(group, isReady){
    const { state: headState, timerClass, waitClass } = tableHeadState(group.items);
    const waitMin = calcWait({ SubmitOrderDateTime: group.submitTime });

    const waitBadgeHtml = (!isReady)
        ? `<div class="card-wait-badge ${waitClass}">⏱ ${fmtWait(waitMin)}</div>`
        : `<div class="card-wait-badge">✓ เสร็จ</div>`;

    const itemsHtml = group.items.map(function(row){ return buildItemRow(row, isReady); }).join('<hr class="item-divider">');

    return `
    <article class="kds-card">
        <div class="kds-card-head ${headState}">
            <div class="card-head-left">
                <div class="card-table">โต๊ะ ${esc(group.tableText)}</div>
                <div class="card-queue">Order ${esc(group.orderNo)}</div>
                <div class="card-time-in">เข้า ${esc(fmtTime(group.submitTime))}</div>
            </div>
            <div class="card-head-right">
                ${waitBadgeHtml}
                <div class="card-salemode">${esc(group.saleModeName)}</div>
            </div>
        </div>
        <div class="kds-card-body">
            ${itemsHtml}
        </div>
        <div class="kds-card-foot">
            <div class="foot-row">
                <span class="foot-label">เวลารอ</span>
                <span class="foot-timer ${timerClass}">${isReady ? '✓ เสร็จแล้ว' : fmtWait(waitMin)}</span>
            </div>
            <div class="foot-row">
                <span class="foot-label">รายการ</span>
                <span class="foot-val">${group.items.length} รายการ</span>
            </div>
        </div>
    </article>`;
}

function buildFinishedItem(group){
    const tableText = group.tableText;
    return `
    <div class="finished-item">
        <div class="fi-top">
            <div>
                <div class="fi-name">โต๊ะ ${esc(tableText)}</div>
                <div class="fi-meta">Order ${esc(group.orderNo)} · ${esc(group.saleModeName)}</div>
            </div>
            <div class="fi-qty">${group.items.length} รายการ</div>
        </div>
        ${group.items.map(function(row){
            return `<div class="fi-product">
                <span class="fi-qty-small">x${fmtQty(row.ProductAmount)}</span>
                ${row.parent_name ? `<span class="card-parent-label">${esc(row.parent_name)}</span> ` : ''}
                ${esc(row.ProductName||'-')}
            </div>`;
        }).join('')}
        <div class="fi-meta" style="margin-top:5px">เสร็จเมื่อ ${esc(fmtDateTime(group.items[0] && group.items[0].FinishDateTime))}</div>
    </div>`;
}

function renderBoard(){
    const board = document.getElementById('kdsBoard');
    const activeRows   = state.filter === 'ready'  ? [] : safeArray(state.active_rows);
    const finishedRows = state.filter === 'active' ? [] : safeArray(state.recent_finished_rows);

    const activeGroups   = groupByTable(activeRows);
    const finishedGroups = groupByTable(finishedRows);

    let html = '';

    if (activeGroups.length) {
        activeGroups.forEach(function(g){ html += buildTableCard(g, false); });
    }

    if (state.filter !== 'active') {
        html += `
        <div class="finished-col">
            <div class="finished-head">
                <div class="finished-head-title">พร้อมเสิร์ฟแล้ว</div>
                <div class="finished-head-count" id="finishedCount">${finishedGroups.length} โต๊ะ</div>
            </div>
            <div class="finished-list" id="finishedList">
                ${finishedGroups.length
                    ? finishedGroups.map(buildFinishedItem).join('')
                    : '<div class="panel-empty">ยังไม่มีรายการ</div>'}
            </div>
        </div>`;
    }

    if (!activeGroups.length && state.filter !== 'ready') {
        html = `<div class="kds-empty">ไม่มีคิวค้างในตอนนี้ 🎉</div>` + (html || '');
    }
    if (!html) {
        html = `<div class="kds-empty">ไม่มีรายการที่ตรงกับตัวกรอง</div>`;
    }

    board.innerHTML = html;

    document.getElementById('statActiveRows').textContent  = activeGroups.length;
    document.getElementById('statActiveQty').textContent   = fmtQty(state.stats.active_qty || 0);
    document.getElementById('statFinishedRows').textContent = finishedGroups.length;
}

function syncFilters(){
    document.querySelectorAll('[data-filter]').forEach(function(btn){
        btn.classList.toggle('active', btn.getAttribute('data-filter') === state.filter);
    });
}

async function loadAll(){
    const dot  = document.getElementById('statusDot');
    const txt  = document.getElementById('statusText');
    dot.className = 'status-dot loading';
    txt.textContent = 'กำลังโหลด';
    try {
        const [aRes, fRes] = await Promise.all([
            fetch(endpointActive   + '&_=' + Date.now(), {cache:'no-store'}),
            fetch(endpointFinished + '&_=' + Date.now(), {cache:'no-store'})
        ]);
        const aJson = await aRes.json();
        const fJson = await fRes.json();
        if (!aRes.ok || !aJson.success) throw new Error(aJson.error || 'โหลดคิวไม่สำเร็จ');
        if (!fRes.ok || !fJson.success) throw new Error(fJson.error || 'โหลดรายการเสร็จไม่สำเร็จ');

        state.active_rows          = safeArray(aJson.active_rows || []);
        state.recent_finished_rows = safeArray(fJson.recent_finished_rows || []);
        state.stats.active_rows    = Number((aJson.stats||{}).active_rows || 0);
        state.stats.active_qty     = Number((aJson.stats||{}).active_qty  || 0);
        state.stats.recent_finished_rows = state.recent_finished_rows.length;

        dot.className = 'status-dot';
        txt.textContent = 'พร้อมใช้งาน';
        renderBoard();
    } catch(err) {
        dot.className = 'status-dot error';
        txt.textContent = 'เกิดข้อผิดพลาด';
        document.getElementById('kdsBoard').innerHTML = `<div class="kds-empty">โหลดข้อมูลไม่สำเร็จ — ${esc(err.message)}</div>`;
        console.error(err);
    }
}

document.querySelectorAll('[data-filter]').forEach(function(btn){
    btn.addEventListener('click', function(){
        state.filter = btn.getAttribute('data-filter') || 'all';
        syncFilters();
        renderBoard();
    });
});
document.getElementById('refreshBtn').addEventListener('click', loadAll);
document.addEventListener('visibilitychange', function(){ if (!document.hidden) loadAll(); });

loadAll();
setInterval(function(){ if (!document.hidden) loadAll(); }, REFRESH_MS);
</script>

<script>
(function(){
    var btn = document.getElementById('fsBtn');
    if (!btn) return;
    function upd(){
        var f = !!document.fullscreenElement;
        btn.querySelector('.fs-ico-enter').style.display = f ? 'none' : 'inline';
        btn.querySelector('.fs-ico-exit').style.display  = f ? 'inline' : 'none';
        btn.title = f ? 'ออกจากเต็มจอ' : 'เต็มจอ';
    }
    btn.addEventListener('click', function(){
        if (!document.fullscreenElement) document.documentElement.requestFullscreen().catch(function(){});
        else document.exitFullscreen().catch(function(){});
    });
    document.addEventListener('fullscreenchange', upd);
    upd();
})();
</script>
</body>
</html>
