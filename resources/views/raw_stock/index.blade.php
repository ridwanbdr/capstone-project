@extends('layouts.main') {{-- atau layout yang kamu pakai --}}
@section('title', 'Raw Stock Material')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-3xl font-bold underline mb-4">
                        Material Stock Form
                    </h5>
                    <div class="container">
                        {{-- Form input di atas --}}
                        @include('raw_stock.form')                        
                    </div>
                </div>                
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="text-3xl font-bold underline mb-4">
                                Table Material Stock
                            </h5>
                        </div>
                        <div class="col-6">
                            {{-- Form pencarian di atas tabel --}}
                            <div class="mb-4 d-flex justify-content-end align-items-end">
                                <form action="{{ route('raw_stock.index') }}" method="GET" class="d-flex gap-2">
                                    <div class="flex-grow-1">
                                        <input type="text" 
                                            name="search" 
                                            class="form-control" 
                                            placeholder="Cari stok.." 
                                            value="{{ request('search') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                    @if(request('search'))
                                        <a href="{{ route('raw_stock.index') }}" class="btn btn-secondary">Reset</a>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>                    
                    {{-- Tabel data di bawah --}}
                    @include('raw_stock.table')
                </div>
            </div>
        </div>
    </div>
@endsection