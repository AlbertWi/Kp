@extends('layouts.app')

@section('title', 'Data Pembelian')

@section('content')
<div class="container">
    <h4 class="mb-4">Data Pembelian</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="supplier_id" class="form-label"><strong>Supplier</strong></label>
            <select name="supplier_id" id="supplier_id" class="form-control">
                <option value="">-- Pilih Supplier --</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Tanggal</label>
            <input type="text" class="form-control" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" readonly>
        </div>

        <hr>
        <h5 class="mb-3">Produk</h5>

        <div id="product-wrapper">
            @php $oldItems = old('items', [ [] ]); @endphp
            @foreach ($oldItems as $i => $item)
                <div class="row mb-2 product-row">
                    <div class="col-md-4">
                        <select name="items[{{ $i }}][product_id]" class="form-control">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ (isset($item['product_id']) && $item['product_id'] == $product->id) ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[{{ $i }}][qty]" class="form-control" placeholder="Qty" min="1" value="{{ $item['qty'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="items[{{ $i }}][price]" class="form-control price-input" placeholder="Harga Satuan" value="{{ isset($item['price']) ? number_format($item['price'], 0, ',', '.') : '' }}">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-row">Hapus</button>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" id="add-product" class="btn btn-secondary mt-2">+ Tambah Produk</button>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan Pembelian</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let index = {{ count(old('items', [ [] ])) }};

    document.getElementById('add-product').addEventListener('click', function () {
        const wrapper = document.getElementById('product-wrapper');

        const row = document.createElement('div');
        row.classList.add('row', 'mb-2', 'product-row');

        row.innerHTML = `
            <div class="col-md-4">
                <select name="items[${index}][product_id]" class="form-control">
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][qty]" class="form-control" placeholder="Qty" min="1">
            </div>
            <div class="col-md-3">
                <input type="text" name="items[${index}][price]" class="form-control price-input" placeholder="Harga Satuan">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-row">Hapus</button>
            </div>
        `;

        wrapper.appendChild(row);
        index++;
    });

    document.getElementById('product-wrapper').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.product-row').remove();
        }
    });

    // Format angka harga saat input
    function formatPriceInput(input) {
        input.value = input.value
            .replace(/[^0-9]/g, '')
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('price-input')) {
            formatPriceInput(e.target);
        }
    });

    // Bersihkan koma sebelum submit
    document.querySelector('form').addEventListener('submit', function() {
        document.querySelectorAll('.price-input').forEach(function(input) {
            input.value = input.value.replace(/,/g, '');
        });
    });
</script>
@endpush
