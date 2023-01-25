@extends('layouts.admin')
@section('link')
   <link rel="stylesheet" href="{{ asset('css/inc/tree.css') }}">
@endsection
@section('content')
   <div class="container-fluid">
      <div class="list__filters my-4">
         <a href="/admin/orders?tab=current" class="btn btn-outline-info btn-sm px-3 mr-2 position-relative
            {{app('request')->input('tab') === 'current' || app('request')->input('tab') === null ? 'active disable-pointer-event' : ''}}" title="Current">
            <span>Current</span>
            <span class="badge badge-pill badge-danger position-absolute" style="top: -5px; right: -5px;">{{$itemsNum[0] ?? 0}}</span>
         </a>
         <a href="/admin/orders?tab=canceled" class="btn btn-outline-warning btn-sm px-3 mr-2 position-relative
            {{app('request')->input('tab') === 'canceled' ? 'active disable-pointer-event' : ''}}" title="Canceled booking">
            <span>Canceled</span>
            <span class="badge badge-pill badge-danger position-absolute" style="top: -5px; right: -5px;">{{$itemsNum[1] ?? 0}}</span>
         </a>
         <a href="/admin/orders?tab=expired" class="btn btn-outline-secondary btn-sm px-3 position-relative
            {{app('request')->input('tab') === 'expired' ? 'active disable-pointer-event' : ''}}"
            title="Date of this orders event is passed">
            <span>Expired</span>
            <span class="badge badge-pill badge-danger position-absolute" style="top: -5px; right: -5px;">{{$itemsNum[2] ?? 0}}</span>
         </a>
      </div>
      @if(isset($datatableData) && count($items) > 0)
         <table id="main-datatable" class="list" width="100%"
                data-is-column-filters="{{ json_encode($datatableData->isColumnFilters) ?? 'false' }}"
                data-no-sort-columns="{{ json_encode($datatableData->noSortColumns) ?? '[]' }}"
                data-no-filter-columns="{{ json_encode($datatableData->noFilterColumns) ?? '[]' }}"
         >
      @else
         <table id="main-table" class="list" width="100%">
      @endif
         <thead id="main-table-head">
            <tr class="list__head">
               <th class="list__id">#</th>
               <th>Invoice Id</th>
               <th>User</th>
               <th>Course</th>
               <th>Date</th>
               <th>Method</th>
               <th>Count</th>
               <th>Sum total</th>
               <th>Recipient persons</th>
               <th>Status</th>
               <th>Created at</th>
               @if(in_array('actions', $page->func))
                  <th class="list__function"></th>
               @endif
            </tr>
         </thead>


         @if(count($items) > 0)
            <tfoot>
            <tr class="list__head">
               <th class="list__id">#</th>
               <th>Invoice Id</th>
               <th>User</th>
               <th>Course</th>
               <th>Date</th>
               <th>Method</th>
               <th>Count</th>
               <th>Sum total</th>
               <th>Recipient persons</th>
               <th>Status</th>
               <th>Created at</th>
               @if(in_array('actions', $page->func))
                  <th class="list__function"></th>
               @endif
            </tr>
            </tfoot>

            <tbody id="main-table-body">
            @foreach($items as $idx => $item)
               <tr class="list__item{{ app('request')->input('highlight') ? ($item->id === (int) app('request')->input('highlight') ? ' current' : '') : ''}}"
                   data-id="{{ $item->id }}">

                  <td class="list__id">{{ $idx + 1 }}</td>

                  <td data-sort="{{$item->invoice ? $item->invoice->additional_data : null}}"
                      data-search="{{$item->invoice ? $item->invoice->additional_data : null}}"
                  >{{ $item->invoice ? $item->invoice->additional_data : null }}</td>

                  <td data-sort="{{$item->user ? ($item->user->name ? "{$item->user->name} {$item->user->surname}" : $item->user->email) : null}}"
                      data-search="{{$item->user ? ($item->user->name ? "{$item->user->name} {$item->user->surname}" : $item->user->email) : null}}">
                     <span type="button" class="color-gold cursor-pointer" onclick="showUserInfo({{$item->user->id}}, false)" data-id="{{$item->user->id}}">{{ $item->user ? ($item->user->name ? "{$item->user->name} {$item->user->surname}" : $item->user->email) : null }}</span>
                  </td>

                  <td data-sort="{{isset($item->date->event) ? $item->date->event->title : null}}"
                      data-search="{{isset($item->date->event) ? $item->date->event->title : null}}"
                  >
                     @isset($item->date->event)
                        <a href="/admin/events?highlight={{$item->date->event->id}}">{{$item->date->event->title}}</a>
                     @endisset
                  </td>

                  <td data-sort="{{ $item->date ? strtotime($item->date->start) : null }}"
                      data-search="{{ $item->date ?
