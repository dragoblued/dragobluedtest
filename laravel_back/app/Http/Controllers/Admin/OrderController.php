<?php

namespace App\Http\Controllers\Admin;

use App\Invoice;
use App\Jobs\GenerateCompanyInvoiceFile;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends AdminController
{

    public function __construct ()
    {
        parent::__construct();
    }

    public function init ()
    {
        $this->setPage([
            'route' => 'admin.orders',
            'title' => 'Orders - [ ADMIN ]',
            'h1'    => 'Orders',
            'custom' => true,
            'func'  => [
                'actions'
            ]
        ]);
        $this->setModel(Ticket::class);
    }

    public function index(Request $request)
    {
        $this->init();
        $tab = $request->get('tab');

        $expiredItems = $this->model::with(['user', 'date', 'invoice'])
            ->where('is_expired', 1)
            ->orderBy('updated_at')
            ->get();
        $canceledItems = $this->model::with(['user', 'date', 'invoice'])
            ->where([
                ['is_canceled', 1],
                ['is_expired', 0]
            ])
            ->orderBy('updated_at')
            ->get();
        $currentItems = $this->model::with(['user', 'date', 'invoice'])
            ->where([
                ['is_expired', 0],
                ['is_canceled', 0],
            ])
            ->orderBy('updated_at')
            ->get();

        switch ($tab) {
            case 'expired':
                $items = $expiredItems;
                break;
            case 'canceled':
                $items = $canceledItems;
                break;
            default:
                $items = $currentItems;
                break;
        }

        $data = [
            'page'  => $this->getPage(),
            'items' => $items,
            'itemsNum' => [count($currentItems), count($canceledItems), count($expiredItems)],
            'datatableData'  => (object) [
                'isColumnFilters' => true,
                'noSortColumns' => [8,11],
                'noFilterColumns' => [0,1,6,7,10,11]
            ]
        ];
        return view('admin._custom.orders.list', $data);
    }

    public function refreshInvoiceFiles(int $invoiceId): JsonResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $user = $invoice->user;
        if ($invoice && $user) {
            GenerateCompanyInvoiceFile::dispatch($user->id, $invoice->id, false);
        }
        return response()->json();
    }

    protected function checkUpdate ($item): bool {
        return false;
    }

    protected function checkDrop ($item): bool {
        return false;
    }

    public function errorDrop (Request $request)
    {
        if($request->ajax()) {
            $error = 'Removing or updating Orders is forbidden';
            return response()->json($error, 423);
        }
        return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Removing or updating <b>Orders</b> is forbidden");
    }
}
