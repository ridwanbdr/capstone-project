@extends('layouts.main')

@section('title', 'Edit Quality Control')

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-4">Edit Quality Control #{{ $qcCheck->qc_id }}</h5>
        @include('qc_check.form', [
          'action' => route('qc_check.update', $qcCheck->qc_id),
          'isEdit' => true,
          'qcCheck' => $qcCheck
        ])
      </div>
    </div>
  </div>
</div>
@endsection
