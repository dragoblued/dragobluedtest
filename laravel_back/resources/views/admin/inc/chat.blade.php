@push('links')
   <link rel="stylesheet" href="{{ asset('css/inc/chat.css') }}">
@endpush

<div class="chat narrow-srollbar {{$chatClassName ?? ''}}">
   @isset($displayChatList)
   <div id="chat-list" class="chat__list-col chat-list {{$chatDialogClassName ?? ''}}">
      @foreach($users as $user)
         <div id="chat-list-item-{{$user->id}}" class="chat-list__item chat-list-item">
            <img src="{{$user->avatar_url ? asset($user->avatar_url) : asset('media/img/avatar-default_min.jpg')}}"
                 alt="User Avatar" class="chat-list-item__avatar bdrs-50p cursor-pointer"
                 onclick="showUserInfo({{$user->id}})"/>
            <div class="chat-list-item__body fz-0_9rem cursor-pointer"
                 onclick="showRooms({{$user->id}}, '{{$user->name ?: $user->email}}')"
                 data-toggle="collapse" data-target="#collapse{{$user->id}}" aria-expanded="true"
                 aria-controls="collapse{{$user->id}}">
               <div class="chat-list-item__first-line font-weight-bold">
                  <span class="chat-list-item__first-line-text">{{$user->name ? $user->name : $user->email}}</span>
                  @if($user->newMessagesCount > 0)
                     <span id="user-new-{{$user->id}}" data-count="{{$user->newMessagesCount}}"
                           class="badge badge-danger badge-pill lh-1_5">{{$user->newMessagesCount}}</span>
                  @endif
               </div>
            </div>
         </div>
         <div class="chat-list__item-expand collapse" id="collapse{{$user->id}}"
              aria-labelledby="chat-list-item-{{$user->id}}" data-parent="#chat-list">
            @foreach($user->lessonRooms as $room)
               <div id="room-{{$room->id}}" class="chat-list-item chat-list-item_sub">
                  <div class="chat-list-item__body fz-0_8rem cursor-pointer" onclick="showDialog({{$room->id}})">
                     <div class="chat-list-item__first-line font-weight-bold">
                        <span>{{$room->subject ? $room->subject->title : null}}</span>
                        <span class="chat-list-item__first-line-date">
                              @if($room->lastMessage)
                              @if((strtotime(now()) - strtotime($room->lastMessage->created_at)) / 1000 < 1440)
                                 {{date('H:m', strtotime($room->lastMessage->created_at))}}
                              @else
                                 {{date('Y.m.d', strtotime($room->lastMessage->created_at))}}
                              @endif
                           @endif
                           @if($room->newMessagesCount > 0)
                              <span id="room-new-{{$room->id}}" data-count="{{$room->newMessagesCount}}"
                                    class="badge badge-danger badge-pill lh-1_5 ml-2">{{$room->newMessagesCount}}</span>
                           @endif
                              </span>
                     </div>
                     @if($room->lastMessage)
                        <div class="chat-list-item__second-line fz-0_7rem">
                           @if($room->lastMessage->user_id === $page->admin->id)
                              <span class="color-light-blue">You: </span>
                           @endif
                           <span class="chat-list-item__second-line-text">{{$room->lastMessage->text}}</span>
                        </div>
                     @endif
                  </div>
               </div>
            @endforeach
         </div>
      @endforeach
   </div>
   @endisset

   @isset($displayChatDialog)
   <div id="chat-dialog" class="chat__dialog-col chat-dialog {{$chatDialogClassName ?? ''}}">
      <span id="chat-dialog-empty" class="chat-dialog__empty badge badge-light"
      >{{$emptyDialogMessage ?? 'There are no messages yet'}}</span>
      @isset($displayChatDialogHeader)
      <div id="chat-dialog-header" class="chat-dialog__header chat-dialog-header d-none">
         <span id="chat-dialog-header-current" class="chat-dialog-header__sign fz-1_2rem"></span>
         <span class="chat-dialog-header__members fz-1_2rem ml-auto">
                  <span>Members: </span>
                  <span id="chat-dialog-header-members"></span>
               </span>
      </div>
      @endisset
{{--      <div id="chat-dialog-tabs" class="chat-dialog__tabs chat-dialog-tabs">--}}
{{--         <button class="btn btn-secondary chat-dialog-tabs__tab">All</button>--}}
{{--         <button class="btn btn-secondary chat-dialog-tabs__tab">Marked</button>--}}
{{--      </div>--}}
      <div id="chat-dialog-main" class="chat-dialog__main"></div>
      @isset($displayChatDialogFooter)
      <div id="chat-dialog-footer" class="chat-dialog__footer chat-dialog-footer d-none">
         <div id="chat-dialog-link" class="chat-dialog-footer__link chat-dialog-link"></div>
         <div id="chat-dialog-typing" class="chat-dialog-footer__typing">Someone typing...</div>
         <div class="chat-dialog-footer__send">
            <div contenteditable="true" id="new-message" class="chat-dialog-footer__input"
                 placeholder="Send a message..." onkeypress="onTyping(event)"></div>
            <i class="fa fa-arrow-circle-right fz-1_5rem ml-3 chat-dialog-footer__submit-icon"
               id="new-message-submit-btn" onclick="sendNewMessage()"></i>
         </div>
      </div>
      @endisset
   </div>
   @endisset
   <div class="dropdown-menu dropdown-menu-sm" id="context-menu">
      <span class="dropdown-item" id="context-menu-remove-message" data-message="" onclick="removeMessage(event)">Delete message</span>
   </div>
</div>

@push('scripts')
   <script src="{{ asset('js/echo-init.js') }}"></script>
   <script src="{{ asset('js/inc/chat.js') }}"></script>
@endpush
