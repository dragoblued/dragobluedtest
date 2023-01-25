<?php

namespace App\Http\Controllers\Admin;

use App\Invoice;
use App\Jobs\GenerateCompanyInvoiceFile;
use App\Ticket;
use App\UserCourse;
use App\UserTopic;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use function Psy\debug;

class VideoOrderController extends AdminController
{

    public function __construct ()
    {
        parent::__construct();
    }

    public function init ()
    {
        $this->setPage([
            'route' => 'admin.video-orders',
            'title' => 'Video Orders - [ ADMIN ]',
            'h1'    => 'Video Orders',
            'custom' => true,
            'func'  => [
                'actions'
            ]
        ]);
        $this->setModel(UserCourse::class);
    }

    public function index(Request $request)
    {
        $this->init();
        $tab = $request->get('tab');
//      if ($tab === 'older') {
//         $courseItems = $this->model::with(['user', 'course', 'invoice'])
//            ->where('is_purchased', 1)
//            ->where('created_at', '<', Carbon::now()->subWeek()->toDateTimeString())
//            ->get();
//         $topicItems = UserTopic::with(['user', 'topic', 'invoice'])
//            ->where('is_purchased', 1)
//            ->where('created_at', '<', Carbon::now()->subWeek()->toDateTimeString())
//            ->get();
//         $items = $courseItems->concat($topicItems);
//      } else {

        $courseItems = $this->model::with(['user', 'course', 'invoice'])
            ->where('is_purchased', 1)
            ->orderBy('updated_at')
            ->get();
        $topicItems = UserTopic::with(['user', 'topic', 'invoice'])
            ->where('is_purchased', 1)
            ->orderBy('updated_at')
            ->get();
        $items = $courseItems
            ->concat($topicItems)
            ->sortBy('created_at')
            ->groupBy(function ($item, $key) {
                return $item['invoice_id'] ? $item['invoice_id'] : "free-{$key}";
            })
            ->values();

        $data = [
            'page'  => $this->getPage(),
            'items' => $items,
            'itemsNum' => [count($items)],
            'datatableData'  => (object) [
                'isColumnFilters' => true,
                'noSortColumns' => [8],
                'noFilterColumns' => [0,1,5,7,8]
            ]
        ];
        return view('admin._custom.video-orders.list', $data);
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

    public function freshInvoices(Request $request)
    {
        $courseItems = UserCourse::where('is_purchased', 1)
            ->whereNotNull('invoice_id')
            ->get();
        $topicItems = UserTopic::where('is_purchased', 1)
            ->whereNotNull('invoice_id')
            ->get();
        $eventItems = Ticket::where('is_purchased', 1)
            ->whereNotNull('invoice_id')
            ->get();
        $groups = $courseItems->concat($topicItems)->concat($eventItems)->sortBy('invoice_id')->values()->groupBy('invoice_id');

        $currentVideoCount = 0;
        $currentEventCount = 0;
        foreach ($groups as $invoiceId => $group) {
            $mixed = false;
            $prev = null;
            foreach ($group as $item) {
                if ($item->table === 'tickets') {
                    if ($prev === 'video') {
                        $mixed = true;
                    }
                    $currentEventCount += 1;
                    $prev = 'event';
                } else {
                    if ($prev === 'event') {
                        $mixed = true;
                    }
                    $currentVideoCount += 1;
                    $prev = 'video';
                }
            }

            if ($mixed) {
                $invoiceIdName = "OPNTMO Nr. {$currentVideoCount}, OPNTK Nr. {$currentEventCount}";
            } elseif ($prev === 'event') {
                $invoiceIdName = "OPNTK Nr. {$currentEventCount}";
            } else {
                $invoiceIdName = "OPNTMO Nr. {$currentVideoCount}";
            }

            $invoice = Invoice::findOrFail($invoiceId);
            $user = $invoice->user;
            if ($invoice && $user) {
                $invoice->additional_data = $invoiceIdName;
                $invoice->save();
                GenerateCompanyInvoiceFile::dispatch($user->id, $invoice->id, false, [], $request->has('should-generate'));
            }
        }
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
