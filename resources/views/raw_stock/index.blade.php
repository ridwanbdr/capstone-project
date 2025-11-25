@extends('layouts.main') {{-- atau layout yang kamu pakai --}}
@section('title', 'Raw Stock Material')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-3xl font-bold underline mb-4">
                        Raw Material Stock List
                    </h5>
                    <div class="container">
                        {{-- Form input di atas --}}
                        @include('raw_stock.form')

                        {{-- Tabel data di bawah --}}
                        @include('raw_stock.table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection