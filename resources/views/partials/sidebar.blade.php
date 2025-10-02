@php
    $menus = [
        [
            'key' => 'dashboard',
            'title' => 'Dashboard',
            'icon' => 'house',
            'route' => 'dashboard',
        ],
        [
            'key' => 'events',
            'title' => 'Alerts',
            'icon' => 'bell',
            'route' => 'events.index',
            'badge' => 'event_count',
        ],
        [
            'key' => 'complaints',
            'title' => 'Complaints',
            'icon' => 'chat-dots',
            'route' => 'complaints.index',
            'badge' => 'complaint_count',
        ],
        [
            'key' => 'sites',
            'title' => 'Sites',
            'icon' => 'building',
            'route' => 'sites.index',
            'badge' => 'site_count',
        ],
        [
            'key' => 'guests',
            'title' => 'Customers',
            'icon' => 'people',
            'route' => 'guests.index',
            'badge' => 'guest_count',
        ],
        [
            'key' => 'scans',
            'title' => 'Scans',
            'icon' => 'qr-code-scan',
            'route' => 'scans.index',
            'badge' => 'scan_count',
        ],
    ];

    $menu_statistics = [
        'key' => 'statistics',
        'title' => 'Statistics',
        'icon' => 'graph-up',
        'children' => [
            [
                'key' => 'bus-score',
                'title' => 'Bus score',
                'icon' => 'bus-front',
                'route' => 'statistics.bus-score',
            ],
            [
                'key' => 'operator-score',
                'title' => 'Operator score',
                'icon' => 'person-badge',
                'route' => 'statistics.operator-score',
            ],
            [
                'key' => 'supervisor-score',
                'title' => 'Supervisor score',
                'icon' => 'people',
                'route' => 'statistics.supervisor-score',
            ],
            [
                'key' => 'company-score',
                'title' => 'Company score',
                'icon' => 'buildings',
                'route' => 'statistics.company-score',
            ],
        ],
    ];

    $menu_settings = [
        'key' => 'settings',
        'title' => 'Settings',
        'icon' => 'gear',
        'children' => [
            [
                'key' => 'staff',
                'title' => 'Staff',
                'icon' => 'people',
                'route' => 'staff.index',
            ],
            [
                'key' => 'types',
                'title' => 'Types',
                'icon' => 'stack',
                'route' => 'types.index',
            ],
            [
                'key' => 'zones',
                'title' => 'Zones',
                'icon' => 'pin-map-fill',
                'route' => 'zones.index',
            ],
            [
                'key' => 'companies',
                'title' => 'Companies',
                'icon' => 'buildings',
                'route' => 'companies.index',
            ],
        ],
    ];

    $modules = App\Services\ModuleManager::all();
    $menu_modules = collect($modules)->pluck('menu')->filter()->values()->toArray();

    $menus = array_merge($menus, $menu_modules);
    $menus[] = $menu_statistics;
    $menus[] = $menu_settings;

    $allowedModules = collect($permissions);
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
                <small class="text-gray">{{ auth()->user()->email }}</small>
            </div>
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

        @foreach ($menus as $menu)
            @php
                $hasChildren = !empty($menu['children']);
                $menuKey = $menu['key'] ?? $menu['title'];
                $showMenu = false;

                // Check if menu or any child is allowed
                if ($allowedModules->has($menuKey)) {
                    $showMenu = true;
                } elseif ($hasChildren) {
                    foreach ($menu['children'] as $child) {
                        if (isset($allowedModules[$menuKey]) && in_array($child['key'], $allowedModules[$menuKey])) {
                            $showMenu = true;
                            break;
                        }
                    }
                }

                // Check if submenu should be expanded based on current route
                $isActive = false;
                if (!empty($menu['route']) && request()->routeIs($menu['route'])) {
                    $isActive = true;
                } elseif ($hasChildren) {
                    foreach ($menu['children'] as $child) {
                        if (!empty($child['route']) && request()->routeIs($child['route'])) {
                            $isActive = true;
                            break;
                        }
                    }
                }
            @endphp

            @if ($showMenu)
                <div class="nav-item">
                    @if (!empty($menu['route']))
                        <a class="nav-link d-flex justify-content-between align-items-center px-4 py-3 text-white text-decoration-none {{ $isActive ? 'active' : '' }}"
                            href="{{ oRoute($menu['route']) }}">
                            <div>
                                <i class="bi bi-{{ $menu['icon'] }} me-3 fs-5"></i>
                                <span class="nav-text">{{ $menu['title'] }}</span>
                            </div>
                            @if (!empty($menu['badge']))
                                <span id="{{ $menu['badge'] }}"
                                    class="nav-badge bg-accent text-white ms-2 px-2 py-1 rounded-pill small"></span>
                            @endif
                        </a>
                    @elseif ($hasChildren)
                        <a class="nav-link d-flex align-items-center px-4 py-3 text-white text-decoration-none"
                            data-bs-toggle="collapse" href="#{{ $menuKey }}Submenu" role="button"
                            aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                            aria-controls="{{ $menuKey }}Submenu">
                            <i class="bi bi-{{ $menu['icon'] }} me-3 fs-5"></i>
                            <span class="nav-text">{{ $menu['title'] }}</span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse {{ $isActive ? 'show' : '' }}" id="{{ $menuKey }}Submenu">
                            <ul class="nav flex-column ms-4">
                                @foreach ($menu['children'] as $child)
                                    @if (isset($allowedModules[$menuKey][$child['key']]))
                                        <li class="nav-item">
                                            <a class="nav-link d-flex align-items-center px-4 py-2 text-white text-decoration-none {{ request()->routeIs($child['route']) ? 'active' : '' }}"
                                                href="{{ oRoute($child['route']) }}">
                                                <i class="bi bi-{{ $child['icon'] }} me-2"></i>
                                                <span class="nav-text">{{ $child['title'] }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach

                            </ul>
                        </div>
                    @endif
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
    </nav>
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
        @if (false)
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
