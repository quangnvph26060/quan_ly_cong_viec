<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'seller_tax_code',
        'seller_name',
        'seller_address',
        'invoice_symbol',
        'invoice_number',
        'invoice_date',
        'total_before_tax',
        'total_tax',
        'total_discount',
        'total_fee',
        'total_payment',
        'currency_unit',
        'exchange_rate',
        'invoice_status',
        'invoice_check_result',
        'status',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'total_before_tax' => 'float',
        'total_tax' => 'float',
        'total_discount' => 'float',
        'total_fee' => 'float',
        'total_payment' => 'float',
        'exchange_rate' => 'float',
    ];
}
