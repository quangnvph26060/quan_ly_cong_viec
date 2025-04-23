@extends('admins.layouts.index')

@section('content')
    <!-- Thêm CSS để căn giữa modal -->
    <style>
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }
    </style>

    <div class="page-content" id="content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Hoá đơn bán ra</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                              
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="">Mã số thuế</label>
                                            <input value="{{ request('query') }}" autocomplete="off" name="query"
                                                placeholder="Mã số thuế" type="text" class="form-control"
                                                id="mst">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="">Tên công ty</label>
                                            <input value="{{ request('query') }}" autocomplete="off" name="query"
                                                placeholder="Tên công ty" type="text" class="form-control"
                                                id="tencongty">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="">Từ ngày</label>
                                            <input value="{{ request('query') }}" autocomplete="off" name="query"
                                                placeholder="Nhập từ khóa tìm kiếm" type="date" class="form-control"
                                                id="start_date">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label for="">Đến ngày</label>
                                            <input value="{{ request('query') }}" autocomplete="off" name="query"
                                                placeholder="Nhập từ khóa tìm kiếm" type="date" class="form-control"
                                                id="end_date">
                                        </div>
                                    </div>
                               
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="" style="opacity: 0">1</label> <br>
                                        <button type="button" id="search-btn" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Tìm kiếm
                                        </button>
                                        <a href="{{ url()->current() }}" class="btn btn-danger">
                                            <i class="fas fa-history"></i> Tải lại
                                        </a>
                                        {{-- <button type="button" class="btn btn-success" id="open-add-modal">
                                            <i class="fas fa-plus"></i> Thêm mới
                                        </button> --}}
                                
                                        <form id="import-form" action="{{ route('admin.invoice.invoice.import', ['type' => 'sales']) }}" method="POST" enctype="multipart/form-data" style="display: inline-block;">
                                            @csrf
                                            <input type="file" id="file-input-import" name="file" class="d-none">
                                            <button type="button" class="btn btn-primary" onclick="triggerFileImport()">Import</button>
                                        </form>
                                        <a href="{{ route('admin.invoice.invoice.export', ['type' => 'sales']) }}" class="btn btn-success">Export</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('globals.alert')
                            <div id="alert-container"></div>
                            <div id="table-content">
                                @include('admins.pages.sales_invoice.table', ['clients' => $clients])
                            </div>
                            <div id="pagination-links">
                                {{ $clients->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                </div> <!-- end col -->
            </div> <!-- end row -->

        </div> <!-- container-fluid -->
    </div>

@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        function triggerFileImport() {
            document.getElementById('file-input-import').click();
        }
    
        document.getElementById('file-input-import').addEventListener('change', function () {
            if (this.files.length > 0) {
                document.getElementById('import-form').submit();
            } else {
                alert("Vui lòng chọn file để import.");
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            // Thêm khách hàng mới
            $('#add-client-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('admin.client.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log(response); // Xem dữ liệu trả về
                        if (response.success) {
                            $('#addClientModal').modal('hide');
                            toastr.success(response.success, 'Thành công');
                            $('#table-content').html(response.html);
                            $('#pagination-links').html(response.pagination);
                        } else if (response.error) {
                            toastr.error(response.error, 'Lỗi');
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $('#' + field + '-error').text(errors[field][0]).show();
                        }
                    }
                });
            });

            $('#edit-client-form').on('submit', function(e) {
                e.preventDefault();
                let clientId = $('#edit-client-id').val();
                let url = "{{ route('admin.client.update', ':id') }}";
                url = url.replace(':id', clientId);

                $.ajax({
                    url: url,
                    type: "PUT",
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log(response); // Kiểm tra dữ liệu trả về
                        if (response.success) {
                            $('#editClientModal').modal('hide');
                            toastr.success(response.success, 'Thành công');
                            $('#table-content').html(response.html);
                            $('#pagination-links').html(response.pagination);
                        } else if (response.error) {
                            toastr.error(response.error, 'Lỗi');
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $('#edit-' + field + '-error').text(errors[field][0]);
                        }
                    }
                });
            });


            // Tìm kiếm khách hàng
            $('#search-btn').on('click', function() {
                let mst        = $('#mst').val();
                let tencongty  = $('#tencongty').val();
                let start_date = $('#start_date').val();
                let end_date   = $('#end_date').val();
              
                updateTableAndPagination(mst, tencongty, start_date, end_date );
            });

            $('#search-query').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    updateTableAndPagination();
                }
            });

            // Xóa khách hàng
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                if (confirm('Bạn có chắc chắn muốn xóa?')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function(response) {
                            toastr.success('Khách hàng đã được xóa thành công!', 'Thành công');
                            updateTableAndPagination();
                        },
                        error: function(xhr) {
                            toastr.error('Xóa khách hàng thất bại', 'Lỗi');
                        }
                    });
                }
            });

            // Hiển thị modal sửa thông tin khách hàng
            $(document).on('click', '.open-edit-modal', function() {
                let clientId = $(this).data('id');
                let url = "{{ route('admin.client.edit', ':id') }}";
                url = url.replace(':id', clientId);

                $.get(url, function(data) {
                    $('#edit-client-id').val(data.id);
                    $('#edit-name').val(data.name);
                    $('#edit-email').val(data.email);
                    $('#edit-phone').val(data.phone);
                    $('#edit-address').val(data.address);
                    $('#edit-field').val(data.field);
                    $('#edit-company_name').val(data.company_name);
                    $('#edit-tax_number').val(data.tax_number);
                    $('#editClientModal').modal('show');
                });
            });

            // Hiển thị modal thêm khách hàng
            $('#open-add-modal').on('click', function() {
                $('#add-client-form')[0].reset();
                $('.invalid-feedback').hide();
                $('#addClientModal').modal('show');
            });

            // Cập nhật bảng khách hàng và phân trang
            function updateTableAndPagination(mst, tencongty, start_date, end_date ) {
                let query = $('#search-query').val();
                $.ajax({
                    url: "{{ route('admin.invoice.indexSalesInvoice') }}",
                    type: 'GET',
                    data: {
                        mst: mst,
                        tencongty: tencongty,
                        start_date: start_date,
                        end_date:   end_date,
                    },
                    success: function(response) {
                        $('#table-content').html(response.html);
                        $('#pagination-links').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            // Xử lý sự kiện click vào liên kết phân trang
            $(document).on('click', '#pagination-links a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let query = $('#search-query').val();
                let newUrl = url + (url.includes('?') ? '&' : '?') + 'query=' + encodeURIComponent(query);
                $.ajax({
                    url: newUrl,
                    type: 'GET',
                    success: function(response) {
                        $('#table-content').html(response.html);
                        $('#pagination-links').html(response.pagination);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
            // Xử lý sự kiện khi nhấp vào tên khách hàng
            document.addEventListener('click', function(e) {
                if (e.target.matches('.client-name')) {
                    e.preventDefault();
                    const clientId = e.target.dataset.id;

                    fetch(`/admin/receipt/client/${clientId}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('client-name').textContent = data.name || '';
                            document.getElementById('client-phone').textContent = data.phone || '';
                            document.getElementById('client-company').textContent = data.company_name ||
                                'Chưa có công ty';
                            document.getElementById('client-field').textContent = data.field ||
                                'Chưa có ngành nghề';
                            document.getElementById('client-email').textContent = data.email ||
                                'Chưa có email';
                            document.getElementById('client-address').textContent = data.address ||
                                '';
                            document.getElementById('client-tax-number').textContent = data
                                .tax_number || 'Chưa có mã số thuế';

                            $('#clientModal').modal('show');
                        });
                }
            });
        });
    </script>
@endsection
