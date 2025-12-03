@extends('layouts.main')

@section('title', 'Quality Control Management')

@section('content')
<div class="row">
  <div class="col-lg-12 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="card-title fw-semibold mb-0">Daftar Quality Control</h5>
          <a href="{{ route('qc_check.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Tambah QC Check
          </a>
        </div>

        @if ($message = Session::get('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check"></i>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @include('qc_check.table')
      </div>
    </div>
  </div>
</div>

<!-- Delete Form -->
@foreach($qcChecks as $qc)
  <form id="delete-form-{{ $qc->qc_id }}" action="{{ route('qc_check.destroy', $qc->qc_id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
  </form>
@endforeach
@endsection
