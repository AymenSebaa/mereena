@if (in_array(auth()->user()->profile->role_id, [2, 4]))
    @include('buses.scan')
    @include('partials.scan')
@endif

@php
    $sidebarItems = [
        [
            'key' => 'dashboard',
            'route' => 'dashboard',
            'icon' => 'bi bi-house',
            'text' => 'Dashboard',
            'badge' => null,
        ],
        [
            'key' => 'tasks',
            'route' => 'tasks.index',
            'pattern' => 'tasks.*',
            'icon' => 'bi bi-bus-front',
            'text' => 'Departures',
            'badge' => 'task_count',
        ],
        [
            'key' => 'events',
            'route' => 'events.index',
            'pattern' => 'events.*',
            'icon' => 'bi bi-bell',
            'text' => 'Alerts',
            'badge' => 'event_count',
        ],
        [
            'key' => 'complaints',
            'route' => 'complaints.index',
            'pattern' => 'complaints.*',
            'icon' => 'bi bi-chat-dots',
            'text' => 'Complaints',
            'badge' => 'complaint_count',
        ],
        [
            'key' => 'buses',
            'route' => 'buses.index',
            'pattern' => 'buses.*',
            'icon' => 'bi bi-bus-front',
            'text' => 'Buses',
            'badge' => 'bus_count',
        ],
        [
            'key' => 'sites',
            'route' => 'sites.index',
            'pattern' => 'sites.*',
            'icon' => 'bi bi-building',
            'text' => 'Sites',
            'badge' => 'site_count',
        ],
        [
            'key' => 'guests',
            'route' => 'guests.index',
            'pattern' => 'guests.*',
            'icon' => 'bi bi-people',
            'text' => 'Customers',
            'badge' => 'guest_count',
        ],
        [
            'key' => 'scans',
            'route' => 'scans.index',
            'pattern' => 'scans.*',
            'icon' => 'bi-qr-code-scan',
            'text' => 'Scans',
            'badge' => 'scan_count',
        ],
    ];

    $menu = [
        [
            'key' => 'statistics',
            'text' => 'Statistics',
            'icon' => 'bi bi-graph-up',
            'children' => [
                [
                    'key' => 'statistics.bus-score',
                    'route' => 'statistics.bus-score',
                    'icon' => 'bi bi-bus-front',
                    'text' => 'Bus score',
                ],
                [
                    'key' => 'statistics.operator-score',
                    'route' => 'statistics.operator-score',
                    'icon' => 'bi bi-person-badge',
                    'text' => 'Operator score',
                ],
                [
                    'key' => 'statistics.supervisor-score',
                    'route' => 'statistics.supervisor-score',
                    'icon' => 'bi bi-people',
                    'text' => 'Supervisor score',
                ],
                [
                    'key' => 'statistics.company-score',
                    'route' => 'statistics.company-score',
                    'icon' => 'bi bi-buildings',
                    'text' => 'Company score',
                ],
            ],
        ],
        [
            'key' => 'settings',
            'text' => 'Settings',
            'icon' => 'bi bi-gear',
            'children' => [
                [
                    'key' => 'settings.staff',
                    'route' => 'staff.index',
                    'icon' => 'bi bi-people',
                    'text' => 'Staff',
                ],
                [
                    'key' => 'settings.types',
                    'route' => 'types.index',
                    'icon' => 'bi bi-stack',
                    'text' => 'Types',
                ],
                [
                    'key' => 'settings.zones',
                    'route' => 'zones.index',
                    'icon' => 'bi bi-pin-map-fill',
                    'text' => 'Zones',
                ],
                [
                    'key' => 'settings.companies',
                    'route' => 'companies.index',
                    'icon' => 'bi bi-buildings',
                    'text' => 'Companies',
                ],
            ],
        ],
    ];

    $modules = App\Services\ModuleManager::all();
    // dd($modules);
