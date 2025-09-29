@extends('layouts.app')

@section('title', 'Complaints - TFA')

@php
    $role_id = auth()->user()->profile->role_id;
@endphp

@section('content')
    <div class="mobile-padding">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Complaints </h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#upsertComplaintModal">
                <i class="bi bi-plus-circle me-2"></i> New
            </button>
        </div>

        <!-- Complaints Grid -->
        <div id="complaints-container" class="tasks-container">
            @forelse ($complaints as $complaint)
                <div class="task-card">
                    <div class="d-flex justify-content-between mb-2">
                        <h6>{{ $complaint->subject }}</h6>
                        <span
                            class="claim-status  @if ($complaint->status->name === 'In Progress') status-in-progress @elseif($complaint->status->name === 'Resolved') status-resolved @else status-open @endif">
                            {{ ucfirst($complaint->status->name) }}
                        </span>
                    </div>
                    <p class="small mb-2">{{ $complaint->body }}</p>
                    @if (!in_array($role_id, [10]))
                        <small class="text-secondary d-block">
                            Created by: {{ $complaint->user->name ?? 'Unknown' }}
                            on {{ $complaint->created_at ? $complaint->created_at->format('Y-m-d H:i:s') : '-' }}
                        </small>
                        <div class="d-flex justify-content-between align-items-end mt-2">
                            <small class="text-secondary">
                                Decided by: {{ $complaint->decider->name ?? '-' }}
                                @if ($complaint->decided_at)
                                    on {{ $complaint->decided_at->format('Y-m-d H:i:s') }}
                                @endif
                            </small>
                            <div class="mt-3 d-flex gap-2">
                                <button class="btn btn-sm btn-outline-warning" onclick="openEdit({{ $complaint->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="openDelete({{ $complaint->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="task-card">No complaints found</div>
            @endforelse
        </div>

    </div>

    <!-- Upsert Modal -->
    <div class="modal fade" id="upsertComplaintModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Upsert Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="upsertComplaintForm">
                    @csrf
                    <input type="hidden" name="id" id="complaint_id">

                    <div class="modal-body">
                        <div class="mb-3" {{ in_array($role_id, [10]) ? 'hidden' : '' }}>
                            <label for="complaint-status" class="form-label">Status</label>
                            <select class="form-select" name="status_id" id="complaint-status">
                                @foreach ($status as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-7">
                                <label for="complaint-subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="complaint-subject" name="subject"
                                    {{ in_array($role_id, [10]) ? '' : 'disabled' }}>
                            </div>
                            <div class="col-md-5">
                                <label for="complaint-type" class="form-label">Type</label>
                                <select class="form-control" name="type_id" id="complaint-type"
                                    {{ in_array($role_id, [10]) ? '' : 'disabled' }} required >
                                    @foreach ($types as $type)
                                        <optgroup label="{{ $type->name }}">
                                            @foreach ($type->subTypes as $subType)
                                                <option value="{{ $subType->id }}"> {{ $subType->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <span class="text-danger brand_id-error"></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="complaint-body" class="form-label">Body</label>
                            <textarea class="form-control" id="complaint-body" name="body" rows="6"
                                {{ in_array($role_id, [10]) ? '' : 'disabled' }} required></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save Complaint</button>
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteComplaintModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteComplaintForm" method="POST">
                    @csrf @method('delete')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this complaint?</p>
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

        .task-card:hover {
            transform: translateY(-5px);
        }

        [data-theme="dark"] .task-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] .modal-content {
            background: rgba(30, 41, 59, 0.95);
            color: #f1f5f9;
        }
    </style>

    <script>
        // ---- Edit ----
        function openEdit(id) {
            fetch(`{{ oRoute('complaints.index') }}/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('complaint_id').value = data.id;
                    document.getElementById('complaint-status').value = data.status_id ?? '';
                    document.getElementById('complaint-type').value = data.type_id ?? '';
                    document.getElementById('complaint-subject').value = data.subject;
                    document.getElementById('complaint-body').value = data.body;
                    new bootstrap.Modal(document.getElementById('upsertComplaintModal')).show();
                });
        }

        // ---- Upsert ----
        document.getElementById('upsertComplaintForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch("{{ oRoute('complaints.upsert') }}", {
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
            document.getElementById('deleteComplaintForm').action = "{{ url('complaints/delete') }}/" + id;
            new bootstrap.Modal(document.getElementById('deleteComplaintModal')).show();
        }
    </script>
@endpush
