@extends('layouts.main')

@section('title', 'Quality Control Management')

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

        {{-- Production Status Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header py-3">
                <div class="d-flex align-items-center text-primary">
                    <i class="ti ti-checklist me-2 fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Status QC per Production</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="{{ route('qc_check.index') }}" class="btn btn-sm {{ empty($completionFilter) ? 'btn-primary' : 'btn-outline-primary' }}">
                        Semua
                    </a>
                    <a href="{{ route('qc_check.index', ['completion' => 'pending']) }}" class="btn btn-sm {{ ($completionFilter ?? '') === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Belum Selesai
                    </a>
                    <a href="{{ route('qc_check.index', ['completion' => 'completed']) }}" class="btn btn-sm {{ ($completionFilter ?? '') === 'completed' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Selesai
                    </a>
                </div>

                <div class="row g-3">
                    @forelse($productions as $production)
                        @php
                            $status = $productionStatuses[$production->production_id] ?? ['total' => 0, 'checked' => 0, 'completed' => false, 'percentage' => 0];
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="card border {{ $status['completed'] ? 'border-success' : 'border-warning' }} h-100 position-relative">
                                <a href="{{ route('qc_check.index', array_filter(['production_id' => $production->production_id, 'completion' => $completionFilter])) }}"
                                   class="stretched-link"></a>
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $production->production_label }}</h6>
                                            <small class="text-muted d-block">ID: {{ $production->production_id }}</small>
                                        </div>
                                        <div class="d-flex gap-1">
                                            @if($status['completed'])
                                                <span class="badge bg-success">
                                                    <i class="ti ti-check"></i> Selesai
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="ti ti-clock"></i> Belum
                                                </span>
                                            @endif
                                            <form action="{{ route('qc_check.destroy_production', $production->production_id) }}" method="POST" onsubmit="return confirm('Hapus semua QC untuk production ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger p-1" title="Hapus semua QC">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar {{ $status['completed'] ? 'bg-success' : 'bg-warning' }}" 
                                             role="progressbar" 
                                             style="width: {{ $status['percentage'] }}%"
                                             aria-valuenow="{{ $status['percentage'] }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <strong>{{ $status['checked'] }}</strong> / <strong>{{ $status['total'] }}</strong> produk
                                        </small>
                                        <small class="fw-semibold {{ $status['completed'] ? 'text-success' : 'text-warning' }}">
                                            {{ $status['percentage'] }}%
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center text-muted py-4">
                                Tidak ada production untuk filter ini.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        @if($selectedProductionId)
            {{-- Form Card --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header py-3">
                    <div class="d-flex align-items-center text-primary">
                        <i class="ti ti-clipboard-check me-2 fs-5"></i>
                        <h5 class="mb-0 fw-semibold">Tambah Quality Control</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    @include('qc_check.form', ['qcCheck' => null])
                </div>
            </div>

            {{-- Table Card --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-table-options me-2 text-primary fs-5"></i>
                        <h5 class="mb-0 fw-semibold">Daftar Quality Control</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @include('qc_check.table')
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
