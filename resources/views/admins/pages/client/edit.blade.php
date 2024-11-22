@extends('admins.layouts.index')

@section('content')
    <style>
        .lb-close {
            display: none !important;
        }

        .image-thumbnail {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Sửa ảnh của khách hàng: {{ $client->name }}</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <form id="update-client-image-form" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="front_id_image" class="form-label">Ảnh mặt trước CCCD</label>
                                            <input type="file" class="form-control" id="edit-front_id_image"
                                                name="front_id_image" accept="image/*">
                                            <a href="{{ $client->front_id_image ? Storage::url($client->front_id_image) : asset('images/no-image.jpg') }}"
                                                data-lightbox="client-images">
                                                <img id="preview-front"
                                                    src="{{ $client->front_id_image ? Storage::url($client->front_id_image) : asset('images/no-image.jpg') }}"
                                                    alt="Ảnh mặt trước CCCD" class="mt-2 image-thumbnail">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="back_id_image" class="form-label">Ảnh mặt sau CCCD</label>
                                            <input type="file" class="form-control" id="edit-back_id_image"
                                                name="back_id_image" accept="image/*">
                                            <a href="{{ $client->back_id_image ? Storage::url($client->back_id_image) : asset('images/no-image.jpg') }}"
                                                data-lightbox="client-images">
                                                <img id="preview-back"
                                                    src="{{ $client->back_id_image ? Storage::url($client->back_id_image) : asset('images/no-image.jpg') }}"
                                                    alt="Ảnh mặt sau CCCD" class="mt-2 image-thumbnail">
                                            </a>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mt-5">
                                        <div class="mb-3">
                                            <label for="other_images" class="form-label">Ảnh khác</label>
                                            <input type="file" class="form-control mb-3" id="other_images"
                                                name="other_images[]" multiple>
                                            <div class="row" id="preview-other-images">
                                                @if ($otherImages)
                                                    @foreach ($otherImages as $image)
                                                        <div class="col-4 mb-3">
                                                            <a href="{{ Storage::url($image) }}"
                                                                data-lightbox="client-images" data-title="Ảnh khác">
                                                                <img class="img-fluid image-thumbnail"
                                                                    src="{{ Storage::url($image) }}" alt="Ảnh khác">
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="col-4 mb-3">
                                                        <a href="{{ asset('images/no-image.jpg') }}"
                                                            data-lightbox="client-images" data-title="Ảnh khác">
                                                            <img class="img-fluid image-thumbnail"
                                                                src="{{ asset('images/no-image.jpg') }}" alt="Ảnh khác">
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary w-md">Xác nhận</button>
                                        <a href="{{ route('admin.client.index') }}" class="btn btn-danger ms-2">Quay về</a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox-plus-jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cập nhật preview ảnh
            function previewImage(input, previewId) {
                const file = input.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.querySelector(previewId);
                        img.src = e.target.result;
                        img.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.querySelector(previewId).style.display = 'none';
                }
            }

            document.getElementById('edit-front_id_image').addEventListener('change', function() {
                previewImage(this, '#preview-front');
            });

            document.getElementById('edit-back_id_image').addEventListener('change', function() {
                previewImage(this, '#preview-back');
            });

            // Xử lý preview ảnh "other_images"
            document.getElementById('other_images').addEventListener('change', function() {
                const files = this.files;
                const previewContainer = document.getElementById('preview-other-images');
                previewContainer.innerHTML = ''; // Xóa các ảnh đã preview

                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-fluid', 'image-thumbnail');
                        const colDiv = document.createElement('div');
                        colDiv.classList.add('col-4', 'mb-3');
                        colDiv.appendChild(img);
                        previewContainer.appendChild(colDiv);
                    };
                    reader.readAsDataURL(file);
                });
            });

            // Xử lý AJAX form submit
            $('#update-client-image-form').on('submit', function(e) {
                e.preventDefault(); // Ngăn chặn submit mặc định

                let formData = new FormData(this);
                $.ajax({
                    url: "{{ route('admin.client.updateImage', ['id' => $client->id]) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: response.success,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: response.error || 'Có lỗi xảy ra.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Không thể cập nhật hình ảnh. Vui lòng thử lại.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Tạo nút tải về
            // var $downloadButton = $('<button>').text('Tải về')
            //     .addClass('btn btn-primary')
            //     .css({
            //         'position': 'absolute',
            //         'top': '10px',
            //         'right': '10px', // Khoảng cách từ góc trên bên phải
            //         'z-index': '9999',
            //         'background-color': '#28a745', // Màu nền xanh lá
            //         'border': 'none',
            //         'color': '#fff', // Màu chữ trắng
            //         'padding': '12px 18px', // Padding đẹp hơn
            //         'font-size': '16px', // Tăng kích thước chữ một chút
            //         'border-radius': '8px', // Bo góc mượt hơn
            //         'box-shadow': '0 4px 6px rgba(0, 0, 0, 0.1)', // Tạo hiệu ứng bóng đổ
            //         'cursor': 'pointer', // Khi hover vào, con trỏ sẽ thành hình tay
            //         'transition': 'background-color 0.3s ease, transform 0.2s ease',
            //     })
            //     .hide(); // Ẩn nút khi chưa mở lightbox

            // // Hàm để hiển thị nút tải về khi mở ảnh trong lightbox
            // function showDownloadButton() {
            //     setTimeout(function() {
            //         // Đảm bảo lightbox đã mở
            //         var $lightboxContainer = $('.lb-outerContainer');
            //         if ($lightboxContainer.length) {
            //             // Thêm nút tải về vào lightbox nếu chưa có
            //             if (!$downloadButton.is(':visible')) {
            //                 $lightboxContainer.append($downloadButton.show());
            //             }
            //         }
            //     }, 500); // Delay một chút để chắc chắn lightbox đã mở
            // }

            // Khi click vào ảnh mặt trước
            $('#preview-front').on('click', function() {
                showDownloadButton(); // Hiển thị nút tải về khi mở ảnh mặt trước
            });

            // Khi click vào ảnh mặt sau
            $('#preview-back').on('click', function() {
                showDownloadButton(); // Hiển thị nút tải về khi mở ảnh mặt sau
            });

            // Khi click vào ảnh khác trong lightbox
            $('[data-lightbox="client-images"]').on('click', function() {
                showDownloadButton(); // Hiển thị nút tải về khi mở ảnh khác
            });

            // Khi nhấn nút tải về
            $downloadButton.on('click', function() {
                var $currentImage = $('.lb-image'); // Lấy ảnh hiện tại trong lightbox
                if ($currentImage.length) {
                    var imageUrl = $currentImage.attr('src'); // Lấy URL ảnh
                    var link = document.createElement('a');
                    link.href = imageUrl;
                    link.download = imageUrl.split('/').pop(); // Tên file sẽ là tên cuối cùng của URL
                    link.click(); // Tự động tải ảnh về
                }
            });
        });
    </script>
@endsection
