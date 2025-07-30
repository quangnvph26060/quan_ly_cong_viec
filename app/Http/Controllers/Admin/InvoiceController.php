<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchaseInvoicesExport;
use App\Exports\SalesInvoicesExport;
use App\Http\Controllers\Controller;
use App\Imports\PurchaseInvoicesImport;
use App\Imports\SalesInvoicesImport;
use App\Models\InvoiceModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{


    public function index(Request $request)
    {
        try {
            $start_date = $request->start_date;
            $end_date   = $request->end_date;
            $mst        = $request->mst;
            $tencongty  = $request->tencongty;
            $tax        = $request->tax;
            $clients = InvoiceModel::query()
                ->when($mst, function ($query, $mst) {
                    $query->where('seller_tax_code', 'like', "%$mst%");
                })
                ->when($tencongty, function ($query, $tencongty) {
                    $query->where('seller_name', 'like', "%$tencongty%");
                })
                ->when($tax !== null, function ($query) use ($tax) {
                    if ($tax == 0) {
                        $query->where('total_tax', '>', 0);
                    } elseif ($tax == 1) {
                        $query->where('total_tax', '<=', 0);
                    }
                })
                ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    try {
                        $start = Carbon::parse($start_date)->startOfDay();
                        $end = Carbon::parse($end_date)->endOfDay();
                        $query->whereBetween('invoice_date', [$start, $end]);
                    } catch (Exception $e) {
                        Log::error("Invalid date format: $start_date - $end_date");
                    }
                })

                ->where('status', 0)
                ->orderByDesc('invoice_date')
                ->paginate(10);
                $start = Carbon::parse($start_date)->startOfDay();
                $end = Carbon::parse($end_date)->endOfDay();
            $sumBeforeTax = InvoiceModel::whereBetween('invoice_date', [$start, $end])->where('status', 0)->sum('total_before_tax');
            $sumTax       = InvoiceModel::whereBetween('invoice_date', [$start, $end])->where('status', 0)->sum('total_tax');
            $sumPayment   = InvoiceModel::whereBetween('invoice_date', [$start, $end])->where('status', 0)->sum('total_payment');
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admins.pages.purchase_invoice.table', compact('clients', 'sumBeforeTax', 'sumTax', 'sumPayment'))->render(),
                    'pagination' => $clients->links('pagination::bootstrap-4')->toHtml()
                ]);
            }

            return view('admins.pages.purchase_invoice.index', compact('clients', 'sumBeforeTax', 'sumTax', 'sumPayment'));
        } catch (Exception $e) {
            Log::error('Failed to get paginated Client list: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get paginated Client list'], 500);
        }
    }
    public function indexSellerInvoice(Request $request)
    {
        try {
            $start_date = $request->start_date;
            $end_date   = $request->end_date;
            $mst        = $request->mst;
            $tencongty  = $request->tencongty;
            $tax        = $request->tax;
            $clients = InvoiceModel::query()
                ->when($mst, function ($query, $mst) {
                    $query->where('seller_tax_code', 'like', "%$mst%");
                })
                ->when($tencongty, function ($query, $tencongty) {
                    $query->where('seller_name', 'like', "%$tencongty%");
                })
                ->when($tax !== null, function ($query) use ($tax) {
                    if ($tax == 0) {
                        $query->where('total_tax', '>', 0);
                    } elseif ($tax == 1) {
                        $query->where('total_tax', '<=', 0);
                    }
                })
                ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    try {
                        $start = Carbon::parse($start_date)->startOfDay();
                        $end = Carbon::parse($end_date)->endOfDay();
                        $query->whereBetween('invoice_date', [$start, $end]);
                    } catch (Exception $e) {
                        Log::error("Invalid date format: $start_date - $end_date");
                    }
                })

                ->where('status', 1)
                ->orderByDesc('invoice_date')
                ->paginate(10);
                $start = Carbon::parse($start_date)->startOfDay();
                $end = Carbon::parse($end_date)->endOfDay();
              
            $sumBeforeTax = InvoiceModel::whereBetween('invoice_date', [$start, $end])->where('status', 1)->sum('total_before_tax');
            $sumTax = InvoiceModel::whereBetween('invoice_date', [$start, $end])->where('status', 1)->sum('total_tax');
            $sumPayment = InvoiceModel::whereBetween('invoice_date', [$start, $end])->where('status', 1)->sum('total_payment');
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admins.pages.sales_invoice.table', compact('clients', 'sumBeforeTax', 'sumTax', 'sumPayment'))->render(),
                    'pagination' => $clients->links('pagination::bootstrap-4')->toHtml()
                ]);
            }

            return view('admins.pages.sales_invoice.index', compact('clients', 'sumBeforeTax', 'sumTax', 'sumPayment'));
        } catch (Exception $e) {
            Log::error('Failed to get paginated Client list: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get paginated Client list'], 500);
        }
    }
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            // Xử lý tìm kiếm theo tên hoặc số điện thoại
            if (preg_match('/\d/', $query)) {
                //  $clients = $this->clientService->getClientByPhone($query);
            } else {
                //  $clients = $this->clientService->getClientByName($query);
            }

            if ($request->ajax()) {
                $html = view('admins.pages.client.table', compact('clients'))->render();
                $pagination = $clients->appends(['query' => $query])->links('pagination::bootstrap-4')->render();
                return response()->json(['html' => $html, 'pagination' => $pagination]);
            }
            return view('admins.pages.sales_invoice.index', compact('clients'));
        } catch (Exception $e) {
            Log::error('Failed to search clients: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to search clients'], 500);
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
        Log::info($type);
        if ($type === 'purchase') {
            Excel::import(new PurchaseInvoicesImport, $request->file('file'));
        } elseif ($type === 'sales') {
            Excel::import(new SalesInvoicesImport, $request->file('file'));
        } else {
            return redirect()->back()->with('error', 'Loại hóa đơn không hợp lệ.');
        }

        return back()->with('success', 'Import thành công!');
    }
    public function deleteInvoice($id)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Không tìm thấy dữ liệu');
        }

        $deleteInvoice = InvoiceModel::find($id);
        if (!$deleteInvoice) {
            return redirect()->back()->with('error', 'Không tìm thấy hoá đơn');
        }

        $deleteInvoice->delete();
        return back()->with('success', 'Xoá thành công');
    }
    public function deleteInvoiceAll(Request $request)
    {
        $ids = $request->data;

        if (empty($ids)) {
            return response()->json(['message' => 'Không có dữ liệu để xoá'], 400);
        }
        InvoiceModel::whereIn('id', $ids)->delete();

        return response()->json(['status' => 'success', 'message' => 'Xoá thành công']);
    }
}
