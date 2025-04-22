@extends('admins.layouts.index')

@section('content')
    <style>
        .auto-expand {
            overflow-y: hidden;
            resize: none;
            height: 30px;
            min-height: 30px;
            width: 100%;
            line-height: 1.5;
            padding: 0.5rem;
            box-sizing: border-box;
        }

        #search {
            width: calc(100% - 2rem);
            padding: 0.5rem;
            box-sizing: border-box;
        }

        #results {
            width: 100% !important;
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .list-group-item {
            cursor: pointer;
        }

        .list-group-item:hover {
            background-color: #f1f1f1;
        }

        .no-results {
            display: block;
            padding: 0.5rem;
            color: #888;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Tạo báo giá</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin phiếu báo giá</h4>
                        </div>

                        <div class="card-body p-4">
                            <form action="{{ route('admin.bill.store') }}" method="POST" id="billForm">
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên hàng hóa, dịch vụ</th>
                                            <th>ĐVT</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody id="service-table-body">
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <textarea name="bill_details[0][service_name]" class="form-control auto-expand" placeholder="Nhập tên dịch vụ"
                                                    oninput="adjustHeight(this); validateForm()"></textarea>
                                            </td>
                                            <td>
                                                <input type="text" name="bill_details[0][unit]" class="form-control"
                                                    placeholder="Đơn vị tính" oninput="validateForm()">
                                            </td>
                                            <td>
                                                <input type="number" name="bill_details[0][amount]"
                                                    class="form-control quantity-input" placeholder="Số lượng"
                                                    oninput="calculateTotal(this); validateForm()">
                                            </td>
                                            <td>
                                                <input type="text" name="bill_details[0][price]"
                                                    class="form-control unit-price-input" placeholder="Đơn giá"
                                                    oninput="formatCurrency(this); calculateTotal(this); validateForm()">
                                            </td>
                                            <td>
                                                <input type="text" name="bill_details[0][total]"
                                                    class="form-control total-input" placeholder="Thành tiền" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Pháº§n nháº­p thuáº¿ -->
                                <div class="form-group mt-3">
                                    <label for="tax">Thuế (%):</label>
                                    <input type="number" name="tax" id="tax" class="form-control"
                                        placeholder="Nhập % thuế" oninput="calculateGrandTotal(); validateForm()">
                                </div>

                                <!-- Tá»•ng cá»™ng -->
                                <div class="form-group mt-3">
                                    <label for="total_money">Tổng cộng:</label>
                                    <input type="text" name="total_money" id="total_money" class="form-control" readonly>
                                </div>

                                <input type="hidden" name="client_id" id="selected-client-id" value="" />
                                <div class="mt-3">
                                    <button type="button" class="btn btn-secondary" onclick="addRow()">Thêm dịch
                                        vụ</button>
                                    <button type="submit" id="submitButton" class="btn btn-primary w-md" disabled>Xác
                                        nhận</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- end col -->

                <!-- Cá»™t thÃ´ng tin khÃ¡ch hÃ ng -->
                <div class="col-lg-4 col-md-4">
                    @include('admins.pages.bill.customer_info')
                </div> <!-- end col -->

            </div>
            <!-- end row -->

        </div> <!-- container-fluid -->
    </div>
    {{-- <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">Thêm kháhc hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-client-form">
                        @csrf
                        <!-- Form fields -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ tên<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div id="name-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                            <div id="email-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                            <div id="phone-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="field" class="form-label">Ngành nghề<span class="text-danger">*</span></label>
                            <input type="field" class="form-control" id="field" name="field">
                            <div id="phone-error" class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" required>
                            <div id="address-error" class="invalid-feedback"></div>
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
                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const resultsList = document.getElementById('results');
            const noResultsItem = document.querySelector('.no-results');

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

            searchInput.addEventListener('input', function() {
                const query = this.value;

                if (query.length > 0) {
                    fetch(`/admin/bill/customer-search?query=${query}`)
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
                    document.getElementById('selected-client-id').value = id;

                    resultsList.style.display = 'none';
                    validateForm();
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                    resultsList.style.display = 'none';
                }
            });
        });

        function addRow() {
            const tableBody = document.getElementById('service-table-body');
            const rowCount = tableBody.rows.length;
            const row = document.createElement('tr');

            row.innerHTML = `
        <td>${rowCount + 1}</td>
        <td><textarea type="text" name="bill_details[${rowCount}][service_name]" class="form-control auto-expand" placeholder="Nhập tên dịch vụ" oninput="adjustHeight(this); validateForm()"></textarea></td>
        <td><input type="text" name="bill_details[${rowCount}][unit]" class="form-control" placeholder="Đơn vị tính" oninput="validateForm()"></td>
        <td><input type="number" name="bill_details[${rowCount}][amount]" class="form-control quantity-input" placeholder="Số lượng" oninput="calculateTotal(this); validateForm()"></td>
        <td><input type="text" name="bill_details[${rowCount}][price]" class="form-control unit-price-input" placeholder="Đơn giá" oninput="formatCurrency(this); calculateTotal(this); validateForm()"></td>
        <td><input type="text" name="bill_details[${rowCount}][total]" class="form-control total-input" placeholder="Thành tiền" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Xóa</button></td>
    `;

            tableBody.appendChild(row);
        }

        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function calculateTotal(element) {
            const row = element.closest('tr');
            const quantity = row.querySelector('.quantity-input').value;
            let unitPrice = row.querySelector('.unit-price-input').value.replace(/,/g, '');

            const totalInput = row.querySelector('.total-input');
            const total = parseFloat(quantity) * parseFloat(unitPrice);

            if (!isNaN(total)) {
                totalInput.value = total.toLocaleString('en-US');
            } else {
                totalInput.value = '';
            }

            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            const totalInputs = document.querySelectorAll('.total-input');
            let grandTotal = 0;

            totalInputs.forEach(input => {
                let totalValue = parseFloat(input.value.replace(/,/g, ''));
                if (!isNaN(totalValue)) {
                    grandTotal += totalValue;
                }
            });

            const tax = document.getElementById('tax').value;
            if (!isNaN(tax) && tax !== '') {
                grandTotal += (grandTotal * parseFloat(tax)) / 100;
            }

            document.getElementById('total_money').value = grandTotal.toLocaleString('en-US');
        }
        document.getElementById('billForm').addEventListener('submit', function(event) {
            const clientId = document.getElementById('selected-client-id').value;
            if (!clientId) {
                event.preventDefault();
                alert('Vui lòng chọn khách hàng');
            }
        });

        function validateForm() {
            const form = document.getElementById('billForm');
            const submitButton = document.getElementById('submitButton');
            const inputs = form.querySelectorAll('input:not([readonly])');
            let allFilled = true;

            inputs.forEach(input => {
                if (input.value === '') {
                    allFilled = false;
                }
            });

            submitButton.disabled = !allFilled;
        }

        function removeRow(button) {
            const row = button.closest('tr');
            row.remove();

            // Cáº­p nháº­t láº¡i sá»‘ thá»© tá»±
            const rows = document.querySelectorAll('#service-table-body tr');
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1;
            });

            validateForm();
        }

        $('#add-client-form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.client.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        $('#addClientModal').modal('hide');
                        toastr.success(response.success, 'Thành công');
                    } else if (response.error) {
                        toastr.error(respons.error, 'Lỗi');
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

        //Hiá»ƒn thá»‹ modal thÃªm khÃ¡ch hÃ ng
        $('#open-add-modal').on('click', function() {
            $('#add-client-form')[0].reset();
            $('.invalid-feedback').hide();
            $('#addClientModal').modal('show');
        });

        function adjustHeight(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        document.querySelectorAll('.auto-expand').forEach(textarea => {
            adjustHeight(textarea);
            textarea.addEventListener('input', function() {
                adjustHeight(this);
            });
        });
    </script>
@endsection
