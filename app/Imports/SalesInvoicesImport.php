<?php

namespace App\Imports;

use App\Models\InvoiceModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SalesInvoicesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new InvoiceModel([
            'seller_tax_code' => $row['mst_nguoi_ban'],
            'seller_name' => $row['ten_nguoi_ban'],
            'seller_address' => $row['dia_chi_nguoi_ban'],
            'invoice_symbol' => $row['ky_hieu_hoa_don'],
            'invoice_number' => $row['so_hoa_don'],
            'invoice_date' => $row['ngay_lap'],
            'total_before_tax' => $row['tong_chua_thue'],
            'total_tax' => $row['tong_thue'],
            'total_discount' => $row['chiet_khau'],
            'total_fee' => $row['tong_phi'],
            'total_payment' => $row['tong_thanh_toan'],
            'currency_unit' => $row['don_vi_tien_te'],
            'exchange_rate' => $row['ty_gia'],
            'invoice_status' => $row['trang_thai'],
            'invoice_check_result' => $row['kq_kiem_tra_hoa_don'],
            'status' => 1, // Hóa đơn bán
        ]);
    }
}
