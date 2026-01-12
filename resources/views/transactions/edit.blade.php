@extends('layouts.main')

@section('title', 'Edit Transaksi')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <x-breadcrumb :breadcrumbs="[
                    'Home' => route('dashboard'),
                    'Transaksi' => route('transactions.index'),
                    'Edit Transaksi' => '#'
                ]"/>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
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

            <div class="card shadow-sm border-0">
                <div class="card-header py-3">
                    <div class="d-flex align-items-center text-primary">
                        <i class="ti ti-edit me-2 fs-5"></i>
                        <h5 class="mb-0 fw-semibold">Edit Transaksi #{{ $transaction->transaction_id }}</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('transactions.update', $transaction->transaction_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal</label>
                                    <input type="date"
                                           name="date"
                                           class="form-control @error('date') is-invalid @enderror"
                                           value="{{ old('date', optional($transaction->date)->format('Y-m-d')) }}"
                                           required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" name="id" value="{{ old('id', $transaction->id) }}">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Produk</label>
                                    <input type="text"
                                           name="product_name"
                                           class="form-control @error('product_name') is-invalid @enderror"
                                           value="{{ old('product_name', $transaction->product_name) }}"
                                           readonly>
                                    @error('product_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ukuran</label>
                                    <input type="text"
                                           name="size"
                                           class="form-control @error('size') is-invalid @enderror"
                                           value="{{ old('size', $transaction->size) }}"
                                           readonly>
                                    @error('size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Quantity</label>
                                        <input type="number"
                                               name="qty"
                                               min="1"
                                               class="form-control @error('qty') is-invalid @enderror"
                                               value="{{ old('qty', $transaction->qty) }}"
                                               required>
                                        @error('qty')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Harga Satuan (Rp)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="number"
                                                   name="price"
                                                   min="0"
                                                   step="0.01"
                                                   class="form-control @error('price') is-invalid @enderror"
                                                   value="{{ old('price', $transaction->price) }}"
                                                   readonly>
                                            @error('price')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Paid (Rp)</label>
                                    <input type="number"
                                           name="paid"
                                           min="0"
                                           step="0.01"
                                           class="form-control @error('paid') is-invalid @enderror"
                                           value="{{ old('paid', $transaction->paid) }}">
                                    @error('paid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Metode Pembayaran</label>
                                    <input type="text"
                                           name="payment_method"
                                           class="form-control @error('payment_method') is-invalid @enderror"
                                           value="{{ old('payment_method', $transaction->payment_method) }}">
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jatuh Tempo Pembayaran</label>
                                    <input type="date"
                                           name="due_date_payment"
                                           class="form-control @error('due_date_payment') is-invalid @enderror"
                                           value="{{ old('due_date_payment', optional($transaction->due_date_payment)->format('Y-m-d')) }}">
                                    @error('due_date_payment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Status</label>
                                    <input type="text"
                                           name="status"
                                           class="form-control @error('status') is-invalid @enderror"
                                           value="{{ old('status', $transaction->status) }}">
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="ti ti-device-floppy"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


