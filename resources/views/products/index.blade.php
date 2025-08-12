@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Product Management</h2>
    <button class="btn btn-primary mb-3" id="btnAdd">Add Product</button>
    <table class="table table-bordered" id="productsTable">
        <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="productModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="productForm">
                <div class="modal-header"><h5 class="modal-title"></h5></div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="productId">
                    <div class="mb-2"><label>Name</label><input type="text" name="name" class="form-control"></div>
                    <div class="mb-2"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
                    <div class="mb-2"><label>Price</label><input type="number" name="price" class="form-control" step="0.01"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success">Save</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Show Modal -->
<div class="modal fade" id="showModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Product Details</h5></div>
            <div class="modal-body">
                <p><b>Name:</b> <span id="showName"></span></p>
                <p><b>Description:</b> <span id="showDescription"></span></p>
                <p><b>Price:</b> $<span id="showPrice"></span></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    let table = $('#productsTable').DataTable({
        ajax: "{{ route('products.fetch') }}",
        columns: [
            {data: 'id'},
            {data: 'name'},
            {data: 'description'},
            {data: 'price'},
            {data: null, render: function(data) {
                return `
                    <button class="btn btn-info btn-sm btnShow" data-id="${data.id}">Show</button>
                    <button class="btn btn-warning btn-sm btnEdit" data-id="${data.id}">Edit</button>
                    <button class="btn btn-danger btn-sm btnDelete" data-id="${data.id}">Delete</button>
                `;
            }}
        ]
    });

    $('#btnAdd').click(function() {
        $('#productModal').modal('show');
        $('#productForm')[0].reset();
        $('.modal-title').text('Add Product');
        $('#productId').val('');
    });

    $('#productForm').submit(function(e) {
        e.preventDefault();
        let id = $('#productId').val();
        let url = id ? `/products/update/${id}` : "{{ route('products.store') }}";
        $.post(url, $(this).serialize(), function() {
            $('#productModal').modal('hide');
            table.ajax.reload();
        });
    });

    $(document).on('click', '.btnShow', function() {
        $.get(`/products/show/${$(this).data('id')}`, function(res) {
            $('#showName').text(res.name);
            $('#showDescription').text(res.description);
            $('#showPrice').text(res.price);
            $('#showModal').modal('show');
        });
    });

    $(document).on('click', '.btnEdit', function() {
        $.get(`/products/show/${$(this).data('id')}`, function(res) {
            $('#productId').val(res.id);
            $('[name="name"]').val(res.name);
            $('[name="description"]').val(res.description);
            $('[name="price"]').val(res.price);
            $('.modal-title').text('Edit Product');
            $('#productModal').modal('show');
        });
    });

    $(document).on('click', '.btnDelete', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Delete this product?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/products/delete/${id}`,
                    type: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function() {
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});
</script>
@endpush
