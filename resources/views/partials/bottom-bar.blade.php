@php
    $bottomNavItems = [
        [
            'permission' => 'dashboard',
            'route' => 'dashboard',
            'icon' => 'bi-house',
            'label' => 'Dashboard',
            'pattern' => 'dashboard',
        ],
        [
            'permission' => 'tasks',
            'route' => 'tasks.index',
            'icon' => 'bi-clock-history',
            'label' => 'Departures',
            'pattern' => 'tasks.*',
        ],
        [
            'permission' => 'events',
            'route' => 'events.index',
            'icon' => 'bi-bell',
            'label' => 'Alerts',
            'pattern' => 'events.*',
        ],
        [
            'permission' => 'complaints',
            'route' => 'complaints.index',
            'icon' => 'bi-chat-dots',
            'label' => 'Complaints',
            'pattern' => 'complaints.*',
        ],
        [
            'permission' => 'buses',
            'route' => 'buses.index',
            'icon' => 'bi-bus-front',
            'label' => 'Buses',
            'pattern' => 'buses.*',
        ],
        [
            'permission' => 'hotels',
            'route' => 'hotels.index',
            'icon' => 'bi-building',
            'label' => 'Hotels',
            'pattern' => 'hotels.*',
        ],
        [
            'permission' => 'guests',
            'route' => 'guests.index',
            'icon' => 'bi-people',
            'label' => 'Guests',
            'pattern' => 'guests.*',
        ],
    ];
@endphp

<nav id="bottom-bar" class="bottom-nav glass animate-on-load d-lg-none">
    <div class="d-flex justify-content-around align-items-center w-100">
        @foreach ($bottomNavItems as $item)
            @if (in_array($item['permission'], $permissions) || $item['permission'] === 'dashboard')
                <a href="{{ oRoute($item['route']) }}"
                    class="nav-item text-center text-decoration-none {{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <small style="font-size: .7em" >{{ $item['label'] }}</small>
                </a>
            @endif
        @endforeach

        {{-- Sidebar toggle button --}}
        <button id="sidebarToggle" class="nav-item text-center btn-sidebar-toggle">
            <i class="bi bi-three-dots"></i>
            <small>More</small>
        </button>
    </div>
</nav>

<style>
    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 65px;
        background: var(--primary, #6366f1); /* Primary background */
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 900;

        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .bottom-nav .nav-item {
        flex: 1;
        color: #fff; /* White text */
        font-size: 0.8rem;
        transition: all 0.2s ease-in-out;
    }

    .bottom-nav .nav-item i {
        font-size: 1.3rem;
        display: block;
    }

    .bottom-nav .nav-item.active {
        color: #fff; /* Keep white for active */
        font-weight: 600;
    }

    .bottom-nav .nav-item.active i {
        transform: scale(1.2);
        color: #fff;
    }

    /* Logout button same style as links */
    .bottom-nav .btn-logout {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 0.8rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0;
        width: 100%;
    }

    .bottom-nav .btn-logout i {
        font-size: 1.4rem;
        margin-bottom: 2px;
    }

    .bottom-nav .btn-logout:hover,
    .bottom-nav .btn-logout:focus {
        color: #f1f1f1;
    }

    .btn-sidebar-toggle {
        background: transparent;
        border: none;
        color: #fff;
        padding: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .btn-sidebar-toggle:hover {
        color: #f1f1f1;
    }
</style>
