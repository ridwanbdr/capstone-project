@extends('layouts.main')

@section('title', 'Tambah Quality Control')

@section('content')
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <x-breadcrumb :breadcrumbs="[
                    'Home' => route('dashboard'),
                    'Quality Control' => route('qc_check.index'),
                    'Tambah QC' => '#'
                ]"/>
            </div>
        </div>
    </div>

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
                    <i class="ti ti-clipboard-check me-2 fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Tambah Quality Control Baru</h5>
                </div>
            </div>
            <div class="card-body p-4">
                @include('qc_check.form', ['qcCheck' => null])
            </div>
        </div>
    </div>
</div>
@endsection
