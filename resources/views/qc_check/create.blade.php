@extends('layouts.main')

@section('title', 'Tambah Quality Control')

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-4">Tambah Quality Control Baru</h5>
        @include('qc_check.form', [
          'action' => route('qc_check.store'),
          'isEdit' => false,
          'qcCheck' => null
        ])
      </div>
    </div>
  </div>
</div>
@endsection
