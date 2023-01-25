@extends('layouts.admin')
@section('content')
   <div class="container-fluid">
      @if(isset($search_form))
         <table id="main-table-search" class="list" width="100%">
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
                  </tr>
                  </thead>

                  @if(count($items) > 0)
                     <tfoot>
                     <tr class="list__head">
                        <td class="list__id">#</td>
                        @include($page->route)

                        @if(in_array('edit', $page->func))
                           <td class="list__function"></td>
                        @endif
                     </tr>
                     </tfoot>

                     <tbody id="main-table-body">
                     @foreach($items as $item)
                        <tr class="list__item{{ app('request')->input('highlight') ? ($item->id === (int) app('request')->input('highlight') ? ' current' : '') : ''}}"
                            data-id="{{ $item->id }}"
                        >
                           @if($items instanceof Illuminate\Pagination\LengthAwarePaginator)
                              @if(is_object($items) && $items->perPage() && $items->currentPage())
                                 <td class="list__id">{{ ($loop->index + 1) + ($items->perPage() * ($items->currentPage() - 1)) }}</td>
                              @else
                                 <td class="list__id">{{ $item->id }}</td>
                              @endif
                           @else
                              <td class="list__id">{{ $item->id }}</td>
                           @endif

                           @include($page->route)
                           @if(in_array('edit', $page->func))
                              <td class="list__function list__function_edit">
{{--                                 @dd($page->route.'.edit')--}}
{{--                                 <a href="{{ route('page_content.edit', [$item->id]) }}" class="function list__function-button">--}}

                                 <a href="{{ route($page->route.'.edit', $item->id) }}" class="function list__function-button">
                                    <i class="far fa-edit"></i>
                                 </a>
                              </td>
                           @endif
                        </tr>
                     @endforeach
                     </tbody>
                  @endif
               </table>
               @if(Route::currentRouteName() !== 'admin.social_links.index')
                  @if($items instanceof Illuminate\Pagination\LengthAwarePaginator)
                     {{ $items->links() }}
                  @endif
               @endif

               @if(count($items) == 0)
                  <div class="list-empty">There is no data in the table</div>
      @endif
   </div>

@endsection
