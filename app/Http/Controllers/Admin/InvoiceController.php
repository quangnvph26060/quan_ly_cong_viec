<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchaseInvoicesExport;
use App\Exports\SalesInvoicesExport;
use App\Http\Controllers\Controller;
use App\Imports\PurchaseInvoicesImport;
use App\Imports\SalesInvoicesImport;
use App\Models\InvoiceModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
  
    
    public function index(Request $request)
    {
        try {
          
            $clients =  InvoiceModel::orderByDesc('created_at')->where('status',0)->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admins.pages.purchase_invoice.table', compact('clients'))->render(),
                    'pagination' => $clients->links('pagination::bootstrap-4')->toHtml()
                ]);
            }

            return view('admins.pages.purchase_invoice.index', compact('clients'));
        } catch (Exception $e) {
            Log::error('Failed to get paginated Client list: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get paginated Client list'], 500);
        }
    }
    public function indexSellerInvoice(Request $request)
    {
        try {
          
            $clients =  InvoiceModel::orderByDesc('created_at')->where('status',1)->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admins.pages.sales_invoice.table', compact('clients'))->render(),
                    'pagination' => $clients->links('pagination::bootstrap-4')->toHtml()
                ]);
            }

            return view('admins.pages.sales_invoice.index', compact('clients'));
        } catch (Exception $e) {
            Log::error('Failed to get paginated Client list: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get paginated Client list'], 500);
        }
    }
    public function export(Request $request)
    {
        $type = $request->query('type'); // 'purchase' hoặc 'sales'

        if ($type === 'purchase') {
            return Excel::download(new PurchaseInvoicesExport, 'purchase_invoices.xlsx');
        } elseif ($type === 'sales') {
            return Excel::download(new SalesInvoicesExport, 'sales_invoices.xlsx');
        } else {
            return redirect()->back()->with('error', 'Loại hóa đơn không hợp lệ.');
        }
    }

    public function import(Request $request)
    {
        $type = $request->query('type'); // 'purchase' hoặc 'sales'

        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($type === 'purchase') {
            Excel::import(new PurchaseInvoicesImport, $request->file('file'));
        } elseif ($type === 'sales') {
            Excel::import(new SalesInvoicesImport, $request->file('file'));
        } else {
            return redirect()->back()->with('error', 'Loại hóa đơn không hợp lệ.');
        }

        return back()->with('success', 'Import thành công!');
    }
}
