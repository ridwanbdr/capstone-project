@extends('layouts.main')
@section('title', 'Production')

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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header py-3">
                    <div class="d-flex align-items-center text-primary">
                        <i class="ti ti-clipboard-text me-2 fs-5"></i>
                        <h5 class="mb-0 fw-semibold">Tambah Data Production</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    @include('productions.form')
                </div>
            </div>

            {{-- Table Card --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-table-options me-2 text-primary fs-5"></i>
                                <h5 class="mb-0 fw-semibold">Daftar Production</h5>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <form action="{{ route('production.index') }}" method="GET" class="d-flex gap-2">
                                <div class="input-group flex-grow-1">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text"
                                           name="search"
                                           class="form-control border-start-0"
                                           placeholder="Cari berdasarkan label / lead / material..."
                                           value="{{ request('search') }}">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> Cari
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('production.index') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-x"></i> Reset
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @include('productions.table')
                </div>
            </div>
        </div>
    </div>
@endsection