@extends('layouts.admin')
@section('content')
   <div class="container-fluid">
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
                     <th>Course</th>
                     <th>Year</th>
                     <th>Date</th>
                     <th>Lang</th>
                     <th>Seats total</th>
                     <th>Seats vacant</th>
                     <th>Seats booked now</th>
                     <th>Seats purchased</th>
                     <th>Created at</th>
                     @if(in_array('edit', array_keys($page->func)))
                        <th class="list__function"></th>
                     @endif
                     @if(in_array('delete', array_keys($page->func)))
                        <th class="list__function"></th>
                     @endif
                  </tr>
                  </thead>


                  @if(count($items) > 0)
                     <tfoot>
                     <tr class="list__head">
                        <th class="list__id">#</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Date</th>
                        <th>Lang</th>
                        <th>Seats total</th>
                        <th>Seats vacant</th>
                        <th>Seats booked now</th>
                        <th>Seats purchased</th>
                        <th>Created at</th>
                        @if(in_array('edit', array_keys($page->func)))
                           <th class="list__function"></th>
                        @endif
                        @if(in_array('delete', array_keys($page->func)))
                           <th class="list__function"></th>
                        @endif
                     </tr>
                     </tfoot>

                     <tbody id="main-table-body">
                     @foreach($items as $item)
                        <tr class="list__item{{ app('request')->input('highlight') ? ($item->id === (int) app('request')->input('highlight') ? ' current' : '') : ''}}"
                            data-id="{{ $item->id }}">

                           <td class="list__id">{{ $item->id }}</td>
                           <td>
                              @if(!is_null($item->event))
                                 <a href="{{ config('app.app_url').'/admin/events?highlight='.$item->event->id }}"
                                    target="_self"
                                 >{{ $item->event->title }}</a>
                              @endif
                           </td>
                           <td>{{ $item->year }}</td>
                           <td>{{ date('F d D', strtotime($item->start)).' - '.date('F d D', strtotime($item->end)) }}</td>
                           <td>{{ $item->lang }}</td>
                           <td>{{ $item->seats_total }}</td>
                           <td>{{ $item->seats_vacant }}</td>
                           <td>
                        <span class="color-gold cursor-pointer"
                              onclick="showList('{{$item->id}}', 'booked', '{{$item->event->title}} Course. {{date('Y', strtotime($item->start)).' '.date('F d', strtotime($item->start)).' - '.date('F d', strtotime($item->end))}}')">{{ $item->seats_booked }}</span>
                           </td>
                           <td>
                        <span class="color-gold cursor-pointer"
                              onclick="showList('{{$item->id}}', 'purchased', '{{$item->event->title}} Course. {{date('Y', strtotime($item->start)).' '.date('F d', strtotime($item->start)).' - '.date('F d', strtotime($item->end))}}')">{{ $item->seats_purchased }}</span>
                           </td>
                           <td data-sort="{{ strtotime($item->created_at) }}"
                           >{{ $item->created_at }}</td>
                           @if(in_array('edit', array_keys($page->func)))
                              <td class="list__function list__function_edit">
                                 <a href="{{ route($page->route.'.edit', $item->id) }}" class="function list__function-button">
                                    <i class="far fa-edit"></i>
                                 </a>
                              </td>
                           @endif
                           @if(in_array('delete', array_keys($page->func)))
                              <td class="list__function list__function_delete">
                                 <a href="{{ route($page->route.'.destroy', $item->id) }}" class="function list__function-button js-delete">
                                    <i class="far fa-trash-alt"></i>
                                 </a>
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
@endsection

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title font-weight-bold fz-1_5rem" id="exampleModalLongTitle">Customers list</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div id="exampleModalCenterBody" class="modal-body modal-card">
         </div>
         <div class="modal-footer">
            <button type="button" onclick="printList('exampleModalCenterBody', '{{ asset('css/admin.css') }}')" class="btn btn-info">Print</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>
@section('js')
   <script>
      const fillModal = (users, title) => {
         console.log(users, title);
         const modalBody = document.getElementById('exampleModalCenterBody');
         modalBody.innerHTML = '';

         if (users instanceof Array) {
            const dateTitle = document.createElement('div');
            dateTitle.classList.add('modal-card__title');
            dateTitle.innerHTML = title;
            modalBody.appendChild(dateTitle);
            users.forEach((user, idx) => {
               const personTitle = document.createElement('div');
               personTitle.classList.add('modal-card__line-title');
               personTitle.innerHTML = 'Person ' + (idx + 1);
               modalBody.appendChild(personTitle);
               if (user.name) {
                  const p = document.createElement('p');
                  p.classList.add('modal-card__line');
                  p.innerHTML = '<span>Name: <\/span>' + '<span class="ml-3">'+user.name+'<\/span>';
                  modalBody.appendChild(p);
               }
               if (user.email) {
                  const p = document.createElement('p');
                  p.classList.add('modal-card__line');
                  p.innerHTML = '<span>Email: <\/span>' + '<a class="ml-3" href="mailto:'+user.email+'">'+user.email+'<\/a>';
                  modalBody.appendChild(p);
               }
               if (user.phone) {
                  const p = document.createElement('p');
                  p.classList.add('modal-card__line');
                  p.innerHTML = '<span>Phone number: <\/span>' + '<a class="ml-3 phone-mask" href="tel:'+user.phone+'">'+user.phone+'<\/a>';
                  modalBody.appendChild(p);
               }
            });
         }
      }
      const showList = (id, type, title) => {
         console.log(id, type);
         $.ajax({
            type:'GET',
            url:`/admin/dates/${id}/get-customers-list/${type}`,
            success: function(data) {
               fillModal(data, title);
               $('#exampleModalCenter').modal('show');
            },
            error: function(error) {
               console.log(error);
            }
         });
      }

      const printList = (blockId, stylesLink) => {
         console.log(stylesLink);
         const data = document.getElementById(blockId).innerHTML;
         var mywindow = window.open('', 'PRINT', 'height=800,width=600');
         mywindow.document.write(`<html><head>`);
         mywindow.document.write(`<link rel="stylesheet" href="${stylesLink}" media="print" type="text/css"/>`);
         mywindow.document.write(`<\/head><body>`);
         mywindow.document.write(data);
         mywindow.document.write(`<\/body><\/html>`);
         mywindow.document.close();
         mywindow.focus();

         mywindow.print();
         setTimeout(() => {mywindow.print()},1500);
         mywindow.close();

         return true;
      }
   </script>
@endsection
