@extends('layouts.main')
@section('title', 'Detail Produk')

@section('content')
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            @if(empty($production_id))
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <x-breadcrumb :breadcrumbs="[
                        'Home' => route('dashboard'),
                        'Produksi' => route('production.index'),
                        'Detail Produk' => route('detail_product.index'),
                        'Daftar Production' => '#'
                    ]"/>
                </div>
            @else
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <x-breadcrumb :breadcrumbs="[
                        'Home' => route('dashboard'),
                        'Produksi' => route('production.index'),
                        'Detail Produk' => route('detail_product.index'),
                        $productionLabel => '#'
                    ]"/>
                </div>
            @endif
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Show Production Cards if no production selected --}}
            @if(empty($production_id))
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header py-3">
                        <div class="d-flex align-items-center text-primary">
                            <i class="ti ti-package me-2 fs-5"></i>
                            <h5 class="mb-0 fw-semibold">Pilih Production untuk Mengelola Produk</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            @forelse($productions ?? [] as $production)
                                @php
                                    $stats = $productionStats[$production->production_id] ?? [
                                        'product_count' => 0,
                                        'total_qty' => 0,
                                        'total_limit' => 0,
                                        'remaining_unit' => 0,
                                        'percentage' => 0
                                    ];
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border h-100 shadow-sm hover-shadow transition-all cursor-pointer" 
                                         onclick="window.location.href='{{ route('detail_product.index', ['production_id' => $production->production_id]) }}'"
                                         style="cursor: pointer;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 fw-semibold">{{ $production->production_label }}</h6>
                                                <span class="badge bg-primary">ID: {{ $production->production_id }}</span>
                                            </div>
                                            <small class="text-muted d-block mb-2">
                                                <i class="ti ti-user me-1"></i>{{ $production->production_lead }}
                                            </small>
                                            <small class="text-muted d-block mb-3">
                                                <i class="ti ti-calendar me-1"></i>{{ \Carbon\Carbon::parse($production->production_date)->format('d M Y') }}
                                            </small>
                                            
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted">Progress Produk Selesai</small>
                                                    <small class="fw-semibold">{{ $stats['percentage'] }}%</small>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar {{ $stats['percentage'] >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min($stats['percentage'], 100) }}%"
                                                         aria-valuenow="{{ $stats['percentage'] }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row g-2 mt-2">
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Total Produk</small>
                                                    <span class="fw-bold text-primary">{{ $stats['product_count'] }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Total Unit</small>
                                                    <span class="fw-bold">{{ number_format($stats['total_qty']) }} / {{ number_format($stats['total_limit']) }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3 pt-2 border-top">
                                                <small class="text-muted d-block">Sisa Unit</small>
                                                <span class="fw-bold {{ $stats['remaining_unit'] > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($stats['remaining_unit']) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="ti ti-package-off fs-1 text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">Belum ada production yang tersedia</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                {{-- Show Form and Table for selected production --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center text-primary">
                                <i class="ti ti-clipboard-text me-2 fs-5"></i>
                                <h5 class="mb-0 fw-semibold">
                                    Detail Production - {{ $productionLabel }}
                                </h5>
                            </div>
                            <a href="{{ route('detail_product.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-arrow-left me-1"></i> Kembali ke List Production
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @include('detail_product.form')
                    </div>
                </div>

                {{-- Table Card --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-table-options me-2 text-primary fs-5"></i>
                                    <h5 class="mb-0 fw-semibold">Data Detail Production</h5>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <form action="{{ route('detail_product.index') }}" method="GET" class="d-flex gap-2">
                                    <input type="hidden" name="production_id" value="{{ $production_id }}">
                                    <input type="hidden" name="production_label" value="{{ $productionLabel ?? '' }}">

                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input type="text"
                                               name="search"
                                               class="form-control border-start-0"
                                               placeholder="Cari produk..."
                                               value="{{ request('search') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-search"></i> Cari
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('detail_product.index', ['production_id' => $production_id, 'production_label' => $productionLabel]) }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x"></i> Reset
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @include('detail_product.table')
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
