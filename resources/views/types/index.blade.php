@extends('layouts.app')

@section('title', 'Types')

@section('content')
    <div class="mobile-padding">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb glass-card p-3 mb-4">
                <li class="breadcrumb-item">
                    <a href="{{ oRoute('types.index') }}">Types</a>
                </li>

                @if ($type)
                    @php
                        $ancestors = [];
                        $current = $type;
                        while ($current) {
                            $ancestors[] = $current;
                            $current = $current->parent;
                        }
                        $ancestors = array_reverse($ancestors);
                    @endphp

                    @foreach ($ancestors as $ancestor)
                        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                            @if (!$loop->last)
                                <a href="{{ oRoute('types.index', $ancestor->id) }}">{{ $ancestor->name }}</a>
                            @else
                                {{ $ancestor->name }}
                            @endif
                        </li>
                    @endforeach
                @endif
            </ol>
        </nav>

        <!-- Search + Add -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="typeSearch" class="form-control" placeholder="Search by name...">
            <button class="btn btn-primary w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertTypeModal">
                <i class="bi bi-plus-circle me-2"></i> New
            </button>
        </div>

        <!-- Types Grid -->
        <div id="types-container" class="tasks-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>

    <!-- Upsert Modal -->
    <div class="modal fade" id="upsertTypeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Upsert Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="upsertTypeForm">
                    @csrf
                    <input type="hidden" name="id" id="type_id">
                    <input type="hidden" name="type_id" value="{{ $type->id ?? '' }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Status</label>
                            <select class="form-select" name="status" id="type_status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="type_name" required>
                            <span class="text-danger name-error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteTypeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteTypeForm" method="POST">
                    @csrf @method('delete')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this type?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            transition: all 0.3s;
        }

        [data-theme="dark"] .task-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] .modal-content {
            background: rgba(30, 41, 59, 0.95);
            color: #f1f5f9;
        }

        [data-theme="dark"] .breadcrumb,
        [data-theme="dark"] .breadcrumb-item.active,
        [data-theme="dark"] .breadcrumb-item::before {
            background: rgba(30, 41, 59, 0.7);
            border-radius: 8px;
            color: #f1f5f9;
        }

        .task-card:hover {
            transform: translateY(-5px);
        }
    </style>

    <script>
        let typesData = @json($types ?? []);
        let currentPage = 1;
        const perPage = 9;

        function renderTypes() {
            const container = document.getElementById('types-container');
            container.innerHTML = '';

            const start = (currentPage - 1) * perPage;
            const pageItems = typesData.slice(start, start + perPage);

            if (pageItems.length === 0) {
                container.innerHTML = `<div class="task-card">No types found</div>`;
                return;
            }

            pageItems.forEach(type => {
                container.insertAdjacentHTML('beforeend', `
                    <div class="task-card">
                        <div class="d-flex justify-content-between mb-2">
                            <h6>${_(type.name)}</h6>
                            <span class="badge ${_(type.status == 1 ? 'bg-success' : 'bg-danger')}">
                                ${_(type.status == 1 ? 'Active' : 'Inactive')}
                            </span>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ oRoute('types.index') }}/${_(type.id)}">
                                <i class="bi bi-stack"></i> 
                                <small>${_(type.sub_types_count ?? 0)}</small>
                            </a>
                            <button class="btn btn-sm btn-outline-warning"
                                    onclick="openEdit(${_(type.id)}, '${_(type.name)}', ${_(type.status)})">
                                <i class="bi bi-pencil-square"></i> 
                            </button>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="openDelete(${_(type.id)})">
                                <i class="bi bi-trash"></i> 
                            </button>
                        </div>
                    </div>
                `);
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(typesData.length / perPage);
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
            renderTypes();
            renderPagination();
        }

        // ---- Upsert ----
        document.getElementById('upsertTypeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch("{{ oRoute('types.upsert') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) location.reload();
                })
                .catch(err => console.error(err));
        });

        // ---- Delete ----
        function openDelete(id) {
            document.getElementById('deleteTypeForm').action = "{{ url('types/delete') }}/" + id;
            new bootstrap.Modal(document.getElementById('deleteTypeModal')).show();
        }

        // ---- Edit ----
        function openEdit(id, name, status) {
            document.getElementById('type_id').value = id;
            document.getElementById('type_name').value = name;
            document.getElementById('type_status').value = status;
            new bootstrap.Modal(document.getElementById('upsertTypeModal')).show();
        }

        // ---- Search ----
        document.getElementById('typeSearch').addEventListener('input', function(e) {
            const q = e.target.value.toLowerCase();
            typesData = @json($types ?? []).filter(t => t.name.toLowerCase().includes(q));
            currentPage = 1;
            renderTypes();
            renderPagination();
        });

        // Init
        renderTypes();
        renderPagination();
    </script>
@endpush
