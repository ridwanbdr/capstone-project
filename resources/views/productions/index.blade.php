@extends('layouts.main')
@section('title', 'Production')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-3xl font-bold underline mb-4">
                        Production Form
                    </h5>
                    <div class="container">
                        {{-- Form input di atas --}}
                        @include('productions.form')                        
                    </div>
                </div>                
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="text-3xl font-bold underline mb-4">
                                Table Production
                            </h5>
                        </div>
                        <div class="col-6">
                            {{-- Form pencarian di atas tabel --}}
                            <div class="mb-4 d-flex justify-content-end align-items-end">
                                <form action="{{ route('production.index') }}" method="GET" class="d-flex gap-2">
                                    <div class="flex-grow-1">
                                        <input type="text" 
                                            name="search" 
                                            class="form-control" 
                                            placeholder="Cari production.." 
                                            value="{{ request('search') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                    @if(request('search'))
                                        <a href="{{ route('production.index') }}" class="btn btn-secondary">Reset</a>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Alert messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    {{-- Tabel data di bawah --}}
                    @include('productions.table')
                </div>
            </div>
        </div>
    </div>
@endsection

