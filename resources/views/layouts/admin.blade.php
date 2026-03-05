<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Admin')</title>

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <style>
        body {
            margin:0;
            font-family:Segoe UI, sans-serif;
            background:#f4f6f9;
        }

        .container {
            display:flex;
            min-height:100vh;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            width:220px;                 /* FIXED WIDTH */
            background:#111827;
            color:#fff;
            padding:20px;
            flex-shrink:0;
        }

        .sidebar h2 {
            margin-bottom:20px;
        }

        .sidebar a {
            display:block;
            color:#cbd5e1;
            padding:10px;
            text-decoration:none;
            border-radius:6px;
            font-size:14px;
        }

        .sidebar a:hover {
            background:#1f2937;
            color:#fff;
        }

        .sidebar a.active {
            background:#2563eb;
            color:#fff;
        }

        /* ================= SUB MENU ================= */

        .sidebar-parent {
            display:block;
            padding:10px;
            font-weight:600;
            cursor:pointer;
            border-radius:6px;
        }

        .sidebar-parent:hover {
            background:#1f2937;
        }

        .sidebar-sub {
            display:none;
            margin-left:10px;
        }

        #assets-toggle:checked + .sidebar-parent + .sidebar-sub {
            display:block;
        }

        .sidebar-sub a {
            font-size:13px;
            padding:6px 10px;
            color:#9ca3af;
        }

        .sidebar-sub a:hover {
            background:#1f2937;
            color:#fff;
        }

        /* ================= MAIN ================= */

        .main {
            flex:1;
            padding:40px;
            overflow-x:auto;
        }

        .card {
            background:#fff;
            padding:25px;
            border-radius:12px;
            box-shadow:0 4px 20px rgba(0,0,0,.06);
            width:100%;
        }

        /* ================= TABLE ================= */

        .table-scroll {
            overflow-x:auto;
            width: 100%;
            position: relative;
        }

        /* Ensure table is wide enough */
        .table-scroll table {
            min-width: 1300px; /* increase if needed */
        }
        
/* Sticky right columns */
        .sticky-action {
            position: sticky;
            right: 0;
            background: #fff;
            z-index: 3;
            min-width: 110px;
            box-shadow: -3px 0 6px rgba(0,0,0,0.05);
        }

        .sticky-check {
            position: sticky;
            right: 120px; /* width of action column */
            background: #fff;
            z-index: 3;
            min-width: 140px;
            box-shadow: -3px 0 6px rgba(0,0,0,0.05);
}

