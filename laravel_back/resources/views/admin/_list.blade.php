@extends('layouts.admin')

@isset($ext_links)
   @push('links')
      @foreach($ext_links as $ext_link)
         {!! $ext_link !!}
      @endforeach
   @endpush
@endisset

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
                     @include($page->route)

                     @if(in_array('edit', $page->func))
                        <th class="list__function"></th>
                     @endif
                     @if(in_array('delete', $page->func))
                        <th class="list__function"></th>
                     @endif
                  </tr>
                  </thead>

                  @if(count($items) > 0)
                     <tfoot>
                     <tr class="list__head">
                        <th class="list__id">#</th>
                        @include($page->route)

                        @if(in_array('edit', $page->func))
                           <th class="list__function"></th>
                        @endif
                        @if(in_array('delete', $page->func))
                           <th class="list__function"></th>
                        @endif
                     </tr>
                     </tfoot>

                     <tbody id="main-table-body">
                     @foreach($items as $idx => $item)
                        <tr class="list__item{{ app('request')->input('highlight') ? ($item->id === (int) app('request')->input('highlight') ? ' current' : '') : ''}}"
                            data-id="{{ $item->id }}">
                           @if($items instanceof Illuminate\Pagination\LengthAwarePaginator)
                              @if(is_object($items) && $items->perPage() && $items->currentPage())
                                 <td class="list__id">{{ ($loop->index + 1) + ($items->perPage() * ($items->currentPage() - 1)) }}</td>
                              @else
                                 <td class="list__id">{{ $item->id }}</td>
                              @endif
                           @else
                              <td class="list__id" data-sort="{{ $idx + 1 }}" data-search="{{ $idx + 1 }}">{{ $idx + 1 }}</td>
                           @endif
                           @include($page->route)
                           @if(in_array('edit', $page->func))
                              <td class="list__function list__function_edit">
                                 <a href="{{ route($page->route.'.edit', $item->id) }}" class="function list__function-button">
                                    <i class="far fa-edit"></i>
                                 </a>
                              </td>
                           @endif

                           @if(in_array('delete', $page->func))
                              <td class="list__function list__function_delete">
                                 <a href="{{ route($page->route.'.destroy', $item->id) }}"
                                    class="function list__function-button js-delete">
                                    <i class="far fa-trash-alt"></i>
                                 </a>
                              </td>
                           @endif

                        </tr>
                     @endforeach
                     </tbody>

                  @endif
               </table>
               @if($items instanceof Illuminate\Pagination\LengthAwarePaginator)
                  {{ $items->links() }}
               @endif

               @if(count($items) == 0)
                  <div class="list-empty">There is no data in the table</div>
      @endif
   </div>
   @include('admin.inc.modal-dialog')

@endsection

@isset($ext_scripts)
   @push('ext_scripts')
      @foreach($ext_scripts as $ext_script)
         {!! $ext_script !!}
      @endforeach
   @endpush
@endisset
@isset($scripts)
   @push('scripts')
      @foreach($scripts as $script)
         {!! $script !!}
      @endforeach
   @endpush
@endisset
