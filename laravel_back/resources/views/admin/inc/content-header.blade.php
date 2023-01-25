<h1 class="content__header">
	{{ property_exists($page, 'h1') ? $page->h1 : $page->title }}

@if(property_exists($page, 'func'))
	@if(in_array('create', $page->func))
		@if(isset($page->current))
			@if($page->current == 'create')
			@elseif (Route::has("{$page->route}.create"))
				<a href="{{ route("{$page->route}.create") }}" class="content__add function">
					<i class="fa-plus-square far"></i>
				</a>
			@else
			@endif
		@else
			<a href="{{ route("{$page->route}.create") }}" class="content__add function">
				<i class="fa-plus-square far"></i>
			</a>
		@endif
	@endif
@endif

	</h1>

@if(Session::has('alert'))
	<div class="alert">
		{!! Session::get('alert') !!}
	</div>
@endif