@endphp

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<aside class="sidebar glass animate-on-load" style="width: 280px;">
    <div class="sidebar-bg">
        <div class="sidebar-bg-animation"></div>
    </div>
    <div class="position-relative d-flex justify-content-between align-items-center px-4">
        <div class="d-flex flex-column">
            <strong class="text-light">{{ auth()->user()->organization?->name }}</strong>
            <div>
                <small class="text-light"><b>{{ auth()->user()->profile->role->name }}</b></small>
                <span>{{ auth()->user()->profile->category ?? '' }}</span>
            </div>
            <small class="text-gray">{{ auth()->user()->email }}</small>
        </div>
        <div class="d-flex align-items-center">
            <button class="theme-toggle me-2" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
            <button class="collapse-toggle" id="collapseToggle" hidden>
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>
        </div>
    </div>

    <nav class="nav-items flex-grow-1 py-3 ">

        @foreach ($sidebarItems as $item)
            @if (in_array($item['key'], $permissions))
                <div class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center px-4 py-3 text-white text-decoration-none
                        {{ request()->routeIs($item['pattern'] ?? $item['route']) ? 'active' : '' }}"
                        href="{{ oRoute($item['route']) }}">
                        <div>
                            <i class="{{ $item['icon'] }} me-3 fs-5"></i>
                            <span class="nav-text">{{ $item['text'] }}</span>
                        </div>
                        @if ($item['badge'])
                            <span id="{{ $item['badge'] }}"
                                class="nav-badge bg-accent text-white ms-2 px-2 py-1 rounded-pill small"></span>
                        @endif
                    </a>
                </div>
            @endif
        @endforeach
    </nav>

    <div class="mt-auto border-top border-white border-opacity-10 pt-3">
        {{-- Modules --}}
        @foreach ($modules as $module)
            @if (isset($module['menu']))
                <div class="nav-item">
                    <a class="nav-link d-flex align-items-center px-4 py-3 text-white text-decoration-none
                {{ request()->routeIs($module['menu']['route'] ?? '') ? 'active' : '' }}"
                        @if (!empty($module['menu']['children'])) data-bs-toggle="collapse"
                    href="#{{ $module['slug'] }}Submenu"
                    role="button"
                    aria-expanded="{{ collect($module['menu']['children'])->pluck('route')->contains(fn($r) => request()->routeIs($r)) ? 'true' : 'false' }}"
                    aria-controls="{{ $module['slug'] }}Submenu"
                @elseif(isset($module['menu']['route']))
                    href="{{ oRoute($module['menu']['route']) }}"
                @else
                    href="#" @endif>
                        <i class="bi bi-{{ $module['menu']['icon'] }} me-3 fs-5"></i>
                        <span class="nav-text">{{ $module['menu']['title'] }}</span>

                        @if (!empty($module['menu']['children']))
                            <i class="bi bi-chevron-down ms-auto"></i>
                        @endif
                    </a>

                    {{-- If the module has children, render collapsible submenu --}}
                    @if (!empty($module['menu']['children']))
                        <div class="collapse {{ collect($module['menu']['children'])->pluck('route')->contains(fn($r) => request()->routeIs($r)) ? 'show' : '' }}"
                            id="{{ $module['slug'] }}Submenu">
                            <ul class="nav flex-column ms-4">
                                @foreach ($module['menu']['children'] as $child)
                                    <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center px-4 py-2 text-white text-decoration-none
                                    {{ request()->routeIs($child['route']) ? 'active' : '' }}"
                                            href="{{ oRoute($child['route']) }}">
                                            <i class="bi bi-{{ $child['icon'] }} me-2"></i>
                                            <span class="nav-text">{{ $child['title'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach

        @foreach ($menu as $main)
            @if (in_array($main['key'], $permissions))
                <div class="nav-item">
                    <a class="nav-link d-flex align-items-center px-4 py-3 text-white text-decoration-none"
                        data-bs-toggle="collapse" href="#{{ $main['key'] }}Submenu" role="button"
                        aria-expanded="{{ collect($main['children'])->pluck('route')->contains(fn($r) => request()->routeIs($r)) ? 'true' : 'false' }}"
                        aria-controls="{{ $main['key'] }}Submenu">
                        <i class="{{ $main['icon'] }} me-3 fs-5"></i>
                        <span class="nav-text">{{ $main['text'] }}</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ collect($main['children'])->pluck('route')->contains(fn($r) => request()->routeIs($r)) ? 'show' : '' }}"
                        id="{{ $main['key'] }}Submenu">
                        <ul class="nav flex-column ms-4">
                            @foreach ($main['children'] as $item)
                                @if (in_array($item['key'], $permissions))
                                    <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center px-4 py-2 text-white text-decoration-none
                                            {{ request()->routeIs($item['route']) ? 'active' : '' }}"
                                            href="{{ oRoute($item['route']) }}">
                                            <i class="{{ $item['icon'] }} me-2"></i>
                                            <span class="nav-text">{{ $item['text'] }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Logout --}}
        <div class="nav-item">
            <form method="POST" action="{{ oRoute('logout') }}" class="d-inline">
                @csrf
                <button type="submit"
                    class="nav-link d-flex align-items-center px-4 py-3 text-white text-decoration-none border-0 bg-transparent w-100">
                    <i class="bi bi-box-arrow-right me-3 fs-5"></i>
                    <span class="nav-text">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<style>
    /* Floating button styles */
    .sidebar-toggle-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1200;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background: var(--primary, #0d6efd);
        color: #fff;
        font-size: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
    }

    .sidebar-toggle-btn:hover {
        background: var(--primary-dark, #0b5ed7);
    }

    /* Hide on desktop */
    @media (min-width: 992px) {
        .sidebar-toggle-btn {
            display: none !important;
        }
    }

    /* Active sidebar */
    .sidebar.active {
        left: 0;
    }

    /* Backdrop behind sidebar */
    .sidebar-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-in-out, visibility 0.3s;
    }

    /* Show backdrop when active */
    .sidebar-backdrop.active {
        opacity: 1;
        visibility: visible;
    }

    /* Collapsed sidebar */
    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar.collapsed .nav-text,
    .sidebar.collapsed .nav-link .bi-chevron-down {
        display: none;
    }

    .sidebar.collapsed .nav-link {
        justify-content: center !important;
    }

    .sidebar.collapsed .nav-link>div {
        display: flex;
        justify-content: center;
    }

    .sidebar.collapsed ul.nav {
        padding-left: 0 !important;
    }

    .sidebar.collapsed .nav-badge {
        margin-left: 0;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btn = document.getElementById("sidebarToggle");
        const collapseBtn = document.getElementById("collapseToggle");
        const sidebar = document.querySelector(".sidebar");
        const backdrop = document.getElementById("sidebarBackdrop");

        function toggleSidebar() {
            sidebar.classList.toggle("active");
            backdrop.classList.toggle("active");
        }

        btn.addEventListener("click", toggleSidebar);
        backdrop.addEventListener("click", toggleSidebar);

        collapseBtn.addEventListener("click", function() {
            sidebar.classList.toggle("collapsed");
        });

        // Fetch counts
        @if(false)
        fetch("{{ oRoute('sidebar.counts') }}")
            .then(res => res.json())
            .then(data => {
                @if (in_array('tasks', $permissions))
                    document.getElementById("task_count").innerText = data.task_count;
                @endif
                @if (in_array('events', $permissions))
                    document.getElementById("event_count").innerText = data.event_count;
                @endif
                @if (in_array('complaints', $permissions))
                    document.getElementById("complaint_count").innerText = data.complaint_count;
                @endif
                @if (in_array('buses', $permissions))
                    document.getElementById("bus_count").innerText = data.bus_count;
                @endif
                @if (in_array('hotels', $permissions))
                    document.getElementById("hotel_count").innerText = data.hotel_count;
                @endif
                @if (in_array('guests', $permissions))
                    document.getElementById("guest_count").innerText = data.guest_count;
                @endif
                @if (in_array('scans', $permissions))
                    document.getElementById("scan_count").innerText = data.scan_count;
                @endif
            });
        @endif
    });
</script>