date('F d D', strtotime($item->date->start)).' - '.date('F d D', strtotime($item->date->end)) : null }}"
                  >{{ $item->date ?
date('F d D', strtotime($item->date->start)).' - '.date('F d D', strtotime($item->date->end)) : null }}</td>

                  <td data-sort="{{ $item->invoice->method ?? ''}}"
                      data-search="{{$item->invoice->method ?? ''}}">
                     @isset($item->invoice)
                        <span>{{$item->invoice->method}}</span>
                        @if($item->invoice->paid_as_company)
                           <span class="badge badge-warning ml-2">Paid as a company</span>
                        @endif
                     @endisset
                  </td>

                  <td data-sort="{{ $item->count }}" data-search="{{ $item->count }}">{{ $item->count }}</td>

                  <td data-sort="{{ $item->invoice->price ?? 0}}"
                      data-search="{{$item->invoice->price ?? 0}}">
                     @isset($item->invoice)
                        <span>{{$item->invoice->price}} {{$item->invoice->currency}}</span>
                     @endisset
                  </td>

                  <td>
                     <ul class="fz-0_8rem list-group list-group-flush">
                     @foreach($item->recipient_persons as $person)
                        @if($person['email'])
                        <li class="list-group-item bg-transparent py-1 px-2">
                           <a href="mailto:{{$person['email']}}">{{$person['email']}}</a>
                           <span>{!!' ('.$person['name'].($person['phone'] ? ', <a href="tel:'.$person['phone'].'" class="phone-mask">'.$person['phone'].'</a>' : '').')'!!}</span>
                        </li>
                        @else
                        <li class="list-group-item bg-transparent py-1 px-2">Unknown person (data not specified)</li>
                        @endif
                     @endforeach
                     </ul>
                  </td>

                  <td data-sort="{{ $item->is_purchased }}"
                      data-search="{{$item->is_purchased === 1 ? 'Purchased' : ($item->is_canceled === 1 ? 'Canceled' : 'Booked')}}">
                     @if($item->is_purchased === 1)
                        <span class="badge badge-info color-white">Purchased</span>
                     @elseif($item->is_canceled === 1)
                        <span class="badge badge-secondary color-white">Canceled</span>
                     @else
                        <span class="badge badge-warning color-white">Booked</span>
                     @endif
                  </td>
                  <td data-sort="{{ strtotime($item->created_at) }}"
                  >{{ $item->created_at }}</td>

                  @if(in_array('actions', $page->func))
                     <td class="list__function list__function_actions">
                        <div class="btn-group">
                           <button type="button" class="btn btn-dark btn-sm px-3 dropdown-toggle"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                           >Actions</button>
                           <div class="dropdown-menu dropdown-menu-right">
                                 @if(isset($item->invoice->company_invoice_url))
                                    <span class="dropdown-item">
                                        <span class="align-middle">Show VAT invoice</span>
                                        <a href="/{{$item->invoice->company_invoice_url ? str_replace('.pdf', '.xlsx', $item->invoice->company_invoice_url) : null}}?rnd={{microtime(true)}}" class="d-inline-flex align-items-center align-middle ml-2" target="_blank">
                                            <img src="{{asset('media/img/icons/excel.png')}}" height="15" alt="PDF Icon">
                                        </a>
                                        <a href="/{{$item->invoice->company_invoice_url ?? null}}?rnd={{microtime(true)}}"
                                           class="d-inline-flex align-items-center align-middle ml-1" target="_blank">
                                            <img src="{{asset('media/img/icons/pdf.png')}}" height="15" alt="PDF Icon">
                                        </a>
                                    </span>
                                    <span class="dropdown-item cursor-pointer" onclick="refreshInvoiceFiles({{$item->invoice->id}})"
                                    >Refresh invoice files</span>
                                 @endif
                                 {{--                              <div class="dropdown-divider"></div>--}}
                           </div>
                        </div>
                     </td>
                  @endif

               </tr>
            @endforeach
            </tbody>
         @endif
      </table>
      @if(count($items) < 1)
         <div class="list-empty">There is no data</div>
      @endif
   </div>
   @include('admin.inc.modal-dialog')
@endsection
@section('js')
   <script src="{{ asset('js/inc/show-user-info.js') }}"></script>
   <script>
      const refreshInvoiceFiles = (invoiceId) => {
         if (!invoiceId) return;
         const refreshInvoiceFilesUrl = "{{config('app.app_url')}}" +`/admin/video-orders/${invoiceId}/refresh-invoice-files`;
         window.fetchServerAction(refreshInvoiceFilesUrl, 'GET', true, 'Excel and pdf files will be updated. Continue?',
            [true, false], ['Files updating is in process and finished soon. Updated files will be available a little later.', '']);
      }

   </script>
@endsection
