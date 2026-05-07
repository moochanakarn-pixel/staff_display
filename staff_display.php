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
        :root{
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
            --shadow:0 12px 28px rgba(15, 23, 42, .10);
            --shadow-soft:0 8px 18px rgba(22, 131, 255, .08);
            --radius:22px;
        }
        *{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
        html,body{height:100%}
        body{
            margin:0;
            font-family:Tahoma, Arial, sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left, rgba(22,131,255,.12), transparent 28%),
                radial-gradient(circle at top right, rgba(255,138,31,.14), transparent 24%),
                linear-gradient(180deg, var(--bg), var(--bg-2));
        }
        .topbar{
            position:sticky;top:0;z-index:30;
            padding:8px 14px 7px;
            backdrop-filter:blur(12px);
            background:linear-gradient(135deg, rgba(8,58,112,.92), rgba(22,131,255,.88), rgba(255,138,31,.84));
            color:#fff;
            box-shadow:0 8px 20px rgba(8,58,112,.18);
        }
        .topbar-inner{
            max-width:1920px;margin:0 auto;
            display:flex;flex-wrap:wrap;align-items:center;gap:8px;justify-content:space-between;
        }
        .brand h1{margin:0;font-size:22px;line-height:1.1;letter-spacing:.2px;white-space:nowrap}
        .brand-sub{margin-top:4px;font-size:12px;opacity:.95;font-weight:bold;display:flex;gap:8px;flex-wrap:wrap;align-items:center}
        .machine-chip{
            display:inline-flex;align-items:center;min-height:24px;padding:0 10px;border-radius:999px;
            background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.18);font-size:11px;font-weight:700
        }
        .controls{display:flex;flex-wrap:wrap;gap:7px;align-items:center}
        .btn{
            appearance:none;border:none;border-radius:12px;min-height:36px;padding:0 12px;font-size:13px;font-weight:bold;
            cursor:pointer;touch-action:manipulation;transition:transform .12s ease,filter .12s ease,opacity .12s ease;
            box-shadow:var(--shadow-soft)
        }
        .btn:active{transform:scale(.985)}
        .btn-neutral{background:#eef4fb;color:#1f324a}
        .btn-primary{background:#fff;color:var(--primary-dark)}
        .btn-filter{background:rgba(255,255,255,.16);color:#fff;border:1px solid rgba(255,255,255,.16);box-shadow:none}
        .btn-filter.active{background:#fff;color:var(--primary-dark)}
        .stats{display:flex;gap:8px;padding:8px 14px 4px;max-width:1920px;margin:0 auto;flex-wrap:wrap}
        .stat{
            display:flex;align-items:center;gap:8px;
            background:rgba(255,255,255,.88);border:1px solid rgba(255,255,255,.72);border-radius:14px;
            padding:7px 14px;box-shadow:var(--shadow)
        }
        .stat-label{font-size:12px;color:var(--muted);white-space:nowrap}
        .stat-value{font-size:20px;font-weight:bold;line-height:1}
        .page{max-width:1920px;margin:0 auto;padding:6px 10px 16px}
        .layout{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(360px,.9fr);gap:12px;align-items:start}
        .panel{
            background:rgba(255,255,255,.92);border:1px solid rgba(255,255,255,.75);border-radius:20px;
            box-shadow:var(--shadow);overflow:hidden
        }
        .panel-head{
            display:flex;justify-content:space-between;align-items:center;gap:10px;
            padding:10px 14px;border-bottom:1px solid var(--line);
            background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(245,250,255,.9))
        }
        .panel-title{margin:0;font-size:18px;font-weight:bold;color:#0f2945}
        .panel-sub{margin-top:4px;font-size:12px;color:var(--muted);font-weight:700}
        .panel-badge{display:inline-flex;align-items:center;min-height:30px;padding:4px 12px;border-radius:999px;background:var(--secondary-soft);color:#9a5200;font-size:13px;font-weight:bold}
        .toolbar{display:flex;gap:8px;flex-wrap:wrap;padding:10px 14px;border-bottom:1px solid var(--line);background:rgba(248,251,255,.92)}
        .cards{display:grid;grid-template-columns:repeat(auto-fill, minmax(245px, 1fr));gap:8px;padding:10px}
        .card{
            background:linear-gradient(180deg,#fff,#fbfdff);border:1px solid var(--line);
            border-radius:16px;padding:10px;box-shadow:0 4px 12px rgba(17,56,92,.05)
        }
        .card.warn-yellow{border-color:#ffe066;background:linear-gradient(180deg,#fffde7,#fffbf0);box-shadow:0 0 0 3px rgba(255,214,0,.18)}
        .card.warn-red{border-color:#ffb3ab;background:linear-gradient(180deg,#fff2f0,#fff8f7);box-shadow:0 0 0 3px rgba(228,76,58,.14)}
        .card.ready-card{border-color:#bfeacc;background:linear-gradient(180deg,#f6fff9,#ecfdf5);box-shadow:0 0 0 3px rgba(18,161,80,.10)}
        .card.voided{border-color:#9ca3af;background:linear-gradient(180deg,#e5e7eb,#f3f4f6);box-shadow:none;opacity:.78}
        .card.voided .product-name,.card.voided .table-name{color:#6b7280}
        .card.voided .qty-badge{background:#d1d5db;border-color:#9ca3af;color:#6b7280}
        .card.moved{border-color:#93c5fd;background:linear-gradient(180deg,#eff6ff,#f5f9ff);box-shadow:0 0 0 3px rgba(59,130,246,.12)}
        .card.combined{border-color:#a78bfa;background:linear-gradient(180deg,#f5f3ff,#faf9ff);box-shadow:0 0 0 3px rgba(139,92,246,.12)}
        .status-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:bold;margin-bottom:6px}
        .status-badge.voided{background:#6b7280;color:#fff}
        .status-badge.moved{background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff}
        .status-badge.combined{background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff}
        .status-badge.ready{background:linear-gradient(135deg,#12a150,#0f8c45);color:#fff}
        .card-head{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px;align-items:start;margin-bottom:8px}
        .table-name{font-size:20px;font-weight:bold;line-height:1.1;word-break:break-word}
        .order-line{margin-top:3px;color:var(--muted);font-size:11px;line-height:1.4}
        .qty-badge{min-width:52px;min-height:52px;display:flex;align-items:center;justify-content:center;padding:6px;border-radius:14px;background:linear-gradient(135deg,var(--secondary-soft),#fff);color:#b35e00;border:1px solid #ffd8b0;font-size:22px;font-weight:bold}
        .qty-badge.ready{background:linear-gradient(135deg,var(--success-soft),#fff);border-color:#bfeacc;color:#11783c}
        .product-block{margin:0 0 8px}
        .product-name{margin:0;font-size:17px;line-height:1.2;word-break:break-word;font-weight:bold}
        .parent-name-label{display:inline-block;margin-bottom:3px;font-size:11px;font-weight:bold;color:#fff;background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:2px 8px;border-radius:999px;letter-spacing:.3px}
        .queue-tags{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px}
        .tag{display:inline-flex;align-items:center;min-height:26px;padding:4px 9px;border-radius:999px;font-size:11px;font-weight:bold;background:#eef6ff;color:#1758a5;border:1px solid #d5e7ff}
        .tag.wait{background:var(--secondary-soft);color:#9f5200;border-color:#ffd2a4}
        .tag.good{background:var(--success-soft);color:#11783c;border-color:#bfeacc}
        .tag.urgent{background:#ffe8e4;color:#b33023;border-color:#ffb3ab}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px;margin-bottom:8px}
        .field{padding:7px 10px;border-radius:12px;background:var(--surface-soft);border:1px solid #e7f0fa}
        .field-label{font-size:11px;color:var(--muted);margin-bottom:2px}
        .field-value{font-size:13px;font-weight:bold;word-break:break-word}
        .comment-list{display:flex;flex-direction:column;gap:6px;margin-top:6px}
        .comment-group{display:flex;flex-wrap:wrap;align-items:flex-start;gap:6px;padding:7px 10px;border-radius:12px;border:1px solid #ffe0bc;background:#fff8f0;color:#6a3900;font-size:12px;line-height:1.4}
        .comment-group.priced{background:#fff1e4;border-color:#ffc792}
        .comment-group-label{font-weight:bold;white-space:nowrap;color:#9a5200}
        .comment-group-items{flex:1 1 auto;min-width:0;word-break:break-word}
        .side-stack{display:grid;gap:12px}
        .finished-list{display:flex;flex-direction:column;gap:10px;padding:12px;max-height:calc(100vh - 240px);overflow:auto}
        .finished-item{border:1px solid var(--line);border-radius:16px;padding:11px;background:#fff}
        .finished-top{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px;align-items:start;margin-bottom:7px}
        .finished-name{font-size:16px;font-weight:bold;line-height:1.25}
        .finished-qty{font-size:20px;font-weight:bold;color:var(--success)}
        .finished-meta{font-size:12px;color:var(--muted);line-height:1.5;margin-bottom:8px}
        .mini-summary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;padding:12px}
        .summary-box{background:#f8fbff;border:1px solid #dbe8f7;border-radius:14px;padding:10px 12px}
        .summary-box-label{font-size:11px;color:var(--muted);font-weight:700}
        .summary-box-value{font-size:22px;font-weight:700;color:#0f2945;margin-top:4px}
        .empty{padding:24px 14px;text-align:center;color:var(--muted);font-size:14px;font-weight:700}
        .hidden{display:none !important}
        @media (max-width:1280px){
            .layout{grid-template-columns:1fr}
            .finished-list{max-height:none}
        }
        @media (orientation:landscape) and (max-width:1366px){
            .layout{grid-template-columns:minmax(0,1.4fr) minmax(320px,.92fr)}
            .cards{grid-template-columns:repeat(auto-fill,minmax(215px,1fr))}
        }
        @media (max-width:820px){
            .stats{padding:6px 10px 4px}
            .page{padding:6px 8px 14px}
            .cards{grid-template-columns:repeat(2,minmax(0,1fr));padding:8px;gap:7px}
            .grid{grid-template-columns:1fr}
        }
        @media (max-width:560px){
            .brand h1{font-size:18px}
            .cards{grid-template-columns:1fr}
            .table-name{font-size:17px}
            .product-name{font-size:15px}
            .qty-badge{min-width:46px;min-height:46px;font-size:18px}
        }
    
        /* ── Complete Tables Bar ── */
        .complete-bar{max-width:1920px;margin:0 auto 0;padding:8px 10px 0}
        .complete-bar-inner{
            background:linear-gradient(135deg,#0b7a3e,#12a150);
            border-radius:16px;padding:12px 16px;
            display:flex;align-items:center;gap:12px;flex-wrap:wrap;
            box-shadow:0 4px 18px rgba(18,161,80,.28)
        }
        .complete-bar-label{font-size:15px;font-weight:bold;color:#fff;white-space:nowrap;flex-shrink:0}
        .complete-bar-chips{display:flex;gap:8px;flex-wrap:wrap}
        .complete-chip{
            background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.32);
            color:#fff;padding:6px 16px;border-radius:999px;
            font-size:14px;font-weight:bold;cursor:pointer;
            transition:background .14s;-webkit-tap-highlight-color:transparent
        }
        .complete-chip:active{background:rgba(255,255,255,.38)}

        /* ── View Toggle ── */
        .view-toggle{display:flex;gap:3px;background:rgba(255,255,255,.12);border-radius:12px;padding:3px;flex-shrink:0}
        .btn-view{appearance:none;border:none;border-radius:9px;min-height:30px;padding:0 13px;font-size:12px;font-weight:bold;cursor:pointer;color:rgba(255,255,255,.78);background:transparent;transition:background .15s,color .15s;white-space:nowrap}
        .btn-view.active{background:#fff;color:var(--primary-dark)}

        /* ── Table Grid ── */
        .table-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;padding:12px}
        .table-card{
            background:#fff;border:2px solid var(--line);border-radius:18px;
            padding:16px 12px;text-align:center;cursor:pointer;
            transition:transform .12s,box-shadow .12s;
            box-shadow:0 4px 12px rgba(17,56,92,.07);
            display:flex;flex-direction:column;align-items:center;gap:5px;
            -webkit-tap-highlight-color:transparent;user-select:none
        }
        .table-card:active{transform:scale(.95)}
        .table-card.status-yellow{border-color:#ffe066;background:linear-gradient(180deg,#fffde7,#fff);box-shadow:0 0 0 3px rgba(255,214,0,.18)}
        .table-card.status-red{border-color:#ffb3ab;background:linear-gradient(180deg,#fff2f0,#fff);box-shadow:0 0 0 3px rgba(228,76,58,.14)}
        .table-card.status-complete{border-color:#6edda0;background:linear-gradient(180deg,#edfff5,#f4fff9);box-shadow:0 0 0 3px rgba(18,161,80,.18)}
        .table-card.status-complete .table-card-dot{background:var(--success)}
        .table-card.status-complete .table-card-name{color:#0b7a3e}
        .table-card-name{font-size:24px;font-weight:bold;line-height:1;color:#0f2945}
        .table-card-count{font-size:12px;font-weight:bold;color:var(--muted)}
        .table-card-dot{width:9px;height:9px;border-radius:50%;background:var(--success);margin-top:2px}
        .table-card.status-yellow .table-card-dot{background:#f5a623}
        .table-card.status-red .table-card-dot{background:var(--danger)}

        /* ── Modal ── */
        .modal-overlay{
            display:none;position:fixed;inset:0;z-index:100;
            background:rgba(8,30,60,.55);backdrop-filter:blur(4px);
            align-items:flex-end;justify-content:center
        }
        .modal-overlay.open{display:flex}
        .modal-box{
            background:#fff;border-radius:24px 24px 0 0;width:100%;max-width:620px;
            max-height:88dvh;display:flex;flex-direction:column;
            box-shadow:0 -14px 44px rgba(8,30,60,.22);
            animation:slideUp .22s ease
        }
        @keyframes slideUp{from{transform:translateY(60px);opacity:0}to{transform:translateY(0);opacity:1}}
        .modal-head{
            display:flex;align-items:center;justify-content:space-between;gap:12px;
            padding:16px 18px 12px;border-bottom:1px solid var(--line);flex-shrink:0
        }
        .modal-title{font-size:22px;font-weight:bold;color:#0f2945}
        .modal-sub{font-size:13px;color:var(--muted);margin-top:3px}
        .modal-close{
            width:36px;height:36px;border-radius:50%;border:none;background:#f0f4f8;
            color:#4a6080;font-size:22px;line-height:1;cursor:pointer;
            display:flex;align-items:center;justify-content:center;flex-shrink:0
        }
        .modal-body{overflow-y:auto;padding:12px 12px 28px;display:flex;flex-direction:column;gap:10px}
        @media(min-width:620px){
            .modal-overlay{align-items:center}
            .modal-box{border-radius:24px;max-height:80dvh}
        }
        @media(max-width:560px){
            .table-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;padding:8px}
            .table-card{padding:12px 8px}
            .table-card-name{font-size:20px}
        }

        /* ── Fullscreen Button ── */
        .btn-fullscreen{
            display:inline-flex;align-items:center;gap:5px;
            padding:5px 11px;border-radius:9px;border:none;cursor:pointer;
            font-size:13px;font-weight:600;white-space:nowrap;
            background:rgba(255,255,255,0.18);color:#fff;
            backdrop-filter:blur(6px);
            transition:background .18s,transform .12s;
            flex-shrink:0;
        }
        .btn-fullscreen:hover{background:rgba(255,255,255,0.30);transform:scale(1.04)}
        .btn-fullscreen svg{width:15px;height:15px;flex-shrink:0}
</style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <h1><img src="logo.svg" alt="" style="width:28px;height:28px;vertical-align:middle;margin-right:7px;border-radius:7px;flex-shrink:0"> Staff Display</h1>
                <div class="brand-sub">
                    <span><?php echo h(APP_TITLE); ?> · หน้าดูสถานะอย่างเดียว</span>
                    <?php if ($machineDisplayName !== ''): ?><span class="machine-chip"><?php echo h($machineDisplayName); ?></span><?php endif; ?>
                </div>
            </div>
            <div class="controls">
                <div class="view-toggle">
                    <button type="button" class="btn-view active" data-view="list">รายการ</button>
                    <button type="button" class="btn-view" data-view="table">โต๊ะ</button>
                </div>
                <button type="button" class="btn btn-filter active" data-filter="all">ทั้งหมด</button>
                <button type="button" class="btn btn-filter" data-filter="active">ดูเฉพาะคิวค้าง</button>
                <button type="button" class="btn btn-filter" data-filter="ready">ดูเฉพาะพร้อมเสิร์ฟแล้ว</button>
                <button type="button" class="btn btn-neutral" id="refreshBtn">รีเฟรช</button>
                <button type="button" class="btn-fullscreen" id="fsBtn" title="เต็มจอ">
                    <svg class="fs-ico-enter" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
                    <svg class="fs-ico-exit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" style="display:none"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/></svg>
                    เต็มจอ
                </button>
            </div>
        </div>
    </div>

    <div class="stats">
        <div class="stat"><div class="stat-label">รีเฟรช</div><div class="stat-value"><?php echo (int)APP_REFRESH_MS / 1000; ?>s</div></div>
        <div class="stat"><div class="stat-label">คิวค้าง</div><div class="stat-value" id="statActiveRows">0</div></div>
        <div class="stat"><div class="stat-label">รายการค้าง</div><div class="stat-value" id="statActiveQty">0</div></div>
        <div class="stat"><div class="stat-label">พร้อมเสิร์ฟแล้ว</div><div class="stat-value" id="statFinishedRows">0</div></div>
        <div class="stat"><div class="stat-label">สถานะ</div><div class="stat-value" id="statStatusText" style="font-size:15px;color:var(--success)">พร้อมใช้งาน</div></div>
    </div>

    <div id="completeTablesBar" class="complete-bar hidden">
        <div class="complete-bar-inner">
            <span class="complete-bar-label">🍽️ พร้อมเสิร์ฟครบแล้ว</span>
            <div class="complete-bar-chips" id="completeChips"></div>
        </div>
    </div>

    <div class="page">
        <div id="tableView" class="hidden">
            <div class="table-grid" id="tableGrid"></div>
        </div>
        <div id="listView">
        <div class="layout">
            <section class="panel" id="activePanel">
                <div class="panel-head">
                    <div>
                        <h2 class="panel-title">คิวครัวที่ยังค้างอยู่</h2>
                        <div class="panel-sub">สำหรับพนักงานดูสถานะอย่างเดียว ไม่มีปุ่มแก้ไขหรือเช็คเอาต์</div>
                    </div>
                    <div class="panel-badge" id="queueSummary">กำลังโหลด...</div>
                </div>
                <div class="toolbar">
                    <div class="tag good">พร้อมใช้งาน</div>
                    <div class="tag wait">เตือนเหลือง <?php echo (int)(defined('ALERT_THRESHOLD_YELLOW_DEFAULT') ? ALERT_THRESHOLD_YELLOW_DEFAULT : 10); ?> นาที</div>
                    <div class="tag urgent">เตือนแดง <?php echo (int)(defined('ALERT_THRESHOLD_RED_DEFAULT') ? ALERT_THRESHOLD_RED_DEFAULT : 20); ?> นาที</div>
                </div>
                <div class="cards" id="activeCards">
                    <div class="empty">กำลังโหลดข้อมูล...</div>
                </div>
            </section>

            <div class="side-stack">
                <section class="panel">
                    <div class="panel-head">
                        <div>
                            <h2 class="panel-title">สรุปหน้างาน</h2>
                            <div class="panel-sub">เหมาะกับ tablet แนวนอนและจอเสริมของพนักงาน</div>
                        </div>
                        <div class="panel-badge" id="currentFilterLabel">แสดงทั้งหมด</div>
                    </div>
                    <div class="mini-summary">
                        <div class="summary-box">
                            <div class="summary-box-label">คิวค้างทั้งหมด</div>
                            <div class="summary-box-value" id="summaryActiveRows">0</div>
                        </div>
                        <div class="summary-box">
                            <div class="summary-box-label">พร้อมเสิร์ฟแล้ว</div>
                            <div class="summary-box-value" id="summaryFinishedRows">0</div>
                        </div>
                        <div class="summary-box">
                            <div class="summary-box-label">จำนวนรายการค้าง</div>
                            <div class="summary-box-value" id="summaryActiveQty">0</div>
                        </div>
                        <div class="summary-box">
                            <div class="summary-box-label">อัปเดตล่าสุด</div>
                            <div class="summary-box-value" id="summaryLastUpdated" style="font-size:18px">-</div>
                        </div>
                    </div>
                </section>

                <section class="panel" id="finishedPanel">
                    <div class="panel-head">
                        <div>
                            <h2 class="panel-title">รายการที่เสร็จแล้วล่าสุด</h2>
                            <div class="panel-sub">ดูเพื่อเช็กว่าโต๊ะไหนพร้อมเสิร์ฟแล้ว</div>
                        </div>
                        <div class="panel-badge" id="finishedCount">0</div>
                    </div>
                    <div class="finished-list" id="finishedList">
                        <div class="empty">กำลังโหลดข้อมูล...</div>
                    </div>
                </section>
            </div>
        </div>
        </div><!-- /listView -->
    </div>

<div class="modal-overlay" id="tableModal">
    <div class="modal-box">
        <div class="modal-head">
            <div>
                <div class="modal-title" id="modalTableName">โต๊ะ -</div>
                <div class="modal-sub" id="modalTableSub"></div>
            </div>
            <button class="modal-close" id="modalClose" aria-label="ปิด">×</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<script>
const REFRESH_MS = <?php echo (int)APP_REFRESH_MS; ?>;
const thresholdYellow = <?php echo (int)(defined('ALERT_THRESHOLD_YELLOW_DEFAULT') ? ALERT_THRESHOLD_YELLOW_DEFAULT : 10); ?>;
const thresholdRed = <?php echo (int)(defined('ALERT_THRESHOLD_RED_DEFAULT') ? ALERT_THRESHOLD_RED_DEFAULT : 20); ?>;
const PAGE_CID = <?php echo (int)(isset($_GET['cid']) && (int)$_GET['cid'] > 0 ? (int)$_GET['cid'] : 0); ?>;
const cidParam = PAGE_CID > 0 ? '&cid=' + PAGE_CID : '';
const endpointActive = 'api_checker.php?action=list_active'   + cidParam;
const endpointFinished = 'api_checker.php?action=list_finished' + cidParam;

const state = {
    stats: { active_rows: 0, active_qty: 0, recent_finished_rows: 0 },
    active_rows: [],
    recent_finished_rows: [],
    filter: 'all',
    view: 'list'
};

function safeArray(v){ return Array.isArray(v) ? v : []; }
function escapeHtml(str){
    return String(str ?? '').replace(/[&<>"']/g, function(m){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[m];
    });
}
function formatQty(v){
    const n = Number(v || 0);
    return Number.isInteger(n) ? String(n) : n.toFixed(2).replace(/\.00$/, '');
}
function formatDateTime(value){
    if (!value) return '-';
    const date = new Date(value.replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) return String(value);
    return date.toLocaleString('th-TH', { hour12:false, year:'numeric', month:'2-digit', day:'2-digit', hour:'2-digit', minute:'2-digit' });
}
function formatTime(value){
    if (!value) return '-';
    const date = new Date(value.replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) return String(value).slice(11,16) || String(value);
    return date.toLocaleTimeString('th-TH', { hour12:false, hour:'2-digit', minute:'2-digit' });
}
function calcWaitMinutes(row){
    const src = row && row.SubmitOrderDateTime ? String(row.SubmitOrderDateTime) : '';
    if (!src) return 0;
    const date = new Date(src.replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) return 0;
    return Math.max(0, Math.floor((Date.now() - date.getTime()) / 60000));
}
function renderComments(comments){
    const items = safeArray(comments).filter(Boolean);
    if (!items.length) return '';
    const normalItems = [];
    const pricedItems = [];
    items.forEach(function(item){
        const amount = Number(item.amount || 0);
        const label = amount > 1 ? `${escapeHtml(item.text || '-') } x${formatQty(amount)}` : escapeHtml(item.text || '-');
        if (item.is_priced) pricedItems.push(label); else normalItems.push(label);
    });
    const blocks = [];
    if (normalItems.length) {
        blocks.push(`<div class="comment-group"><span class="comment-group-label">คอมเมนต์</span><span class="comment-group-items">${normalItems.join(', ')}</span></div>`);
    }
    if (pricedItems.length) {
        blocks.push(`<div class="comment-group priced"><span class="comment-group-label">คอมเมนต์เพิ่มราคา</span><span class="comment-group-items">${pricedItems.join(', ')}</span></div>`);
    }
    return blocks.length ? `<div class="comment-list">${blocks.join('')}</div>` : '';
}
function getCardClass(row, isReady){
    const waitMinutes = calcWaitMinutes(row);
    const isVoided = !!row.is_voided;
    const isMoved = !!row.is_moved;
    const isCombined = !!row.is_combined;
    if (isReady) return { cardClass:'card ready-card', waitClass:'tag good', qtyClass:'qty-badge ready', status:'<div class="status-badge ready">✅ พร้อมเสิร์ฟแล้ว</div>' };
    if (isVoided) return { cardClass:'card voided', waitClass:'tag', qtyClass:'qty-badge', status:'<div class="status-badge voided">🚫 ยกเลิกแล้ว</div>' };
    if (isMoved) return { cardClass:'card moved', waitClass:'tag', qtyClass:'qty-badge', status:`<div class="status-badge moved">🔀 ย้ายไปโต๊ะ ${escapeHtml(row.moved_to || '-')}</div>` };
    if (isCombined) return { cardClass:'card combined', waitClass:'tag', qtyClass:'qty-badge', status:'<div class="status-badge combined">🔗 รวมโต๊ะแล้ว</div>' };
    if (waitMinutes >= thresholdRed) return { cardClass:'card warn-red', waitClass:'tag urgent', qtyClass:'qty-badge', status:'' };
    if (waitMinutes >= thresholdYellow) return { cardClass:'card warn-yellow', waitClass:'tag wait', qtyClass:'qty-badge', status:'' };
    return { cardClass:'card', waitClass:'tag good', qtyClass:'qty-badge', status:'' };
}
function buildCard(row, isReady){
    const tableText = row.DisplayTableName || row.TableID || '-';
    const waitMinutes = calcWaitMinutes(row);
    const style = getCardClass(row, isReady);
    return `
        <article class="${style.cardClass}">
            <div class="card-head">
                <div>
                    <div class="table-name">โต๊ะ ${escapeHtml(tableText)}</div>
                    <div class="order-line">Order No. ${escapeHtml(row.OrderNo || '-')}</div>
                </div>
                <div class="${style.qtyClass}">x${formatQty(row.ProductAmount)}</div>
            </div>
            ${style.status}
            <div class="product-block">
                ${row.parent_name ? `<div class="parent-name-label">${escapeHtml(row.parent_name)}</div>` : ''}
                <h3 class="product-name">${escapeHtml(row.ProductName || '-')}</h3>
                ${renderComments(row.comments || [])}
            </div>
            <div class="queue-tags">
                ${isReady
                    ? `<span class="tag good">✔ เสร็จแล้ว</span><span class="tag">${escapeHtml(row.SaleModeName || '-')}</span>`
                    : `<span class="${style.waitClass}">⏱️ รอ ${waitMinutes} นาที</span><span class="tag">${escapeHtml(row.SaleModeName || '-')}</span>`}
            </div>
            <div class="grid">
                <div class="field">
                    <div class="field-label">ส่งเข้าเมื่อ</div>
                    <div class="field-value">${escapeHtml(formatTime(row.SubmitOrderDateTime))}</div>
                </div>
                <div class="field">
                    <div class="field-label">${isReady ? 'เสร็จเมื่อ' : 'สถานะ'}</div>
                    <div class="field-value">${isReady ? escapeHtml(formatTime(row.FinishDateTime)) : 'กำลังทำอยู่'}</div>
                </div>
            </div>
        </article>`;
}
function renderActiveRows(rows){
    const wrap = document.getElementById('activeCards');
    if (!rows.length) {
        wrap.innerHTML = '<div class="empty">ไม่มีคิวค้างในตอนนี้</div>';
        return;
    }
    wrap.innerHTML = rows.map(function(row){ return buildCard(row, false); }).join('');
}
function renderFinishedRows(rows){
    const wrap = document.getElementById('finishedList');
    if (!rows.length) {
        wrap.innerHTML = '<div class="empty">ยังไม่มีรายการเสร็จล่าสุด</div>';
        return;
    }
    wrap.innerHTML = rows.map(function(row){
        const tableText = row.DisplayTableName || row.TableID || '-';
        return `
            <div class="finished-item">
                <div class="finished-top">
                    <div>
                        ${row.parent_name ? `<div class="parent-name-label">${escapeHtml(row.parent_name)}</div>` : ''}
                        <div class="finished-name">${escapeHtml(row.ProductName || '-')}</div>
                        <div class="finished-meta">โต๊ะ ${escapeHtml(tableText)} · ${escapeHtml(row.SaleModeName || '-')}</div>
                    </div>
                    <div class="finished-qty">x${formatQty(row.ProductAmount)}</div>
                </div>
                ${renderComments(row.comments || [])}
                <div class="finished-meta">ส่งเข้าเมื่อ ${escapeHtml(formatTime(row.SubmitOrderDateTime))} · เสร็จเมื่อ ${escapeHtml(formatDateTime(row.FinishDateTime))}</div>
            </div>`;
    }).join('');
}
function getFilteredActiveRows(){
    return state.filter === 'ready' ? [] : safeArray(state.active_rows);
}
function getFilteredFinishedRows(){
    return state.filter === 'active' ? [] : safeArray(state.recent_finished_rows);
}
function syncFilterButtons(){
    document.querySelectorAll('[data-filter]').forEach(function(btn){
        btn.classList.toggle('active', btn.getAttribute('data-filter') === state.filter);
    });
    const labels = { all:'แสดงทั้งหมด', active:'ดูเฉพาะคิวค้าง', ready:'ดูเฉพาะพร้อมเสิร์ฟแล้ว' };
    document.getElementById('currentFilterLabel').textContent = labels[state.filter] || labels.all;
    document.getElementById('activePanel').classList.toggle('hidden', state.filter === 'ready');
    document.getElementById('finishedPanel').classList.toggle('hidden', state.filter === 'active');
}
function updateView(){
    const activeRows = getFilteredActiveRows();
    const finishedRows = getFilteredFinishedRows();
    const now = new Date();
    const lastUpdatedText = now.toLocaleTimeString('th-TH', { hour12:false, hour:'2-digit', minute:'2-digit', second:'2-digit' });

    document.getElementById('statActiveRows').textContent = Number(state.stats.active_rows || 0);
    document.getElementById('statActiveQty').textContent = formatQty(state.stats.active_qty || 0);
    document.getElementById('statFinishedRows').textContent = Number(state.stats.recent_finished_rows || 0);
    document.getElementById('summaryActiveRows').textContent = Number(state.stats.active_rows || 0);
    document.getElementById('summaryFinishedRows').textContent = Number(state.stats.recent_finished_rows || 0);
    document.getElementById('summaryActiveQty').textContent = formatQty(state.stats.active_qty || 0);
    document.getElementById('summaryLastUpdated').textContent = lastUpdatedText;
    document.getElementById('finishedCount').textContent = Number(state.stats.recent_finished_rows || 0);
    document.getElementById('queueSummary').textContent = state.filter === 'ready'
        ? `พร้อมเสิร์ฟ ${finishedRows.length} รายการ`
        : `คิวค้าง ${activeRows.length} รายการ`;
    document.getElementById('statStatusText').textContent = 'พร้อมใช้งาน';
    document.getElementById('statStatusText').style.color = 'var(--success)';

    renderActiveRows(activeRows);
    renderFinishedRows(finishedRows);
    syncFilterButtons();
    updateTabBadge(Number(state.stats.active_rows || 0));
    renderCompleteTables();
    if(state.view === 'table') renderTableGrid();
}
async function loadAll(){
    try {
        document.getElementById('statStatusText').textContent = 'กำลังโหลด';
        document.getElementById('statStatusText').style.color = 'var(--secondary)';
        const [activeRes, finishedRes] = await Promise.all([
            fetch(endpointActive + '&_=' + Date.now(), { cache: 'no-store' }),
            fetch(endpointFinished + '&_=' + Date.now(), { cache: 'no-store' })
        ]);
        const activeJson = await activeRes.json();
        const finishedJson = await finishedRes.json();
        if (!activeRes.ok || !activeJson.success) throw new Error(activeJson.error || 'โหลดคิวไม่สำเร็จ');
        if (!finishedRes.ok || !finishedJson.success) throw new Error(finishedJson.error || 'โหลดรายการเสร็จไม่สำเร็จ');

        state.stats.active_rows = Number((activeJson.stats || {}).active_rows || 0);
        state.stats.active_qty = Number((activeJson.stats || {}).active_qty || 0);
        state.active_rows = safeArray(activeJson.active_rows || []);
        state.recent_finished_rows = safeArray(finishedJson.recent_finished_rows || []);
        state.stats.recent_finished_rows = state.recent_finished_rows.length;
        updateView();
    } catch (error) {
        document.getElementById('statStatusText').textContent = 'เกิดข้อผิดพลาด';
        document.getElementById('statStatusText').style.color = 'var(--danger)';
        document.getElementById('activeCards').innerHTML = '<div class="empty">โหลดคิวไม่สำเร็จ</div>';
        document.getElementById('finishedList').innerHTML = '<div class="empty">โหลดรายการเสร็จไม่สำเร็จ</div>';
        console.error(error);
    }
}

// ── Table View helpers ──
function tableKey(row){
    return String(row.TableID || row.DisplayTableName || '-');
}
function groupByTable(activeRows, finishedRows){
    const map = new Map();
    safeArray(activeRows).forEach(function(row){
        const key = tableKey(row);
        if(!map.has(key)) map.set(key, { key:key, name: row.DisplayTableName || row.TableID || '-', active:[], ready:[] });
        map.get(key).active.push(row);
    });
    safeArray(finishedRows).forEach(function(row){
        const key = tableKey(row);
        if(!map.has(key)) map.set(key, { key:key, name: row.DisplayTableName || row.TableID || '-', active:[], ready:[] });
        map.get(key).ready.push(row);
    });
    return Array.from(map.values());
}
function getTableWorstStatus(activeRows){
    let worst = 'normal';
    activeRows.forEach(function(row){
        if(row.is_voided || row.is_moved || row.is_combined) return;
        const w = calcWaitMinutes(row);
        if(w >= thresholdRed){ worst = 'red'; }
        else if(w >= thresholdYellow && worst === 'normal'){ worst = 'yellow'; }
    });
    return worst;
}
function isTableComplete(group){
    const pending = group.active.filter(function(r){ return !r.is_voided && !r.is_moved && !r.is_combined; });
    return pending.length === 0 && group.ready.length > 0;
}
function buildTableCard(group){
    const complete = isTableComplete(group);
    const status = complete ? 'complete' : getTableWorstStatus(group.active);
    const pending = group.active.filter(function(r){ return !r.is_voided && !r.is_moved && !r.is_combined; }).length;
    const readyCnt = group.ready.length;
    const dotLabel = complete ? '✅ ครบทุกจานแล้ว'
        : status === 'red' ? '🔴 เกินเวลา'
        : status === 'yellow' ? '🟡 ใกล้เวลา' : '🟢 ปกติ';
    return `<div class="table-card status-${escapeHtml(status)}" data-table-key="${escapeHtml(group.key)}" data-table-name="${escapeHtml(String(group.name))}">
        <div class="table-card-dot"></div>
        <div class="table-card-name">${escapeHtml(String(group.name))}</div>
        ${pending > 0 ? `<div class="table-card-count">${pending} รายการค้าง</div>` : ''}
        ${readyCnt > 0 ? `<div class="table-card-count" style="color:var(--success)">✓ ${readyCnt} พร้อมเสิร์ฟ</div>` : ''}
        <div style="font-size:11px;color:var(--muted);margin-top:1px">${dotLabel}</div>
    </div>`;
}

// ── Complete Tables Bar ──
function getCompleteTables(){
    const activeKeys = new Set(safeArray(state.active_rows).map(tableKey));
    const finishedMap = new Map();
    safeArray(state.recent_finished_rows).forEach(function(row){
        const key = tableKey(row);
        if(!finishedMap.has(key)) finishedMap.set(key, { key:key, name: row.DisplayTableName || row.TableID || '-', count:0 });
        finishedMap.get(key).count++;
    });
    return Array.from(finishedMap.values()).filter(function(t){ return !activeKeys.has(t.key); });
}
function renderCompleteTables(){
    const bar = document.getElementById('completeTablesBar');
    const chips = document.getElementById('completeChips');
    const tables = getCompleteTables();
    if(!tables.length){ bar.classList.add('hidden'); return; }
    chips.innerHTML = tables.map(function(t){
        return `<span class="complete-chip" data-table-key="${escapeHtml(t.key)}" data-table-name="${escapeHtml(String(t.name))}">โต๊ะ ${escapeHtml(String(t.name))} (${t.count})</span>`;
    }).join('');
    bar.classList.remove('hidden');
}
document.getElementById('completeChips').addEventListener('click', function(e){
    const chip = e.target.closest('.complete-chip');
    if(!chip) return;
    openTableModal(chip.dataset.tableKey, chip.dataset.tableName);
});
function renderTableGrid(){
    const wrap = document.getElementById('tableGrid');
    const groups = groupByTable(state.active_rows, state.recent_finished_rows);
    if(!groups.length){ wrap.innerHTML = '<div class="empty">ไม่มีโต๊ะที่มีคิวค้างอยู่</div>'; return; }
    wrap.innerHTML = groups.map(buildTableCard).join('');
}
document.getElementById('tableGrid').addEventListener('click', function(e){
    const card = e.target.closest('.table-card');
    if(!card) return;
    openTableModal(card.dataset.tableKey, card.dataset.tableName);
});

// ── Modal ──
function openTableModal(key, name){
    const activeRows = safeArray(state.active_rows).filter(function(r){ return tableKey(r) === key; });
    const readyRows  = safeArray(state.recent_finished_rows).filter(function(r){ return tableKey(r) === key; });
    document.getElementById('modalTableName').textContent = 'โต๊ะ ' + name;
    document.getElementById('modalTableSub').textContent  = `${activeRows.length} รายการค้าง · ${readyRows.length} พร้อมเสิร์ฟ`;
    let html = activeRows.map(function(r){ return buildCard(r, false); }).join('');
    html    += readyRows.map(function(r){ return buildCard(r, true);  }).join('');
    document.getElementById('modalBody').innerHTML = html || '<div class="empty">ไม่มีรายการ</div>';
    document.getElementById('tableModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeTableModal(){
    document.getElementById('tableModal').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('modalClose').addEventListener('click', closeTableModal);
document.getElementById('tableModal').addEventListener('click', function(e){
    if(e.target === this) closeTableModal();
});
document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') closeTableModal();
});

// ── View toggle ──
function setView(v){
    state.view = v;
    document.querySelectorAll('[data-view]').forEach(function(btn){
        btn.classList.toggle('active', btn.dataset.view === v);
    });
    document.getElementById('listView').classList.toggle('hidden', v === 'table');
    document.getElementById('tableView').classList.toggle('hidden', v === 'list');
    if(v === 'table') renderTableGrid();
}
document.querySelectorAll('[data-view]').forEach(function(btn){
    btn.addEventListener('click', function(){ setView(btn.dataset.view); });
});

// ── Tab badge ──
function updateTabBadge(count){
    document.title = count > 0 ? `(${count}) Staff Display` : 'Staff Display';
}

document.querySelectorAll('[data-filter]').forEach(function(btn){
    btn.addEventListener('click', function(){
        state.filter = btn.getAttribute('data-filter') || 'all';
        updateView();
    });
});
document.getElementById('refreshBtn').addEventListener('click', loadAll);

function handleDisplayVisibilityChange(){
    if (!document.hidden) {
        loadAll();
    }
}

loadAll();
setInterval(function(){
    if (!document.hidden) {
        loadAll();
    }
}, REFRESH_MS);
document.addEventListener('visibilitychange', handleDisplayVisibilityChange);
</script>

    <script>
    (function(){
        var btn = document.getElementById("fsBtn");
        if(!btn) return;
        function updateIcon(){
            var full = !!document.fullscreenElement;
            btn.querySelector(".fs-ico-enter").style.display = full ? "none" : "inline";
            btn.querySelector(".fs-ico-exit").style.display  = full ? "inline" : "none";
            btn.title = full ? "ออกจากเต็มจอ" : "เต็มจอ";
        }
        btn.addEventListener("click", function(){
            if(!document.fullscreenElement){
                document.documentElement.requestFullscreen().catch(function(){});
            } else {
                document.exitFullscreen().catch(function(){});
            }
        });
        document.addEventListener("fullscreenchange", updateIcon);
        updateIcon();
    })();
    </script>
</body>
</html>
