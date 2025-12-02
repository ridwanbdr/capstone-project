@extends('layouts.main')

@section('title', 'Quality Control')

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
          <div class="mb-3 mb-sm-0">
            <h5 class="card-title fw-semibold">Daftar Quality Control</h5>
          </div>
          <a href="{{ route('qc_check.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Tambah QC Check
          </a>
        </div>

        @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="table-responsive">
          @include('qc_check.table')
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
