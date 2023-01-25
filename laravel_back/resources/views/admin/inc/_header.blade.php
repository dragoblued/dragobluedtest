<header class="header">
     <div class="header__body">
         <div class="header__burger">
             <span></span>
         </div>
         <nav class="header__menu">
             <ul class="header__list">
                 <li>
                     <div class="header__links">
                         <a href="{{ config('app.site_url') }}" class="header__link" target="_blank">Go to the site</a>
                     </div>
                 </li>
                 <li>
                     <a href="{{ route('logout') }}" class="header__link header__link_exit">
                         <i class="header__ico fa fa-sign-out-alt" aria-hidden="true"></i>
                         Exit
                     </a>
                     <form action="{{ route('logout') }}" method="POST" class="logout-form">@csrf</form>
                 </li>
             </ul>
         </nav>
     </div>
</header>
