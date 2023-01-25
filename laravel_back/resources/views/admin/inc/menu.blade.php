{{-- config('admin._menu') --}}

@isset($link)
	<li class="menu__item{{ $link->route == $page->route || $link->route == "{$page->route}.index" ? ' menu__item_active' : '' }}{{ isset($link->drop) ? ' menu__item_drop' : '' }}">
		<a href="{{ route($link->route) }}" class="menu__link{{ $link->route == $page->route || $link->route == "{$page->route}.index" ? ' menu__link_active' : '' }}">
			<span class="menu__ico icon">
				@if(isset($link->ico))
					<i class="fa-{{ $link->ico }}" aria-hidden="true"></i>
				@elseif(isset($link->svg))
					<img src='{{ asset("img/admin/icons/{$link->svg}.svg") }}' class="img-responsive icon__body" aria-hidden="true" alt="icon">
				@endif
			</span>
			<span class="menu__title">{{ $link->title ?? '' }}</span>

            <!-- badges -->
            @if(isset($link->badge))
                <span class="menu__badge">{!! $link->badge !!}</span>
            @endif

			<!-- arrows for drop menu -->
			@if(isset($link->drop))
				@if(!($link->route == $page->route || $link->route == "{$page->route}.index" || array_key_exists(str_replace('admin.', '', $page->route), $link->drop)))
				<span class="menu__dropico"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
				@elseif($link->route == $page->route || $link->route == "{$page->route}.index" || array_key_exists(str_replace('admin.', '', $page->route), $link->drop))
				<span class="menu__dropico"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
				@endif
			@endif
		</a>
	</li>
@else

	<div class="menu">
		<a href="{{ route('admin.index') }}" class="menu__user">
			<span class="menu__user-name">Admin</span>
		</a>

	@if(count($page->menu))
		<ul class="menu__links">

		@foreach($page->menu as $link)
			@include('admin.inc.menu')

		@if(property_exists($link, 'drop'))
			<ul class="menu__drop{{ $link->route == "{$page->route}.index" || array_key_exists(str_replace('admin.', '', $page->route), $link->drop) ? ' menu__drop_open' : '' }}">

			@foreach($link->drop as $drop)
				@include('admin.inc.menu', [ 'link' => $drop ])
			@endforeach

			</ul>
		@endif

		@endforeach

		</ul>
	@endif

	</div>

@endisset
