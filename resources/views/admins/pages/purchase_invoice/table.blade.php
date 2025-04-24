<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 d-flex" style="gap:53px">
        <div class="d-flex">
            <p class="fw-bold">Tổng tiền chưa thuế: </p>
            <p>{{ number_format($sumBeforeTax) }}</p>
        </div>
        <div class="d-flex">
            <p class="fw-bold">Tổng tiền thuế: </p>
            <p>{{ number_format($sumTax) }}</p>
        </div>
        <div class="d-flex">
            <p class="fw-bold">Tổng tiền thành toán :</p>
            <p>{{ number_format($sumPayment) }}</p>
        </div>
    </div>
</div>
<div class="table-rep-plugin">
    <div class="table-responsive mb-0" data-pattern="priority-columns">
        <table id="tech-companies-1" class="table table-striped">
            <thead>
                <tr>
                    <th>STT</th>
                    <th data-priority="2">Số hoá đơn</th>
                    <th data-priority="3">Ngày lập</th>
                    <th data-priority="3">Mã số thuế</th>
                    <th data-priority="6">Tên công ty</th>
                    <th>Tổng tiền chưa thuế </th>
                    <th>Tổng tiền thuế </th>
                    <th>Tổng tiền thành toán</th>
                    <th style="text-align: center" data-priority="7">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $start = ($clients->currentPage() - 1) * $clients->perPage() + 1;
                @endphp
                @foreach ($clients as $key => $client)
                    <tr>
                        <td>{{ $key + $start }}</td>
                        <td style="text-align: left;">
                            <a href="#" class="invoice_number">{{ $client->invoice_number }}</a>
                        </td>
                        <td> {{ \Carbon\Carbon::parse($client->invoice_date)->format('d/m/Y') }}</td>
                        <td>{{ $client->seller_tax_code }}</td>
                        <td>{{ $client->seller_name }} </td>
                        <td>{{ number_format($client->total_before_tax) }}</td>
                        <td>{{ number_format($client->total_tax) }}</td>
                        <td>{{ number_format($client->total_payment) }}</td>

                        <td style="text-align: center">
                            <a href="javascript:void(0)" data-id="{{ $client->id }}"
                                class="btn btn-warning open-edit-modal">Sửa</a>
                            <a href="#" data-url="{{ route('admin.client.delete', ['id' => $client->id]) }}"
                                class="btn btn-danger btn-delete">Xóa</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
