@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Laporan Penjualan Cabang</h4>
        @if ($penjualan->count())
            <form method="GET" action="{{ route('sales.export-pdf') }}" style="display: inline;">
                <input type="hidden" name="tanggal_awal" value="{{ $tanggalAwal }}">
                <input type="hidden" name="tanggal_akhir" value="{{ $tanggalAkhir }}">
                <input type="hidden" name="branch_id" value="{{ $branchId ?? '' }}">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </form>
        @endif
    </div>

    <form method="GET" class="row mb-3">
        <div class="col-md-3">
            <label>Tanggal Awal</label>
            <input type="date" name="tanggal_awal" class="form-control" value="{{ $tanggalAwal }}">
        </div>
        <div class="col-md-3">
            <label>Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir }}">
        </div>
        <div class="col-md-3">
            <label>Cabang</label>
            <select name="branch_id" class="form-control">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ ($branchId == $branch->id) ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100">
                <i class="fas fa-search"></i> Tampilkan
            </button>
        </div>
    </form>

    @if ($penjualan->count())
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Pendapatan</h5>
                    <h3>Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Laba</h5>
                    <h3>Rp{{ number_format($totalLaba, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Data Penjualan
                @if($tanggalAwal || $tanggalAkhir)
                    - {{ $tanggalAwal ? \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') : 'Awal' }} 
                    s/d {{ $tanggalAkhir ? \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') : 'Sekarang' }}
                @endif
                @if($branchId)
                    - {{ $branches->find($branchId)->name ?? 'Cabang' }}
                @endif
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Cabang</th>
                            <th>Produk</th>
                            <th>IMEI</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Laba</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($penjualan as $sale)
                            @foreach ($sale->items as $item)
                                @php
                                    $hargaJual = $item->price;
                                    $hargaBeli = $item->inventoryItem->purchaseItem->price ?? 0;
                                    $laba = $hargaJual - $hargaBeli;
                                @endphp
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $sale->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $sale->branch->name ?? '-' }}</td>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td>{{ $item->imei }}</td>
                                    <td>Rp{{ number_format($hargaBeli, 0, ',', '.') }}</td>
                                    <td>Rp{{ number_format($hargaJual, 0, ',', '.') }}</td>
                                    <td class="{{ $laba >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp{{ number_format($laba, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-2x mb-2"></i>
            <h5>Tidak ada data penjualan</h5>
            <p class="mb-0">Tidak ada data penjualan untuk filter yang dipilih.</p>
        </div>
    @endif
</div>
@endsection