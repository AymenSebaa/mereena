@extends('layouts.app')

@section('title', 'Departures')
@php
    $role_id = auth()->user()->profile->role_id;
@endphp
@section('content')
    <div class="mobile-padding">
        <!-- Map -->
        <div class="overlay-card w-100 mb-4">
            <div class="card-title">
                <span>Departures Map</span>
                <small class="text-secondary">Showing pickup & delivery locations</small>
            </div>
            <div class="alerts-section p-0 overflow-hidden">
                <div id="tasks-map" style="width: 100%; height: 500px; border-radius: 0 0 20px 20px;"></div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="searchInput" class="form-control"
                placeholder="Search by title, hotel, bus, pickup or delivery address...">
            @if (in_array(auth()->user()->profile->role_id, [1, 2, 3, 4, 6]))
                <button id="newTaskBtn" class="btn btn-primary w-50 m-3" data-bs-toggle="modal"
                    data-bs-target="#upsertTaskModal">
                    <i class="bi bi-plus-circle me-2"></i> New
                </button>
            @endif
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="taskTabs">
            <li class="nav-item">
                <a class="nav-link active" data-filter="all" href="#">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-filter="open" href="#">Open</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-filter="regular" href="#">Regular</a>
            </li>
        </ul>

        <!-- Task Cards -->
        <div id="tasks-container" class="tasks-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>

        <!-- Upsert Task Modal -->
        <div class="modal fade" id="upsertTaskModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content glass-card">
                    <div class="modal-header">
                        <h5 class="modal-title">Upsert Departure</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="upsertTaskForm">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="id" id="task_id">
                            <div class="row g-3">
                                <div class="col-md-9">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>

                                <!-- Pickup Status -->
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="0">Disabled</option>
                                        <option value="1">Enabled</option>
                                    </select>
                                </div>

                                <!-- Pickup Priority -->
                                <div class="col-md-3">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select">
                                        <option value="0">Normal</option>
                                        <option value="1">High</option>
                                    </select>
                                </div>
                                <!-- Pickup Bus -->
                                <div class="col-md-9">
                                    <label class="form-label">Bus</label>
                                    <input type="text" id="bus_id" class="form-control"
                                        placeholder="Type License plate of Bus...">
                                    <input type="hidden" name="device_id" id="device_id">
                                </div>

                                <hr>

                                <!-- Pickup Hotel -->
                                <div class="col-md-8">
                                    <label class="form-label">Source</label>
                                    <input type="text" id="pickup_hotel" class="form-control"
                                        placeholder="Type name of Source..." required>
                                    <input type="hidden" name="pickup_address" id="pickup_address">
                                    <input type="hidden" name="pickup_address_lat" id="pickup_lat">
                                    <input type="hidden" name="pickup_address_lng" id="pickup_lng">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Departure Time</label>
                                    <input type="datetime-local" name="pickup_time_from" class="form-control">
                                </div>

                                <!-- Delivery Hotel -->
                                <div class="col-md-8">
                                    <label class="form-label">Destination</label>
                                    <input type="text" id="delivery_hotel" class="form-control"
                                        placeholder="Type name of Destination..." required>
                                    <input type="hidden" name="delivery_address" id="delivery_address">
                                    <input type="hidden" name="delivery_address_lat" id="delivery_lat">
                                    <input type="hidden" name="delivery_address_lng" id="delivery_lng">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Arrival Time</label>
                                    <input type="datetime-local" name="pickup_time_to" class="form-control">
                                </div>

                                <hr>

                                <div class="col-md-12 mt-4">
                                    <label for="">Return</label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Departure Time</label>
                                    <input type="datetime-local" name="delivery_time_from" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Arrival Time</label>
                                    <input type="datetime-local" name="delivery_time_to" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content glass-card">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this task?
                    </div>
                    <div class="modal-footer">
                        <form id="deleteTaskForm">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="id" id="delete_task_id">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script>
        // ---- Reset Modal ----
        function resetTaskModal() {
            const form = document.getElementById('upsertTaskForm');
            if (form) {
                form.reset();
                form.querySelector('[name="id"]').value = '';
                form.querySelectorAll('input[type="hidden"]').forEach(h => h.value = '');
            }
        }

        // Reset whenever modal is opened for NEW
        document.getElementById('newTaskBtn')?.addEventListener('click', () => {
            resetTaskModal();
        });

        // Also reset when modal is closed (safety)
        document.getElementById('upsertTaskModal').addEventListener('hidden.bs.modal', resetTaskModal);

        // ---- Bus Autocomplete ----
        let buses = [];
        $.get("{{ route('buses.list') }}", function(data) {
            buses = data.map(h => ({
                external_id: h.external_id,
                label: h.name,
                value: h.name,
                lat: h.lat,
                lng: h.lng,
                status: h.status,
            }));

            $("#bus_id").autocomplete({
                source: buses,
                select: function(event, ui) {
                    $("#device_id").val(ui.item.external_id);
                }
            });
        });

        // ---- Hotel Autocomplete ----
        let hotels = [];
        $.get("{{ route('hotels.list') }}", function(data) {
            hotels = data.map(h => ({
                label: h.name,
                value: h.name,
                address: h.name,
                lat: h.lat,
                lng: h.lng
            }));

            $("#pickup_hotel").autocomplete({
                source: hotels,
                select: function(event, ui) {
                    $("#pickup_address").val(ui.item.address);
                    $("#pickup_lat").val(ui.item.lat);
                    $("#pickup_lng").val(ui.item.lng);
                }
            });

            $("#delivery_hotel").autocomplete({
                source: hotels,
                select: function(event, ui) {
                    $("#delivery_address").val(ui.item.address);
                    $("#delivery_lat").val(ui.item.lat);
                    $("#delivery_lng").val(ui.item.lng);
                }
            });
        });

        // ---- Tab Filter ----
        let currentFilter = "all";
        document.querySelectorAll('#taskTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', e => {
                e.preventDefault();
                document.querySelectorAll('#taskTabs .nav-link').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                currentFilter = tab.dataset.filter;
                currentPage = 1;
                renderTasks();
                renderPagination();
                renderMap();
            });
        });

        document.addEventListener('click', async (e) => {
            if (e.target.closest('.duplicate-task')) {
                let id = e.target.closest('.duplicate-task').dataset.id;

                fetch("{{ route('tasks.duplicate', ':id') }}".replace(':id', id), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 1) {
                            tasksData.unshift(data.task);
                            renderTasks();
                            renderPagination();
                            renderMap();
                        } else {
                            alert(data.message || "Failed to duplicate task");
                        }
                    })
                    .catch(err => console.error(err));
            }
        });


        document.addEventListener('click', e => {
            // ---- Edit Task ----
            if (e.target.closest('.edit-task')) {
                const id = e.target.closest('.edit-task').dataset.id;
                const task = tasksData.find(t => t.id == id);
                if (!task) return;

                document.getElementById('task_id').value = task.id;
                document.querySelector('#upsertTaskForm [name="title"]').value = task.title ?? '';
                document.querySelector('#upsertTaskForm [name="priority"]').value = task.priority ?? '0';
                document.getElementById('bus_id').value = task.bus?.name ?? '';
                document.querySelector('#upsertTaskForm [name="status"]').value = task.status ?? '0';
                document.getElementById('device_id').value = task.device_id ?? '';
                document.getElementById('pickup_hotel').value = task.pickup_address ?? '';
                document.getElementById('pickup_address').value = task.pickup_address ?? '';
                document.getElementById('pickup_lat').value = task.pickup_address_lat ?? '';
                document.getElementById('pickup_lng').value = task.pickup_address_lng ?? '';
                document.getElementById('delivery_hotel').value = task.delivery_address ?? '';
                document.getElementById('delivery_address').value = task.delivery_address ?? '';
                document.getElementById('delivery_lat').value = task.delivery_address_lat ?? '';
                document.getElementById('delivery_lng').value = task.delivery_address_lng ?? '';
                document.querySelector('#upsertTaskForm [name="pickup_time_from"]').value = task.pickup_time_from ?
                    task.pickup_time_from.replace(' ', 'T') : '';
                document.querySelector('#upsertTaskForm [name="pickup_time_to"]').value = task.pickup_time_to ? task
                    .pickup_time_to.replace(' ', 'T') : '';
                document.querySelector('#upsertTaskForm [name="delivery_time_from"]').value = task
                    .delivery_time_from ? task.delivery_time_from.replace(' ', 'T') : '';
                document.querySelector('#upsertTaskForm [name="delivery_time_to"]').value = task.delivery_time_to ?
                    task.delivery_time_to.replace(' ', 'T') : '';

                new bootstrap.Modal(document.getElementById('upsertTaskModal')).show();
            }

            // ---- Delete Task ----
            if (e.target.closest('.delete-task')) {
                const id = e.target.closest('.delete-task').dataset.id;
                document.getElementById('delete_task_id').value = id;
                new bootstrap.Modal(document.getElementById('deleteTaskModal')).show();
            }
        });

        // ---- Delete Form Submit ----
        document.getElementById('deleteTaskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('delete_task_id').value;

            fetch("{{ route('tasks.delete', ':id') }}".replace(':id', id), {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 1) {
                        bootstrap.Modal.getInstance(document.getElementById('deleteTaskModal')).hide();
                        tasksData = tasksData.filter(t => t.id != id);
                        renderTasks();
                        renderPagination();
                        renderMap();
                    } else {
                        // window.location.reload();

                        bootstrap.Modal.getInstance(document.getElementById('deleteTaskModal')).hide();
                        tasksData = tasksData.filter(t => t.id != id);
                        renderTasks();
                        renderPagination();
                        renderMap();
                    }
                })
                .catch(err => console.error(err));
        });

        // ---- Upsert Task Submit ----
        document.getElementById('upsertTaskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("{{ route('tasks.upsert') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 1) {
                        bootstrap.Modal.getInstance(document.getElementById('upsertTaskModal')).hide();
                        const index = tasksData.findIndex(t => t.id == data.task.id);
                        if (index !== -1) {
                            tasksData[index] = data.task;
                        } else {
                            tasksData.push(data.task);
                        }
                        renderTasks();
                        renderPagination();
                        renderMap();
                    } else {
                        alert(data.message || "Failed to save task");
                    }
                })
                .catch(err => console.error(err));
        });

        let tasksData = [];
        let currentPage = 1;
        const perPage = 12;
        let mapApiReady = false;

        function initTasksMap() {
            mapApiReady = true;
            if (tasksData && tasksData.length >= 0) renderMap();
        }

        const debounce = (fn, delay = 300) => {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => fn(...args), delay);
            };
        };

        async function fetchTasks(search = '') {
            try {
                const res = await fetch(`{{ route('tasks.live') }}?search=${encodeURIComponent(search)}`);
                let json = await res.json();
                tasksData = json.tasks || [];
                currentPage = 1;
                renderTasks();
                renderPagination();
                renderMap();
            } catch (e) {
                console.error('Failed to fetch tasks', e);
                tasksData = [];
                currentPage = 1;
                renderTasks();
                renderPagination();
                renderMap();
            }
        }

        function applyFilter(tasks) {
            if (currentFilter === "open") {
                return tasks.filter(t => !t.device_id || !t.pickup_time_from || !t.pickup_time_to || !t
                    .delivery_time_from || !t.delivery_time_to);
            }
            if (currentFilter === "regular") {
                return tasks.filter(t => t.device_id && t.pickup_time_from && t.pickup_time_to && t.delivery_time_from && t
                    .delivery_time_to);
            }
            return tasks;
        }

        function renderTasks() {
            const container = document.getElementById('tasks-container');
            container.innerHTML = '';

            const filtered = applyFilter(tasksData);
            const start = (currentPage - 1) * perPage;
            const pageItems = filtered.slice(start, start + perPage);

            if (pageItems.length === 0) {
                container.innerHTML = `
                <div class="task-card">
                    <div class="task-header">
                        <div class="task-title">No tasks available</div>
                        <div class="task-status status-pending">—</div>
                    </div>
                    <div class="task-details"><p>You’re all caught up 🎉</p></div>
                </div>`;
                return;
            }

            pageItems.forEach(task => {
                const statusMap = {
                    0: {
                        label: 'Pending',
                        class: 'status-pending'
                    },
                    1: {
                        label: 'Online',
                        class: 'status-online'
                    },

                };
                const status = statusMap[task.status] ?? {
                    label: (task.status ?? '-'),
                    class: 'status-pending'
                };

                const formatDate = dt => dt ? new Date(dt).toISOString().split("T")[0] : '-';
                const formatTime = dt => dt ? new Date(dt).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                }) : '-';

                container.insertAdjacentHTML('beforeend', `
                    <div class="task-card">
                        <div class="task-header d-flex justify-content-between align-items-center">
                            <div class="task-title">${_(task.title ?? 'Task')}</div>
                            <div class="task-actions">
                                @if(in_array($role_id, [1, 2, 3, 6]))
                                <button class="btn btn-sm btn-outline-secondary me-1 duplicate-task" data-id="${task.id}">
                                    <i class="bi bi-files"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning me-1 edit-task" data-id="${task.id}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-task" data-id="${task.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="task-header d-flex justify-content-between align-items-start">
                            <div class="task-meta mb-2" style="font-size:14px;">
                                <p class="mb-1"><i class="bi bi-building me-1"></i> ${_(task.hotel?.name ?? '-')}</p>
                                <p class="mb-1"><i class="bi bi-bus-front me-1"></i> ${_(task.bus?.name ?? task.device_id ?? '-')}</p>
                            </div>
                            <div class="task-status ${status.class}">${_(status.label)}</div>
                        </div>
                        <small class="task-schedule mt-2">
                            <p class="task-date text-secondary mb-1">
                                <i class="bi bi-calendar me-1"></i> ${formatDate(_(task.pickup_time_from))}
                            </p>
                            <div class="trip-line">
                                ${task.pickup_address ?? '-'} (${formatTime(_(task.pickup_time_from))}) → 
                                ${task.delivery_address ?? '-'} (${formatTime(_(task.pickup_time_to))})
                            </div>
                            <div class="trip-line">
                                ${task.delivery_address ?? '-'} (${formatTime(_(task.delivery_time_from))}) → 
                                ${task.pickup_address ?? '-'} (${formatTime(_(task.delivery_time_to))})
                            </div>
                        </small>
                    </div>
                `);
            });
        }

        function renderPagination() {
            const filtered = applyFilter(tasksData);
            const totalPages = Math.ceil(filtered.length / perPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';
            if (totalPages <= 1) return;

            for (let i = 1; i <= totalPages; i++) {
                pagination.insertAdjacentHTML('beforeend', `
                <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-light'} mx-1"
                        onclick="goToPage(${i})">${i}</button>
            `);
            }
        }

        function goToPage(page) {
            currentPage = page;
            renderTasks();
            renderPagination();
        }

        function renderMap() {
            if (!mapApiReady) return;
            const map = new google.maps.Map(document.getElementById('tasks-map'), {
                center: {
                    lat: 36.7601,
                    lng: 3.0503
                },
                zoom: 6,
                gestureHandling: "cooperative"
            });
            const bounds = new google.maps.LatLngBounds();

            applyFilter(tasksData).forEach(task => {
                const pLat = Number(task.pickup_address_lat);
                const pLng = Number(task.pickup_address_lng);
                const dLat = Number(task.delivery_address_lat);
                const dLng = Number(task.delivery_address_lng);

                if (!Number.isNaN(pLat) && !Number.isNaN(pLng)) {
                    const pickupPos = {
                        lat: pLat,
                        lng: pLng
                    };
                    const pickupMarker = new google.maps.Marker({
                        position: pickupPos,
                        map,
                        title: `Pickup: ${_(task.title ?? '')}`,
                        icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                    });
                    const pickupInfo = new google.maps.InfoWindow({
                        content: `<div class='text-dark'><strong>${_(task.title ?? 'Task')}</strong><br>Pickup: ${_(task.pickup_address ?? '-')}</div>`,
                    });
                    pickupMarker.addListener("click", () => pickupInfo.open(map, pickupMarker));
                    bounds.extend(pickupPos);
                }

                if (!Number.isNaN(dLat) && !Number.isNaN(dLng)) {
                    const deliveryPos = {
                        lat: dLat,
                        lng: dLng
                    };
                    const deliveryMarker = new google.maps.Marker({
                        position: deliveryPos,
                        map,
                        title: `Delivery: ${task.title ?? ''}`,
                        icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                    });
                    const deliveryInfo = new google.maps.InfoWindow({
                        content: `<div class='text-dark'><strong>${_(task.title ?? 'Task')}</strong><br>Delivery: ${_(task.delivery_address ?? '-')}</div>`,
                    });
                    deliveryMarker.addListener("click", () => deliveryInfo.open(map, deliveryMarker));
                    bounds.extend(deliveryPos);
                }
            });

            if (!bounds.isEmpty()) map.fitBounds(bounds);
        }

        document.getElementById('searchInput').addEventListener('input', debounce(e => {
            fetchTasks(e.target.value);
        }, 300));

        fetchTasks();
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=initTasksMap" async
        defer></script>

    <style>
        .tasks-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(23em, 1fr));
            gap: 20px;
        }

        .task-card {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            animation: slideUp 0.5s ease-out;
        }

        [data-theme="dark"] .task-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .task-title {
            font-weight: 600;
            color: var(--text-color);
        }

        .task-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-online {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
        }

        .status-offline {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }

        .task-meta p {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .trip-line {
            margin: 2px 0;
            padding-left: 10px;
            position: relative;
        }

        .trip-line::before {
            content: "•";
            position: absolute;
            left: 0;
            color: var(--primary, #3b82f6);
            font-size: 14px;
            line-height: 1;
        }

        /* Tabs light mode */
        .nav-tabs .nav-link {
            color: #111827;
            /* gray-900 */
        }

        /* Tabs dark mode */
        [data-theme="dark"] .nav-tabs .nav-link {
            color: #9ca3af;
            /* gray-400 for inactive */
        }

        [data-theme="dark"] .nav-tabs .nav-link.active {
            color: #f9fafb;
            /* white for active */
            background-color: transparent;
            border-color: #4b5563 #4b5563 #1e293b;
            /* subtle gray borders */
        }
    </style>
@endpush
