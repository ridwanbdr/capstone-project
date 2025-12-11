@extends('layouts.main')

@section('title', 'Edit Quality Control')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="ti ti-checks me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="card shadow-sm border-0">
            <div class="card-header py-3">
                <div class="d-flex align-items-center text-primary">
                    <i class="ti ti-edit me-2 fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Edit Quality Control #{{ $qcCheck->qc_id }}</h5>
                </div>
            </div>
            <div class="card-body p-4">
                @include('qc_check.form', ['qcCheck' => $qcCheck])
            </div>
        </div>
    </div>
</div>
@endsection
