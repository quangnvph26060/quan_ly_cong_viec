<?php

namespace App\Exports;

use App\Models\InvoiceModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesInvoicesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return InvoiceModel::where('status', 1)->get(); // 1 là trạng thái Hóa đơn bán
    }

    public function headings(): array
    {
        return [
            'MST người bán',
            'Tên người bán',
            'Địa chỉ người bán',
            'Ký hiệu hóa đơn',
            'Số hóa đơn',
            'Ngày lập',
            'Tổng chưa thuế',
            'Tổng thuế',
            'Chiết khấu',
            'Tổng phí',
            'Tổng thanh toán',
            'Đơn vị tiền tệ',
            'Tỷ giá',
            'Trạng thái',
            'KQ kiểm tra hóa đơn',
        ];
    }
}
