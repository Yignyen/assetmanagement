<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Admin')</title>
   {{--  for select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">


    <style>
        body { margin:0; font-family:Segoe UI, sans-serif; background:#f4f6f9; }
        .container { display:flex; min-height:100vh; }

        .sidebar {
            width:140px;
            background:#111827;
            color:#fff;
            padding:20px;
        }

        .sidebar a {
            display:block;
            color:#cbd5e1;
            padding:10px;
            text-decoration:none;
        }

        .sidebar a:hover {
            background:#1f2937;
            border-radius:6px;
        }

        .main {
            flex:1;
            padding:30px;
        }

        .card {
            background:#fff;
            padding:25px;
            border-radius:12px;
            box-shadow:0 4px 20px rgba(0,0,0,.06);
        }

        table {
            width:100%;
            border-collapse:collapse;
        }

        th, td {
            padding:14px;
            border-bottom:1px solid #e5e7eb;
            text-align:left;
        }

        th {
            background:#f9fafb;
            font-weight:600;
        }

        .btn {
            padding:6px 12px;
            border-radius:6px;
            border:none;
            cursor:pointer;
            text-decoration:none;
            font-size:14px;
        }
        .status-available {
            color: #16a34a;   /* green */
            font-weight: bold;
        }

        .status-assigned {
            color: #2563eb;   /* blue */
            font-weight: bold;
    }


        .btn-primary { background:#2563eb; color:white; }
        .btn-warning { background:#f59e0b; color:white; }
        .btn-danger  { background:#dc2626; color:white; }


        .btn-outline-danger {
                                color: #9f1239;
                                border: 1px solid #fecdd3;
                            }


    /* STATS */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}

.stat-box {
    padding: 20px;
    border-radius: 12px;
    color: #fff;
    text-align: center;
    box-shadow: 0 6px 18px rgba(0,0,0,.08);
}

.stat-box h3 {
    font-size: 28px;
    margin: 0;
}

.stat-box p {
    margin: 6px 0 0;
    font-size: 14px;
    opacity: .9;
}

/* COLORS */
.bg-assets     { background: #0ea5e9; }
.bg-assigned   { background: #6366f1; }
.bg-available  { background: #22c55e; }
.bg-users      { background: #f59e0b; }
.bg-locations  { background: #ef4444; }

/* CHARTS */
.charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.chart-box {
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 4px 14px rgba(0,0,0,.06);
}

.chart-box h4 {
    margin-bottom: 10px;
}

.chart-small {
    height: 180px;
    width: 180px;
    margin: auto;
}

.chart-wide {
    height: 160px;
    width: 260px;
    margin: auto;
}


/* ONLY tables wrapped with .table-scroll */
.table-scroll {
    overflow-x: auto;
    width: 100%;
}

.table-scroll table {
    min-width: 1200px;   /* adjust if needed */
    white-space: nowrap;
}
/* for checin and check out action in action log */
.action-checkout {
    color: #2563eb;font-weight: bold;
}
.action-checkin {
    color: #21850f;font-weight: bold;
}


/* Sidebar collapsible Assets menu */
.sidebar-parent {
    display: block;
    color: #cbd5e1;
    padding: 10px;
    cursor: pointer;
    font-weight: 600;
}

.sidebar-parent:hover {
    background: #1f2937;
    border-radius: 6px;
}

.sidebar-sub {
    display: none;
    margin-left: 12px;
}

/* Show submenu only when Assets is clicked */
#assets-toggle:checked + .sidebar-parent + .sidebar-sub {
    display: block;
}

.sidebar-sub a {
    display: block;
    font-size: 13px;
    padding: 6px 10px;
    color: #9ca3af;
    text-decoration: none;
}

.sidebar-sub a:hover {
    background: #1f2937;
    color: #fff;
    border-radius: 6px;
}
{{-- for select2 --}}
.select2-container {
    width: 30% !important;
}


    </style>




</head>
<body>

<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin</h2>
        <a href="{{ route('dashboard') }}">Dashboard</a>

        <!-- Assets collapsible menu -->
<div class="sidebar-group">
    <input type="checkbox" id="assets-toggle" hidden>

    <label for="assets-toggle" class="sidebar-parent">
        Assets ▸
    </label>

    <div class="sidebar-sub">
        <a href="{{ route('assets.index') }}">List All</a>
        <a href="{{ route('assets.index', ['status' => 'available']) }}">Available</a>
        <a href="{{ route('assets.index', ['status' => 'assigned']) }}">Assigned</a>
    </div>
</div>




        
        <a href="{{ route('users.index') }}">Users</a>        
        <a href="{{ route('action-logs.index') }}">Logs</a>
        <a href="{{ route('locations.index') }}">Locations</a>
    </div>

    <!-- Main content -->
    <div class="main">
        <h2>@yield('page-title')</h2>

        <div class="card">
            @yield('content')
        </div>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('.model-select').select2({
            placeholder: 'Search and select model',
            width: '100%'
        });
    });
</script>

</body>
</html>
