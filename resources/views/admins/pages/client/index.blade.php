@extends('admins.layouts.index')

@section('content')
    <!-- Thêm CSS để căn giữa modal -->
    <style>
        .image-container {
            position: relative;
            display: inline-block;
        }


        .image-container .btn-img-delete {
            position: absolute;
            top: 5px;
            right: 5px;
            background: none;
            /* Đặt góc trên bên phải */
            color: white;
            border: none;
            font-size: 18px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            cursor: pointer;
        }

        .image-container .btn-img-delete:hover {
            background-color: darkred;
        }

        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        /* Đảm bảo các phần tử khác không bị ảnh hưởng */
        .modal-body p {
            margin-bottom: 10px;
        }

        /* CSS đặc biệt cho Mặt trước và Mặt sau CCCD */
        .id-images {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .id-images span {
            margin-left: 10px;
        }

        .id-images strong {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
    </style>

    <div class="page-content" id="content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Danh sách khách hàng</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="">Tìm kiếm</label>
                                        <input value="{{ request('query') }}" autocomplete="off" name="query"
                                            placeholder="Nhập từ khóa tìm kiếm" type="text" class="form-control"
                                            id="search-query">
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
                                        <button type="button" class="btn btn-success" id="open-add-modal">
                                            <i class="fas fa-plus"></i> Thêm mới
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('globals.alert')
                            <div id="alert-container"></div>
                            <div id="table-content">
                                @include('admins.pages.client.table', ['clients' => $clients])
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

    <!-- Modal HTML -->
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">Thêm khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-client-form" enctype="multipart/form-data">
                        @csrf
                        <!-- Form fields -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div id="name-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                            <div id="email-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                            <div id="phone-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="front_id_image" class="form-label">Ảnh mặt trước <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="front_id_image" name="front_id_image">
                        </div>
                        <div class="mb-3">
                            <label for="back_id_image" class="form-label">Ảnh mặt sau <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="back_id_image" name="back_id_image">
                        </div>
                        <div class="mb-3">
                            <label for="other_images" class="form-label">Ảnh khác</label>
                            <input type="file" class="form-control" id="other_images" name="other_images[]" multiple>
                        </div>
                        <div class="row mt-3" id="other_images_preview"></div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" required>
                            <div id="address-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="field" class="form-label">Ngành nghề</label>
                            <input type="text" class="form-control" id="field" name="field">
                            <div id="field-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Tên công ty </label>
                            <input type="text" class="form-control" id="company_name" name="company_name">
                            <div id="company_name-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="tax_number" class="form-label">Mã số thuế </label>
                            <input type="text" class="form-control" id="tax_number" name="tax_number">
                            <div id="tax_number-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="note" name="note"></textarea>
                            <div id="note-error" class="invalid-feedback"></div>
                        </div>

                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Client Modal -->
    <div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClientModalLabel">Sửa thông tin khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-client-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-client-id" name="id">
                        <!-- Form fields (similar to add form but with IDs prefixed with 'edit-') -->
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                            <div id="edit-name-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email">
                            <div id="edit-email-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-phone" class="form-label">Số điện thoại <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-phone" name="phone" required>
                            <div id="edit-phone-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-address" class="form-label">Địa chỉ <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-address" name="address" required>
                            <div id="edit-address-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-field" class="form-label">Ngành nghề</label>
                            <input type="text" class="form-control" id="edit-field" name="field">
                            <div id="edit-field-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-company_name" class="form-label">Tên công ty</label>
                            <input type="text" class="form-control" id="edit-company_name" name="company_name">
                            <div id="edit-company_name-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-tax_number" class="form-label">Mã số thuế</label>
                            <input type="text" class="form-control" id="edit-tax_number" name="tax_number">
                            <div id="edit-tax_number-error" class="invalid-feedback"></div>
                        </div>
                        {{-- <div class="mb-3">
                            <label for="front_id_image" class="form-label">Ảnh mặt trước CCCD</label>
                            <input type="file" class="form-control" id="edit-front_id_image" name="front_id_image">
                        </div>

                        <div class="mb-3">
                            <label for="back_id_image" class="form-label">Ảnh mặt sau CCCD</label>
                            <input type="file" class="form-control" id="edit-back_id_image" name="back_id_image">
                        </div> --}}
                        <div class="mb-3">
                            <label for="edit-note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="edit-note" name="note"></textarea>
                            <div id="edit-note-error" class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        <a id="edit-image-btn" href="#" class="btn btn-secondary">Sửa ảnh</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="clientModal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientModalLabel">Thông tin khách hàng</h5>
                </div>
                <div class="modal-body">
                    <p hidden><span id="client-id"></span></p>
                    <p><strong>Tên:</strong> <span id="client-name"></span></p>
                    <p><strong>Số điện thoại:</strong> <span id="client-phone"></span></p>
                    <p><strong>Tên công ty:</strong> <span id="client-company"></span></p>
                    <p><strong>Ngành nghề:</strong> <span id="client-field"></span></p>
                    <p><strong>Email:</strong> <span id="client-email"></span></p>
                    <p><strong>Địa chỉ:</strong> <span id="client-address"></span></p>
                    <p><strong>Mã số thuế:</strong> <span id="client-tax-number"></span></p>

                    <!-- Thêm phần ảnh mặt trước và mặt sau -->
                    <p>
                        <strong>Mặt trước CCCD:</strong><br>
                    <div class="image-container">
                        <img id="client-front-id-image" src="" alt="Mặt trước CCCD" width="150"
                            height="150" />
                        <button type="button" class="btn-img-delete" id="delete-front-image" data-type="front"
                            aria-label="Close" hidden>&times;</button>
                    </div>
                    </p>
                    <p>
                        <strong>Mặt sau CCCD:</strong><br>
                    <div class="image-container">
                        <img id="client-back-id-image" src="" alt="Mặt sau CCCD" width="150"
                            height="150" />
                        <button type="button" class="btn-img-delete " id="delete-back-image" data-type="back"
                            aria-label="Close" hidden>&times;</button>
                    </div>
                    </p>
                    <p>
                        <strong>Ảnh khác:</strong><br>
                    <div class="image-container" id="client-other-images-container">
                        {{-- <img id="client-other-images" src="" alt="Ảnh khác" width="150" height="150" />
                        <button type="button" class="btn-img-delete " id="delete-other-images" data-type="others"
                            aria-label="Close" hidden>&times;</button> --}}
                    </div>
                    </p>
                    <p><strong>Ghi chú:</strong> <span id="client-note"></span></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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

                // Khởi tạo đối tượng FormData
                var formData = new FormData(
                    this); // 'this' là form hiện tại, FormData sẽ tự động lấy tất cả các trường trong form

                $.ajax({
                    url: "{{ route('admin.client.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false, // Không xử lý dữ liệu (vì đã có file)
                    contentType: false, // Không set content-type, vì FormData sẽ tự động set
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
                updateTableAndPagination();
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
                    $('#edit-note').val(data.note);

                    let editImageUrl = "{{ route('admin.client.editImage', ['id' => ':id']) }}";
                    editImageUrl = editImageUrl.replace(':id', clientId);
                    $('#edit-image-btn').attr('href', editImageUrl);

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
            function updateTableAndPagination() {
                let query = $('#search-query').val();
                $.ajax({
                    url: "{{ route('admin.client.search') }}",
                    type: 'GET',
                    data: {
                        query: query
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

            function updateOtherImagesList(clientId) {
                $.ajax({
                    url: `/admin/receipt/client/${clientId}`,
                    method: 'GET',
                    success: function(data) {
                        const other_images = JSON.parse(data
                        .other_images); // Kiểm tra xem dữ liệu ảnh có đúng định dạng không
                        const otherImagesContainer = $('#client-other-images-container');

                        // Xóa tất cả ảnh cũ trong container trước khi thêm ảnh mới
                        otherImagesContainer.empty();

                        // Kiểm tra nếu không còn ảnh "Khác"
                        if (other_images.length === 0) {
                            // Nếu không còn ảnh, hiển thị thông báo
                            otherImagesContainer.append('<div>Không có ảnh khác</div>');
                        } else {
                            // Cập nhật lại ảnh "Khác"
                            other_images.forEach(function(other_image) {
                                const imageUrl = other_image ? `/storage/${other_image}` :
                                    '/images/no-image.jpg';
                                const imageWrapper = $('<div>', {
                                    style: 'position: relative; display: inline-block; margin: 5px;',
                                });

                                // Tạo phần tử <img>
                                const imageElement = $('<img>', {
                                    src: imageUrl,
                                    alt: 'Ảnh khác',
                                    class: 'img-fluid image-thumbnail',
                                    style: 'width: 100px; height: 100px;',
                                });

                                // Tạo nút xóa
                                const deleteBtn = $('<button>', {
                                    text: 'x',
                                    class: 'btn-img-delete',
                                    style: 'position: absolute; top: 0; right: 0;',
                                }).data('type', 'other');

                                if (!other_image) {
                                    deleteBtn.hide();
                                }

                                imageWrapper.append(imageElement).append(deleteBtn);
                                otherImagesContainer.append(imageWrapper);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Có lỗi khi lấy danh sách ảnh:", error);
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
                            document.getElementById('client-id').textContent = data.id || '';
                            document.getElementById('client-name').textContent = data.name || '';
                            document.getElementById('client-phone').textContent = data.phone || '';
                            document.getElementById('client-company').textContent = data.company_name ||
                                '';
                            document.getElementById('client-field').textContent = data.field || '';
                            document.getElementById('client-email').textContent = data.email || '';
                            document.getElementById('client-address').textContent = data.address || '';
                            document.getElementById('client-tax-number').textContent = data
                                .tax_number || '';
                            document.getElementById('client-note').innerHTML = '<br>' + (data.note ||
                                '');

                            // Đường dẫn ảnh mặc định
                            const defaultImageUrl =
                                '/images/no-image.jpg'; // URL ảnh mặc định trong thư mục public
                            var other_images = JSON.parse(data.other_images);

                            // Lấy container chứa các ảnh khác
                            const otherImagesContainer = document.getElementById(
                                'client-other-images-container');

                            // Xóa tất cả ảnh cũ trong container (nếu có) trước khi thêm ảnh mới
                            otherImagesContainer.innerHTML = '';

                            other_images.forEach(other_image => {
                                // Tạo container riêng cho từng ảnh và nút xóa
                                const imageWrapper = document.createElement('div');
                                imageWrapper.style.position = 'relative';
                                imageWrapper.style.display = 'inline-block';
                                imageWrapper.style.margin = '5px'

                                //Tạo URL cho ảnh
                                const otherImageUrl = other_image ? `/storage/${other_image}` :
                                    defaultImageUrl;

                                //Tạo phần tử <img> mới cho ảnh
                                const otherImageElement = document.createElement('img');
                                otherImageElement.setAttribute('data-imagename', other_image);
                                otherImageElement.src = otherImageUrl;
                                otherImageElement.alt = 'Ảnh khác';
                                otherImageElement.classList.add('img-fluid', 'image-thumbnail');
                                otherImageElement.style.width = '100px';
                                otherImageElement.style.height = '100px';

                                //Thêm ảnh vào container
                                imageWrapper.appendChild(otherImageElement);

                                //Tạo nút xóa cho ảnh
                                const deleteOtherImageBtn = document.createElement('button');
                                deleteOtherImageBtn.textContent = 'x';
                                deleteOtherImageBtn.setAttribute('data-type', 'other');
                                deleteOtherImageBtn.classList.add('btn-img-delete');

                                //Ấn nút nếu không có ảnh
                                deleteOtherImageBtn.hidden = !other_image;

                                //Xử lý sự kiện xóa ảnh


                                //Thêm nút xóa vào container ảnh
                                imageWrapper.appendChild(deleteOtherImageBtn);

                                //Thêm container vào danh sách các ảnh
                                otherImagesContainer.appendChild(imageWrapper);
                            });

                            // Kiểm tra và lấy đường dẫn ảnh, nếu không có thì dùng ảnh mặc định
                            const frontIdImageUrl = data.front_id_image ?
                                `/storage/${data.front_id_image}` : defaultImageUrl;
                            const backIdImageUrl = data.back_id_image ?
                                `/storage/${data.back_id_image}` : defaultImageUrl;

                            // Cập nhật đường dẫn ảnh vào thẻ img
                            document.getElementById('client-front-id-image').src = frontIdImageUrl;
                            document.getElementById('client-back-id-image').src = backIdImageUrl;

                            // Kiểm tra và hiển thị/ẩn nút xóa dựa trên sự tồn tại của ảnh
                            const deleteFrontImageBtn = document.getElementById('delete-front-image');
                            const deleteBackImageBtn = document.getElementById('delete-back-image');

                            if (data.front_id_image && data.front_id_image !== '') {
                                deleteFrontImageBtn.hidden = false;
                            } else {
                                deleteFrontImageBtn.hidden = true;
                            }

                            if (data.back_id_image && data.back_id_image !== '') {
                                deleteBackImageBtn.hidden = false;
                            } else {
                                deleteBackImageBtn.hidden = true;
                            }

                            $('#clientModal').modal('show');
                        });
                }
            });

            $(document).on('click', '.btn-img-delete', function() {
                // Lấy ID của khách hàng từ thuộc tính data-id hoặc định nghĩa riêng
                let clientId = $('#client-id').text().trim();

                // Xác định loại ảnh (mặt trước hoặc mặt sau) dựa vào thuộc tính data-type
                let imageName = $(this).siblings('img').data('imagename');
                let imageType = $(this).data('type');
                const deleteFrontImageBtn = document.getElementById('delete-front-image');
                const deleteBackImageBtn = document.getElementById('delete-back-image');
                // Xác định URL gọi API
                let deleteUrl = "{{ route('admin.client.deleteImage', ':id') }}".replace(':id', clientId);

                // Hiển thị xác nhận bằng swal
                Swal.fire({
                    title: 'Bạn có muốn xóa ảnh này?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Nếu người dùng xác nhận, thực hiện AJAX DELETE
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}",
                                type: imageType, // Truyền loại ảnh nếu cần xử lý riêng
                                imageName: imageName,
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Xóa thành công!',
                                    response.message,
                                    'success'
                                );

                                // Xóa ảnh khỏi giao diện
                                if (imageType === 'front') {
                                    $('#client-front-id-image').attr('src',
                                        '/images/no-image.jpg');
                                    deleteFrontImageBtn.hidden = true;
                                } else if (imageType === 'back') {
                                    $('#client-back-id-image').attr('src',
                                        '/images/no-image.jpg');
                                    deleteBackImageBtn.hidden = true;
                                } else if (imageType === 'other') {
                                    // Xóa ảnh khỏi container "Ảnh khác"
                                    $(this).closest('div')
                                        .remove(); // Xóa phần tử ảnh "Khác"
                                }

                                // Cập nhật lại danh sách ảnh "Khác"
                                updateOtherImagesList(clientId);
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Lỗi!',
                                    xhr.responseJSON.message ||
                                    'Đã xảy ra lỗi khi xóa ảnh',
                                    'error'
                                );
                            },
                        });
                    }
                });
            });



            //Xem trước ảnh mục "Ảnh khác"
            function previewOtherImages() {
                const previewContainer = document.getElementById('other_images_preview');
                previewContainer.innerHTML = '';
                const files = document.getElementById('other_images').files;

                if (files.length > 0) {
                    for (let i = 0; i < filed.length; i++) {
                        const file = file[i];
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = 'Image Preview';
                            img.style.width = '100px';
                            img.style.height = '100px';
                            img.style.objectFit = 'cover';
                            previewContainer.appendChild(img);
                        };

                        reader.readAsDataURL(file);
                    }
                }
            }

        });
    </script>
@endsection
