<?php

namespace App\Exports;

use App\Models\InvoiceModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalesInvoicesExport implements FromCollection, WithMapping, WithStyles, ShouldAutoSize, WithStartRow, WithEvents
{
    private $index = 0;
    public function collection()
    {
        return InvoiceModel::where('status', 1)->get();
    }
    public function startRow(): int
    {
        return 7;
    }
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'DANH SÁCH HOÁ ĐƠN');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->fromArray([
                    [
                        'STT',
                        'Ký hiệu mẫu số',
                        'Ký hiệu hóa đơn',
                        'Số hóa đơn',
                        'Ngày lập',
                        'MST người mua/MST người nhập hàng',
                        'Tên người mua/Tên người nhập hàng',
                        'Địa chỉ người mua',
                        'Tổng tiền chưa thuế',
                        'Tổng tiền thuế',
                        'Tổng tiền chiết khấu thương mại',
                        'Tổng tiền phí',
                        'Tổng tiền thanh toán',
                        'Đơn vị tiền tệ',
                        'Tỷ giá',
                        'Trạng thái hóa đơn',
                        'Kết quả kiểm tra hóa đơn',
                    ]
                ], NULL, 'A6');

                // Định dạng border
                $dataRowCount = $this->collection()->count();
                $startRow = 6;
                $endRow = $startRow + $dataRowCount - 1;
                $range = "A{$startRow}:Q{$endRow}";

                // $styleArray = [
                //     'borders' => [
                //         'allBorders' => [
                //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                //             'color' => ['argb' => '000000'],
                //         ],
                //     ],
                // ];
                // $sheet->getStyle($range)->applyFromArray($styleArray);
            },
        ];
    }





    public function map($invoice): array
    {
        $this->index++;
        return [
            $this->index,
            $invoice->denominator_symbol,
            $invoice->invoice_symbol,
            $invoice->invoice_number,
            $invoice->invoice_date->format('d/m/Y'),
            $invoice->seller_tax_code,
            $invoice->seller_name,
            $invoice->seller_address,
            $invoice->total_before_tax,
            $invoice->total_tax,
            $invoice->total_discount,
            $invoice->total_fee,
            $invoice->total_payment,
            $invoice->currency_unit,
            $invoice->exchange_rate,
            $invoice->invoice_status,
            $invoice->invoice_check_result,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        return [
            6 => [ // Dòng heading
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'FFA500', // Màu cam
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],  // Màu border đen
                    ],
                ],
            ],
            // Áp dụng border từ dòng 6 đến dòng cuối cùng
            'A6:Q' . $highestRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],  // Màu border đen
                    ],
                ],
            ],
        ];
    }
}
