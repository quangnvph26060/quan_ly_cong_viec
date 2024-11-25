@extends('admins.layouts.index')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Sửa phiếu thu số {{ $receipt->id }}</h4>
                    </div>
                </div>
            </div>
            <!-- End page title -->

            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin phiếu thu</h4>
                        </div>

                        <div class="card-body p-4">
                            <form id="receipt-form" action="{{ route('admin.receipt.update', ['id' => $receipt->id]) }}"
                                method="POST">
                                @method('PUT')
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nội dung thu</th>
                                            <th class="text-right">Số tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input value="{{ $receipt->note }}" type="text" id="note"
                                                    name="note" class="form-control" placeholder="Nhập nội dung thu">
                                            </td>
                                            <td>
                                                <input value="{{ number_format($receipt->amount) }}" type="text"
                                                    id="amount" name="amount" oninput="formatReceiptCurrency(this);"
                                                    class="form-control text-right" placeholder="Nhập số tiền">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="hidden" value="{{ $receipt->client_id }}" name="client_id"
                                    id="selected-client-id" value="" />
                                <div class="mt-3">
                                    <button type="submit" id="confirm-button" class="btn btn-primary w-md">Xác
                                        nhận</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- end col -->

                <!-- Include the customer search and info section -->
                <div class="col-lg-4 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin khách hàng</h4>
                        </div>
                        <div class="card-body p-4">
                            <!-- Tìm kiếm khách hàng -->
                            <div class="form-group position-relative">
                                <label for="search">Tìm kiếm khách hàng:</label>
                                <div class="search-container">
                                    <input type="text" id="search" class="form-control"
                                        placeholder="Nhập tên, số điện thoại, hoặc email khách hàng...">
                                    <span id="open-add-modal" class="search-icon">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Kết quả tìm kiếm -->
                            <ul id="results" class="list-group mt-2" style="display: none;">
                                <li class="no-results" style="display: none;">Không có kết quả phù hợp</li>
                            </ul>

                            <!-- Thông tin khách hàng -->
                            <div class="form-group mt-3">
                                <label class="customer-label">Tên khách hàng:</label>
                                <div id="customer-info-name">{{ $receipt->client->name ?? '' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="customer-label">Số điện thoại:</label>
                                <div id="customer-info-phone">{{ $receipt->client->phone ?? '' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="customer-label">Địa chỉ:</label>
                                <div id="customer-info-address">{{ $receipt->client->address ?? '' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="customer-label">Email:</label>
                                <div id="customer-info-email">{{ $receipt->client->email ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addClientModalLabel">Thêm khách hàng</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="add-client-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Họ tên <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                required>
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
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                required>
                                            <div id="phone-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">Tên công ty</label>
                                            <input type="text" class="form-control" id="company_name"
                                                name="company_name">
                                            <div id="company_name-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="field" class="form-label">Ngành nghề</label>
                                            <input type="text" class="form-control" id="field" name="field">
                                            <div id="field-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tax_number" class="form-label">Số thuế</label>
                                            <input type="text" class="form-control" id="tax_number"
                                                name="tax_number">
                                            <div id="tax_number-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Địa chỉ</label>
                                            <input type="text" class="form-control" id="address" name="address">
                                            <div id="address-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="note" class="form-label">Ghi chú</label>
                                            <textarea class="form-control" id="note" name="note"></textarea>
                                            <div id="note-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="modal-footer">
                                            {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button> --}}
                                            <button type="submit" class="btn btn-primary">Thêm</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .form-group {
                            position: relative;
                        }

                        .search-container {
                            position: relative;
                            display: flex;
                            align-items: center;
                        }

                        #search {
                            padding-right: 40px;
                            /* Đảm bảo có đủ không gian cho dấu cộng */
                            flex: 1;
                        }

                        .search-icon {
                            position: absolute;
                            right: 10px;
                            /* Khoảng cách từ bên phải của ô tìm kiếm */
                            top: 50%;
                            transform: translateY(-50%);
                            font-size: 18px;
                            /* Kích thước của dấu cộng */
                            color: #007bff;
                            /* Màu của dấu cộng */
                            cursor: pointer;
                            z-index: 1;
                            /* Đảm bảo dấu cộng nằm trên ô tìm kiếm */
                        }

                        .search-container .form-control {
                            padding-right: 40px;
                            /* Căn chỉnh khoảng cách cho dấu cộng */
                        }
                    </style>

                </div> <!-- end col -->

            </div>
            <!-- End row -->

        </div> <!-- container-fluid -->
    </div>
@endsection

@section('styles')
    <style>
        .text-right {
            text-align: right;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const resultsList = document.getElementById('results');
            const noResultsItem = document.querySelector('.no-results');
            const confirmButton = document.getElementById('confirm-button');
            const noteInput = document.getElementById('note');
            const amountInput = document.getElementById('amount');
            const clientIdInput = document.getElementById('selected-client-id');

            function formatReceiptCurrency(input) {
                // Xóa mọi ký tự không phải số
                let value = input.value.replace(/[^0-9]/g, '');

                // Chuyển đổi số thành chuỗi định dạng tiền tệ có dấu phẩy
                value = new Intl.NumberFormat('vi-VN').format(value);

                // Gán lại giá trị đã định dạng cho ô nhập liệu
                input.value = value;
            }

            document.getElementById('amount').addEventListener('input', function() {
                formatReceiptCurrency(this);
                // checkFormValidity();
            });

            // Hàm kiểm tra tất cả các trường và bật/tắt nút xác nhận
            // function checkFormValidity() {
            //     const isFormValid = noteInput.value.trim() !== '' &&
            //         amountInput.value.trim() !== '' &&
            //         clientIdInput.value !== '';

            //     confirmButton.disabled = !isFormValid; // Nếu tất cả các trường đều được điền, bật nút xác nhận
            // }

            // Gọi hàm kiểm tra khi người dùng nhập nội dung hoặc số tiền
            // noteInput.addEventListener('input', checkFormValidity);
            // amountInput.addEventListener('input', checkFormValidity);

            // Hàm hiển thị danh sách khách hàng
            function displayCustomerList(customers) {
                resultsList.innerHTML = '';
                if (customers.length > 0) {
                    customers.forEach(customer => {
                        const customerItem = document.createElement('li');
                        customerItem.classList.add('list-group-item');
                        customerItem.setAttribute('data-id', customer.id);
                        customerItem.setAttribute('data-fullname', customer.name);
                        customerItem.setAttribute('data-email', customer.email);
                        customerItem.setAttribute('data-phone', customer.phone);
                        customerItem.setAttribute('data-address', customer.address);
                        customerItem.textContent =
                            `${customer.name} (${customer.phone}) - ${customer.company_name}`;
                        resultsList.appendChild(customerItem);
                    });
                    noResultsItem.style.display = 'none';
                    resultsList.style.display = 'block';
                } else {
                    noResultsItem.style.display = 'block';
                    resultsList.style.display = 'block';
                }
            }

            // Lắng nghe sự kiện thay đổi đầu vào tìm kiếm
            searchInput.addEventListener('input', function() {
                const query = this.value;

                if (query.length > 0) { // Chỉ tìm kiếm khi có nhiều hơn 0 ký tự
                    fetch(`/admin/receipt/customer-search?query=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                displayCustomerList(data.customers);
                            } else {
                                resultsList.innerHTML = '';
                                noResultsItem.style.display = 'block';
                            }
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    resultsList.innerHTML = '';
                    noResultsItem.style.display = 'none';
                    resultsList.style.display = 'none';
                }
            });

            // Lắng nghe sự kiện khi chọn khách hàng từ danh sách
            resultsList.addEventListener('click', function(e) {
                if (e.target && e.target.matches('.list-group-item')) {
                    const fullname = e.target.getAttribute('data-fullname');
                    const email = e.target.getAttribute('data-email');
                    const phone = e.target.getAttribute('data-phone');
                    const address = e.target.getAttribute('data-address');
                    const id = e.target.getAttribute('data-id');

                    document.getElementById('customer-info-name').textContent = fullname || 'N/A';
                    document.getElementById('customer-info-phone').textContent = phone || 'N/A';
                    document.getElementById('customer-info-address').textContent = address || 'N/A';
                    document.getElementById('customer-info-email').textContent = email || 'N/A';
                    // Cập nhật giá trị client_id đã chọn
                    clientIdInput.value = id;

                    // Kiểm tra lại tính hợp lệ của form sau khi chọn khách hàng
                    // checkFormValidity();

                    resultsList.style.display = 'none'; // Ẩn danh sách kết quả sau khi chọn
                }
            });

            // Ẩn danh sách khi nhấn chuột ra ngoài
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                    resultsList.style.display = 'none';
                }
            });

            // Gọi kiểm tra form ngay từ đầu
            // checkFormValidity();

            // Gọi formatCurrency ngay từ đầu nếu đã có giá trị trong input
            if (amountInput.value) {
                formatCurrency(amountInput);
            }
        });

        $('#add-client-form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.client.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addClientModal').modal('hide');
                        toastr.success(response.success, 'Thành công');
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

        // Hiển thị modal thêm khách hàng
        $('#open-add-modal').on('click', function() {
            $('#add-client-form')[0].reset();
            $('.invalid-feedback').hide();
            $('#addClientModal').modal('show');
        });
    </script>
@endsection
