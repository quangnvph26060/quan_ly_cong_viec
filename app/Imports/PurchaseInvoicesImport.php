<?php

namespace App\Imports;

use App\Models\InvoiceModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PurchaseInvoicesImport implements ToCollection, WithHeadingRow
{
    // hoá đơn mua vào 

    public function headingRow(): int
    {
        return 6; // Dòng tiêu đề trong Excel
    }

    private function parseDate($date)
    {
        try {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                if (!isset($row['so_hoa_don'])) {
                    Log::warning("Bỏ qua dòng $index vì không có 'so_hoa_don'.", $row->toArray());
                    continue;
                }
                $existing = InvoiceModel::where('invoice_number', $row['so_hoa_don'])->where('status',0)->first();
                if ($existing) {
                    continue;
                }
                InvoiceModel::create([
                    'denominator_symbol'   => $row['ky_hieu_mau_so'] ?? null,
                    'invoice_symbol'       => $row['ky_hieu_hoa_don'] ?? null,
                    'invoice_number'       => $row['so_hoa_don'] ?? null,
                    'invoice_date'         => $this->parseDate($row['ngay_lap']),
                    'seller_tax_code'      => $row['mst_nguoi_banmst_nguoi_xuat_hang'] ?? null,
                    'seller_name'          => $row['ten_nguoi_banten_nguoi_xuat_hang'] ?? null,
                    'seller_address'       => $row['dia_chi_nguoi_ban'] ?? null,
                    'total_before_tax'     => $row['tong_tien_chua_thue'] ?? null,
                    'total_tax'            => $row['tong_tien_thue'] ?? null,
                    'total_discount'       => $row['tong_tien_chiet_khau_thuong_mai'] ?? null,
                    'total_fee'            => $row['tong_tien_phi'] ?? null,
                    'total_payment'        => $row['tong_tien_thanh_toan'] ?? null,
                    'currency_unit'        => $row['don_vi_tien_te'] ?? null,
                    'exchange_rate'        => $row['ty_gia'] ?? null,
                    'invoice_status'       => $row['trang_thai_hoa_don'] ?? null,
                    'invoice_check_result' => $row['ket_qua_kiem_tra_hoa_don'] ?? null,
                    'status'               => 0,
                ]);
            } catch (\Exception $e) {
                Log::error("Lỗi khi import dòng $index: " . $e->getMessage(), [
                    'row_data' => $row->toArray()
                ]);
            }
        }
    }
    
}