/* Fix header also */
th.sticky-action,
th.sticky-check {
    background: #f9fafb;
}

        table {
            width:100%;
            border-collapse:collapse;
            min-width:1100px;
        }

        th, td {
            padding:12px;
            border-bottom:1px solid #e5e7eb;
            text-align:left;
        }

        th {
            background:#f9fafb;
            font-weight:600;
        }

        /* ================= BUTTONS ================= */

        .btn {
            padding:6px 12px;
            border-radius:6px;
            text-decoration:none;
            font-size:14px;
            display:inline-block;
        }

        .btn-primary {
            background:#2563eb;
            color:white;
        }

        .btn-warning {
            background:#f59e0b;
            color:white;
        }

        .btn-danger {
            background:#dc2626;
            color:white;
        }

        .btn-outline-danger {
            border:1px solid #fecdd3;
            color:#9f1239;
        }

        /* ================= STATUS COLORS ================= */

        .status-rtd { color:#16a34a; font-weight:bold; }
        .status-deployed { color:#2563eb; font-weight:bold; }
        .status-pending { color:#f59e0b; font-weight:bold; }
        .status-undeployable { color:#dc2626; font-weight:bold; }
        .status-archived { color:#6b7280; font-weight:bold; }


        /* Status dot */
.status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
    vertical-align: middle;
}

/* Dot colors */
.dot-green { background: #16a34a; }
.dot-blue { background: #2563eb; }
.dot-orange { background: #f59e0b; }
.dot-red { background: #dc2626; }
.dot-gray { background: #6b7280; }

/* Small deployed tag */
.status-tag {
    display: inline-block;
    font-size: 11px;
    padding: 2px 6px;
    margin-left: 6px;
    border-radius: 4px;
    background: #2563eb;
    color: #fff;
}

.text-green {
    color: #16a34a;
    font-weight: 500;
}

.text-blue {
    color: blue;
    font-weight: 500;
}

/* for sscrollable with last two column fix */

/* Sticky right columns */
.sticky-right-1 {
    position: sticky;
    right: 0;
    background: white;
    z-index: 3;
}

.sticky-right-2 {
    position: sticky;
    right: 120px; /* width of last column */
    background: white;
    z-index: 3;
}

/* Add shadow so it looks professional */
.sticky-right-1,
.sticky-right-2 {
    box-shadow: -4px 0 6px rgba(0,0,0,0.05);
}



/* ================= for dashboard ================= */


.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-box {
    padding: 20px;
    border-radius: 12px;
    color: #fff;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    transition: transform 0.2s ease;
}

.stat-box:hover {
    transform: translateY(-3px);
}

.stat-box h3 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
}

.stat-box p {
    margin: 5px 0 0;
    font-size: 14px;
    opacity: 0.9;
}

/* Background Colors */

.bg-assets {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.bg-assigned {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.bg-available {
    background: linear-gradient(135deg, #22c55e, #16a34a);
}

.bg-users {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.bg-locations {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

/* ================= for bulk select bar ================= */

.bulk-select {
    background: #eff1f1;
    color: #0a0202;
    border: 1px solid #444;
    padding: 6px 10px;
    min-width: 200px;
}


/* ================= PAGINATION ================= */

.pagination {
    list-style: none;
    display: flex;
    gap: 6px;
    padding: 0;
    margin: 0;
}

.pagination .page-item {
    list-style: none;
}

.pagination .page-link {
    display: block;
    padding: 6px 12px;
    border-radius: 6px;
    background: #f3f4f6;
    color: #111827;
    text-decoration: none;
    font-size: 14px;
    border: none;
}

.pagination .page-item.active .page-link {
    background: #2563eb;
    color: white;
    font-weight: 600;
}

.pagination .page-link:hover {
    background: #2563eb;
    color: white;
}


/*  for  number beside each badge pf assets */
.count-badge {
    float: right;
    /* background: #374151; */
    color: #fff;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 12px;
}



/* 
side bar divider between asset archived and action log asset */

.sidebar-divider {
    margin: 12px 0;
    border-top: 1px solid rgba(255,255,255,0.08); /* adjust for theme */
}


    </style>
    
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 --}}
</head>

<body>

<div class="container">

    <!-- ================= SIDEBAR ================= -->
    <div class="sidebar">
        <h2>Admin</h2>

        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        @php
            $isAssetsPage = request()->routeIs('assets.*');
        @endphp

        <input type="checkbox"
               id="assets-toggle"
               hidden
               {{ $isAssetsPage ? 'checked' : '' }}>

        <label for="assets-toggle" class="sidebar-parent">
            Assets ▸
        </label>

        <div class="sidebar-sub">

            <a href="{{ route('assets.index') }}"
               class="{{ request()->routeIs('assets.index') && !request('type') ? 'active' : '' }}">
               All Assets <span class="count-badge">{{ $sidebarCounts['all'] ?? 0 }}</span>

            </a>

            <a href="{{ route('assets.index', ['type'=>'rtd']) }}"
               class="{{ request('type')==='rtd' ? 'active' : '' }}">
                Available <span class="count-badge">{{ $sidebarCounts['rtd'] ?? 0 }}</span>

            </a>

            <a href="{{ route('assets.index', ['type'=>'deployed']) }}"
               class="{{ request('type')==='deployed' ? 'active' : '' }}">
                Assigned <span class="count-badge">{{ $sidebarCounts['deployed'] ?? 0 }}</span>

            </a>

            <a href="{{ route('assets.index', ['type'=>'pending']) }}"
               class="{{ request('type')==='pending' ? 'active' : '' }}">
                Waiting/In Process <span class="count-badge">{{ $sidebarCounts['pending'] ?? 0 }}</span>

            </a>

            <a href="{{ route('assets.index', ['type'=>'undeployable']) }}"
               class="{{ request('type')==='undeployable' ? 'active' : '' }}">
                Not Available <span class="count-badge">{{ $sidebarCounts['undeployable'] ?? 0 }}</span>

            </a>

            <a href="{{ route('assets.index', ['type'=>'archived']) }}"
               class="{{ request('type')==='archived' ? 'active' : '' }}">
                Retired Assets <span class="count-badge">{{ $sidebarCounts['archived'] ?? 0 }}</span>
                

            </a>


<div class="sidebar-divider"></div>

           <a href="{{ route('assets.logs') }}"
                class="{{ request()->routeIs('assets.logs') ? 'active' : '' }}">
                    Asset Action Log
                </a>

            <a href="{{ route('assets.deleted') }}"
                class="{{ request()->routeIs('assets.deleted') ? 'active' : '' }}">
                    Deleted Page
            </a>
           {{--  bulk checkout  page 
 --}}
           <a href="{{ route('assets.bulk.checkout.form') }}"
   class="{{ request()->routeIs('assets.bulk.checkout.*') ? 'active' : '' }}">
    Bulk Checkout
</a>

<a href="{{ route('assets.bulk.restore.form') }}"
   class="{{ request()->routeIs('assets.bulk.restore.*') ? 'active' : '' }}">
    Bulk Restore
</a>

        </div>

        <a href="{{ route('users.index') }}"
           class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
            Users
        </a>

       {{--  <a href="{{ route('action-logs.index') }}"
           class="{{ request()->routeIs('action-logs.*') ? 'active' : '' }}">
            Logs
        </a> --}}

        <a href="{{ route('locations.index') }}"
           class="{{ request()->routeIs('locations.*') ? 'active' : '' }}">
            Locations
        </a>
    </div>

    <!-- ================= MAIN ================= -->
    <div class="main">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">

        {{-- LEFT → PAGE TITLE --}}
        <h2 style="margin:0;">
            @yield('page-title')
        </h2>

        {{-- RIGHT → HEADER ACTIONS --}}
        <div>
            @yield('page-actions')
        </div>

    </div>


        <div class="card">
            @yield('content')
        </div>
    </div>

</div>

{{-- Scripts --}}
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('.model-select').select2({
            placeholder: 'Search and select model',
            width: '100%'
        });
    });
</script> --}}
{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@yield('scripts')

</body>

</body>
</html>
