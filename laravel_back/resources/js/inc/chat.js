import Toastify from "toastify-js";

let chatAuthUserId;
let chatAuthUserName;
let chatShouldNotifySound;
let chatShouldMarkingAsRead;

let chatCurrentUserId = null;
let chatCurrentUserName = null;
let chatCurrentRooms = [];
let chatCurrentRoomId = null;
let chatCurrentRoom = null;
let chatCurrentMembers = [];
let chatCurrentMessages = [];
let chatMarkedMessagesHTMLElement = null;
let chatMarkedMessagesHTMLElementId = null;
let chatMarkedMessages = [];

let chatIsScrolling = false;
let chatLinkId;
let chatIsLoading = false;
let chatIsSubmitting = false;
let chatPageNumber = 0;
let chatNoMoreMessages = false;
let chatWindow;
let chatCurrentPrivateChannel;
let chatTypingTimeout;
const notifySound = new Audio('/audio/notify.mp3');

const setChatVars = (adminId, adminName, shouldNotifySound = true,
                     shouldMarkingAsRead = true, markedMessagesHTMLElementId = null) => {
   chatAuthUserId = adminId;
   chatAuthUserName = adminName;
   chatShouldNotifySound = shouldNotifySound;
   chatShouldMarkingAsRead = shouldMarkingAsRead;
   chatMarkedMessagesHTMLElementId = markedMessagesHTMLElementId;
}

const onChatInit = () => {
   console.log('Chat init');
   chatWindow = document.getElementById('chat-dialog-main');
   if (chatMarkedMessagesHTMLElementId) {
      chatMarkedMessagesHTMLElement = document.getElementById(chatMarkedMessagesHTMLElementId);
   }
   chatWindow.addEventListener('scroll', checkIfScrolledToTop);
   document.addEventListener('click', closeContextMenu);
}

const getChatAvatarTemplate = (user) => {
   return `<img src="/${user.avatar_url ?? 'media/img/avatar-default_min.jpg'}"
           alt="User Avatar" class="chat-dialog-header__avatar"
           onclick="showUserInfo(${user.id})"/>`;
}

const getChatLinkTemplate = (message) => {
   return `<i class="chat-dialog-link__icon fa fa-reply fz-1_5rem mr-3 cursor-pointer"
              onclick="goToLink(${message.id})"></i>
            <div class="chat-dialog-link__body">
               <div class="chat-dialog-link__user cursor-pointer"
                    onclick="showUserInfo(${message.user_id})">${message.user_name}</div>
               <span class="chat-dialog-link__text">${message.text}</span>
            </div>
            <i class="chat-dialog-link__cancel-icon fa fa-times-circle fz-1_5rem ml-3 cursor-pointer"
               onclick="closeLink()"
            ></i>`;
}

const getChatMessageTemplate = (message, right = '') => {
   let linkTemplate = '';
   if (message.link_message) {
      linkTemplate =
         `<div class="chat-message__link chat-message-link" data-link="${message.link}" onclick="goToLink(${message.link})">
            <span class="chat-message-link__user"
            >${message.link_message.user?.name ? message.link_message.user?.name : message.link_message.user?.email}</span>
            <span class="chat-message-link__text">${message.link_message.text}</span>
         </div>`;
   }
   const marked = message.status === 2 ? 'marked' : '';
   return `<div id="chat-message-${message.id}" data-message="${message.id}" class="chat-dialog__item chat-message ${right} ${marked}"
             ondblclick="setLink(${message.id})" oncontextmenu="openContextMenu(event)">
               <img src="/${message.user_avatar_url ? message.user_avatar_url : 'media/img/avatar-default_preload.jpg'}"
               class="chat-message__avatar" alt="Avatar">
               <div class="chat-message__first-line">
                <span class="chat-message__user cursor-pointer" onclick="showUserInfo(${message.user_id})">${message.user_name}</span>
                <i class="chat-message__mark-icon cursor-pointer fa fa-star ml-1 "
                 onclick="markMessage(${message.id})"></i>
               </div>
               ${linkTemplate}
               <span class="chat-message__text">${message.text}</span>
               <div class="chat-message__footer">${new Date(message.created_at.replace(/-/g, "/")).toLocaleString()}</div>
            </div>`;
}

const getChatMarkedMessageTemplate = (message, right = '') => {
   /* 0 - new
      1 - viewed
      2 - marked
   */
   let linkTemplate = '';
   if (message.link_message) {
      linkTemplate =
         `<div class="chat-message__link chat-message-link" data-link="${message.link}">
            <span class="chat-message-link__user"
            >${message.link_message.user?.name ? message.link_message.user?.name : message.link_message.user?.email}</span>
            <span class="chat-message-link__text">${message.link_message.text}</span>
         </div>`;
   }
   return `<div id="chat-marked-message-${message.id}" data-message="${message.id}"
                class="chat-dialog__item chat-message ${right} marked">
               <img src="/${message.user_avatar_url ? message.user_avatar_url : 'media/img/avatar-default_preload.jpg'}"
               class="chat-message__avatar" alt="Avatar">
               <div class="chat-message__first-line">
                <span class="chat-message__user cursor-pointer" onclick="showUserInfo(${message.user_id})">${message.user_name}</span>
                <i class="chat-message__mark-icon cursor-pointer fa fa-star ml-1 "
                 onclick="markMessage(${message.id}, 1)"></i>
               </div>
               ${linkTemplate}
               <span class="chat-message__text">${message.text}</span>
               <div class="chat-message__footer">${new Date(message.created_at.replace(/-/g, "/")).toLocaleString()}</div>
            </div>`;
}

const setLink = (messageId) => {
   const message = chatCurrentMessages.find(mes => mes.id === messageId);
   const linkBlock = document.getElementById('chat-dialog-link');
   // console.log(message);
   if (!message || !linkBlock) return;
   linkBlock.innerHTML = getChatLinkTemplate(message);
   chatLinkId = messageId;
   focusTypingField();
}

const closeLink = () => {
   const linkBlock = document.getElementById('chat-dialog-link');
   if (linkBlock) {
      linkBlock.innerHTML = '';
   }
   chatLinkId = null;
}

const emptySendMessageInput = () => {
   const newMessageBlock = document.getElementById('new-message');
   if (newMessageBlock) {
      newMessageBlock.innerText = '';
   }
}

const saveDrafts = () => {
   const newMessageBlock = document.getElementById('new-message');
   let drafts = JSON.parse(localStorage.getItem('chat-drafts'));
   if (!drafts) {
      drafts = {};
   }
   drafts[chatCurrentRoomId] = {message: newMessageBlock.innerText, linkId: chatLinkId};
   localStorage.setItem('chat-drafts', JSON.stringify(drafts));
}

const patchNewMessageInput = (value) => {
   const newMessageBlock = document.getElementById('new-message');
   if (newMessageBlock) {
      newMessageBlock.innerText = value;
   }
}

const setFromDraft = (roomId) => {
   const drafts = JSON.parse(localStorage.getItem('chat-drafts'));
   if (!drafts) return;
   const draft = drafts[roomId];
   // console.log(draft);
   if (draft) {
      patchNewMessageInput(draft.message);
      setLink(draft.linkId);
   }
}

const cleanState = (reverse = false) => {
   if (!reverse) {
      saveDrafts();
   }
   closeLink();
   emptySendMessageInput();
   chatPageNumber = 0;
   chatNoMoreMessages = false;
   chatCurrentMessages = [];
   chatCurrentMembers = [];
   if (reverse) {
      saveDrafts();
   }
}

const openContextMenu = (event) => {
   event.preventDefault();
   const top = event.pageY;
   const left = event.pageX;
   const menu = document.getElementById('context-menu');
   if (menu) {
      menu.style.top = top + 'px';
      menu.style.left = left + 'px';
      menu.classList.add('show');
      const currId = event.currentTarget.getAttribute('data-message');
      document.getElementById('context-menu-remove-message')?.setAttribute('data-message', currId);
   }
   return false;
}

const closeContextMenu = () => {
   const menu = document.getElementById('context-menu');
   if (menu) {
      menu.classList.remove('show');
   }
}

const fillRooms = (data) => {
   // console.log(data, chatCurrentUserId);
   if (data instanceof Array) {
      chatCurrentRooms = data;
   }
}

const focusTypingField = () => {
   document.getElementById('new-message')?.focus();
}

const setHeader = (show = true) => {
   if (show) {
      if (document.getElementById('chat-dialog-header')) {
         let members = '';
         chatCurrentMembers.forEach(member => {
            members += getChatAvatarTemplate(member);
         })
         document.getElementById('chat-dialog-header-members').innerHTML = members;
         document.getElementById('chat-dialog-header').classList.remove('d-none');
         document.getElementById('chat-dialog-header-current').innerHTML =
            `<span class="cursor-pointer" onclick="showUserInfo(${chatCurrentUserId})">${chatCurrentUserName}</span> &#8212;
          <a class="color-inherit" href="/admin/lessons?highlight=${chatCurrentRoom?.subject?.id}">${chatCurrentRoom?.subject?.title}</a>`;
      }
      document.getElementById('chat-dialog-empty')?.classList.add('d-none');


   } else {
      document.getElementById('chat-dialog-header')?.classList.add('d-none');
      document.getElementById('chat-dialog-empty')?.classList.remove('d-none');
   }
}

const setFooter = (show = true) => {
   if (show) {
      document.getElementById('chat-dialog-footer')?.classList.remove('d-none');
      focusTypingField();
   } else {
      document.getElementById('chat-dialog-footer')?.classList.add('d-none');
   }
}

const scrollBottom = () => {
   const dialogWindow = document.getElementById('chat-dialog-main');
   if (!dialogWindow) return;
   dialogWindow.scrollTop = dialogWindow.scrollHeight;
}

const markAsRead = (roomId = chatCurrentRoomId, userId = chatCurrentUserId) => {
   if(!chatShouldMarkingAsRead) return;
   const roomNewBadge = document.getElementById('room-new-'+roomId);
   if (!roomId || !roomNewBadge) return;
   const newRoomCount = parseInt(roomNewBadge.getAttribute('data-count'), 10);
   const userNewBadge = document.getElementById('user-new-'+userId);
   if (userNewBadge) {
      const newUserCount = parseInt(userNewBadge.getAttribute('data-count'), 10);
      if (newUserCount - newRoomCount > 0) {
         userNewBadge.setAttribute('data-count', newUserCount - newRoomCount);
         userNewBadge.innerText = newUserCount - newRoomCount;
      } else {
         userNewBadge.remove();
      }
   }
   const chatNewBadge = document.getElementById('menu-chat-new');
   if (chatNewBadge) {
      const newChatCount = parseInt(chatNewBadge.getAttribute('data-count'), 10);
      if (newChatCount - newRoomCount > 0) {
         chatNewBadge.setAttribute('data-count', newChatCount - newRoomCount);
         chatNewBadge.innerText = newChatCount - newRoomCount;
      } else {
         chatNewBadge.remove();
      }
   }
   roomNewBadge.remove();
}

const showTypingBlock = (name) => {
   clearTimeout(chatTypingTimeout);
   const typingBlock = document.getElementById('chat-dialog-typing');
   typingBlock.innerText = name + ' is typing...';
   typingBlock.classList.add('show');
   chatTypingTimeout = setTimeout(() => {
      typingBlock.classList.remove('show');
   }, 2000);
}

const onTyping = (event) => {
   if ((event.key === 'Enter' || event.keyCode === 13) && !event.shiftKey) {
      event.preventDefault();
      sendNewMessage();
   }
   chatCurrentPrivateChannel.whisper('typing', {name: chatAuthUserName, room: chatCurrentRoomId});
}

const playNotifySound = () => {
   if (!chatShouldNotifySound) return;
   notifySound.play();
}

const connectToBroadcasting = (roomId) => {
   console.log('Connecting', roomId);
   chatCurrentPrivateChannel = window.Echo.private('room.'+roomId)
      .listen('Message', ({message}) => {
         if (!message.message || !message.room_id || !message.id) return;
         if (message.action === 'add') {
            pushMessage(message.message, chatAuthUserId === message.id);
            playNotifySound();
         } else if (message.action === 'delete') {
            shiftMessage(message.id)
         }
      })
      .listenForWhisper('typing', ({name}) => {
          showTypingBlock(name);
      })
      .listenForWhisper('mark-message', ({message, action}) => {
         // console.log('mark-message whisper', message, action);
         if (action && message) {
            onMarkingMessage(message, action);
         }
      });
}

const closeBroadcasting = (roomId) => {
   console.log('Leave', roomId);
   if (!roomId) return;
   window.Echo.leave('room.'+roomId);
   chatCurrentPrivateChannel = null;
}

const fillDialog = (data) => {
   // console.log(data, chatWindow);
   if (!chatWindow || !data.messages) return;
   if (data.messages instanceof Array) {
      chatCurrentMembers = data.users ?? [];
      setHeader();
      setFooter();
      chatCurrentMessages = data.messages;
      chatWindow.innerHTML = '';
      let newHtml = '';
      let right = '';
      chatCurrentMessages.forEach(message => {
         right = '';
         if (message.user_id === chatAuthUserId) {
            right = 'right';
         }
         newHtml += getChatMessageTemplate(message, right);
      });
      chatWindow.innerHTML = newHtml;
      setFromDraft(chatCurrentRoomId);
      scrollBottom();
      markAsRead(chatCurrentRoomId);
   } else {
      setHeader(false);
      setFooter(false);
   }
}

const fillMarkedMessagesList = (data, elementId = chatMarkedMessagesHTMLElementId) => {
   // console.log(data, elementId);
   if (elementId) {
      chatMarkedMessagesHTMLElement = document.getElementById(elementId);
   }
   if (!chatMarkedMessagesHTMLElement || !data.markedMessages) return;
   if (data.markedMessages instanceof Array) {
      if (data.markedMessages.length < 1) return;
      chatMarkedMessages = data.markedMessages;
      chatMarkedMessagesHTMLElement.innerHTML = '';
      let newHtml = '';
      let right = '';
      chatMarkedMessages.reverse().forEach(message => {
         right = '';
         if (message.user_id === chatAuthUserId) {
            right = 'right';
         }
         newHtml += getChatMarkedMessageTemplate(message, right);
      });
      chatMarkedMessagesHTMLElement.innerHTML = newHtml;
      chatMarkedMessagesHTMLElement.classList.add('inserted');
   }
}

const cleanBlocksActive = () => {
   Array.from(document.getElementsByClassName('chat-list__item-expand')).forEach(expandBlock => {
      Array.from(expandBlock.children).forEach(roomBlock => {
         roomBlock.classList.remove('active');
      });
   });
}

const setBlockActive = (roomId) => {
   cleanBlocksActive(chatCurrentUserId);
   if (document.getElementById('room-' + roomId)) {
      document.getElementById('room-' + roomId).classList.add('active');
   }
}

const showRooms = (userId, userName) => {
   console.log(chatCurrentUserId, userId, userName);
   if (chatCurrentUserId === userId) return;
   fetch(`/admin/chat/${userId}/rooms`, {
      method: 'GET',
      headers: {
         'Accept': 'application/json',
         'Content-Type': 'application/json',
         'X-Requested-With': 'XMLHttpRequest',
         'X-CSRF-TOKEN': window.config.CSRFToken
      }
   }).then(response => response.json()).then(data => {
      // console.log(data);
      chatCurrentUserId = userId;
      chatCurrentUserName = userName;
      fillRooms(data);
   }).catch((error) => {
      console.error('Error:', error);
   });
}

const showDialog = (roomId) => {
   if (chatCurrentRoomId === roomId) return;

   fetch(`/admin/chat/${roomId}?marked=true`, {
      method: 'GET',
      headers: {
         'Accept': 'application/json',
         'Content-Type': 'application/json',
         'X-Requested-With': 'XMLHttpRequest',
         'X-CSRF-TOKEN': window.config.CSRFToken
      }
   }).then(response => response.json()).then(data => {
      // console.log(data);
      cleanState();
      closeBroadcasting(chatCurrentRoomId);
      chatCurrentRoomId = data.room.id;
      chatCurrentRoom = data.room;
      setBlockActive(chatCurrentRoomId);
      fillDialog(data);
      fillMarkedMessagesList(data, chatMarkedMessagesHTMLElementId);
      connectToBroadcasting(chatCurrentRoomId);
   }).catch((error) => {
      console.error(error);
   });
}

const blinkMessage = (messageId) => {
   const message = document.getElementById('chat-message-' + messageId);
   if (!message) return;
   message.classList.add('highlight');
   setTimeout(() => message.classList.remove('highlight'), 800);
}

const goToLink = (linkId) => {
   const link = document.getElementById('chat-message-' + linkId);
   if (!link || !chatWindow) return;

   let elementOffset = link.offsetTop;
   const offset = elementOffset + (link.clientHeight / 2) - chatWindow.clientHeight + (chatWindow.clientHeight / 2);
   chatWindow.scrollTo({top: offset, behavior: 'smooth'});

   let timeout = null;
   const endScroll = () => {
      blinkMessage(linkId);
      chatWindow.removeEventListener('scroll', checkScroll);
      clearTimeout(timeout);
      chatIsScrolling = false;
      chatWindow.removeEventListener('scroll', checkScroll);
   }
   const checkScroll = () => {
      if(timeout !== null) {
         clearTimeout(timeout);
      }
      timeout = setTimeout(function() {
         endScroll();
      }, 150);
   }

   if (!chatIsScrolling) {
      chatWindow.addEventListener('scroll', checkScroll);
      chatIsScrolling = true;
      timeout = setTimeout(function() {
         endScroll();
      }, 150);
   }
}

const setState = (state) => {
   const newMessageSubmitBtn = document.getElementById('new-message-submit-btn');
   switch (state) {
      case 'loading':
         chatIsLoading = true;
         break;
      case 'submitting':
         chatIsSubmitting = true;
         if (newMessageSubmitBtn) {
            newMessageSubmitBtn.classList.remove('fa-arrow-circle-right');
            newMessageSubmitBtn.classList.add('fa-circle-notch', 'fa-spin');
         }
         break;
      default:
         chatIsSubmitting = false;
         chatIsLoading = false;
         if (newMessageSubmitBtn) {
            newMessageSubmitBtn.classList.add('fa-arrow-circle-right');
            newMessageSubmitBtn.classList.remove('fa-circle-notch', 'fa-spin');
         }
         break;
   }
}

const pushMessage = (message, right = '') => {
   // console.log(message);
   chatCurrentMessages.push(message);
   chatWindow.insertAdjacentHTML('beforeend', getChatMessageTemplate(message, right));
   scrollBottom();
}

const sendNewMessage = () => {
   const value = document.getElementById('new-message').innerHTML;
   if (!value || chatIsSubmitting) return;
   setState('submitting');
   const body = {
      room_id: chatCurrentRoomId,
      subject_id: chatCurrentRoom.subject_id,
      subject_type: chatCurrentRoom.subject_type,
      user_id: chatAuthUserId,
      text: value,
      status: 1
   };
   if (chatLinkId) {
      body.link = chatLinkId;
   }
   // console.log(body, window.config.CSRFToken);
   fetch('/admin/messages', {
      method: 'POST',
      headers: {
         'Accept': 'application/json',
         'Content-Type': 'application/json',
         'X-Requested-With': 'XMLHttpRequest',
         'X-CSRF-TOKEN': window.config.CSRFToken,
         'X-Socket-Id': Echo.socketId()
      },
      body: JSON.stringify(body)
   })
      .then(response => response.json())
      .then(data => {
         setState();
         cleanState(true);
         pushMessage(data, 'right');
      })
      .catch((error) => {
         setState();
         console.error(error);
      });
}

const unshiftMessages = (messages) => {
   // console.log('shift');
   if (messages instanceof Array) {
      if (messages.length > 0) {
         chatCurrentMessages = [...messages, ...chatCurrentMessages];
         messages.reverse().forEach(message => {
            chatWindow.insertAdjacentHTML('afterbegin', getChatMessageTemplate(message, message.user_id === chatAuthUserId ? 'right' : ''));
         })
      } else {
         chatNoMoreMessages = true;
      }
   }
}

const shiftMessage = (id) => {
   document.getElementById('chat-message-'+id)?.remove();
   document.getElementById('chat-marked-message-'+id)?.remove();
}

const loadMoreMessages = () => {
   if (chatIsLoading) return;
   setState('loading');
   // console.log('load more');
   fetch(`/admin/chat-index?page=${chatPageNumber + 1}&room_id=${chatCurrentRoomId}`, {
      method: 'GET',
      headers: {
         'Accept': 'application/json',
         'Content-Type': 'application/json',
         'X-Requested-With': 'XMLHttpRequest',
         'X-CSRF-TOKEN': window.config.CSRFToken
      }
   })
      .then(response => response.json())
      .then(data => {
         // console.log(data);
         setState();
         unshiftMessages(data.messages);
         chatPageNumber += 1;
      })
      .catch((error) => {
         setState();
         console.error(error);
      });
}

const removeMessage = (event) => {
   const messageId = event.currentTarget.getAttribute('data-message');
   console.log('Remove message', messageId);
   if (!messageId) return;
   if (confirm('Confirm this action?')) {
      fetch(`/admin/messages/${messageId}`, {
         method: 'DELETE',
         headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.config.CSRFToken
         }
      })
         .then(response => response.json())
         .then(data => {
            // console.log(data);
            shiftMessage(messageId);
         })
         .catch((error) => {
            console.error(error);
         });
   }
}

const checkIfScrolledToTop = () => {
   closeContextMenu();
   if (chatWindow.scrollTop < 500 && !chatNoMoreMessages) {
      loadMoreMessages();
   }
}

const markMessage = (messageId, forcedStatus = -1) => {
   const messageIdx = chatCurrentMessages.findIndex(el => el.id === messageId);
   if (messageIdx === -1 && forcedStatus === -1) return;
   const status = (forcedStatus === 1 || forcedStatus === 2) ? forcedStatus : (+chatCurrentMessages[messageIdx].status === 2 ? 1 : 2);
   fetch(`/admin/messages/${messageId}`, {
      method: 'PUT',
      headers: {
         'Accept': 'application/json',
         'Content-Type': 'application/json',
         'X-Requested-With': 'XMLHttpRequest',
         'X-CSRF-TOKEN': window.config.CSRFToken
      },
      body: JSON.stringify({status})
   })
      .then(response => response.json())
      .then(data => {
         // console.log(data);
         if (+data.status === 2) {
            onMarkingMessage(data, 'mark', true);
         } else {
            onMarkingMessage(data, 'unmark', true);
         }
      })
      .catch((error) => {
         console.log(error);
         Toastify({
            text: error,
            backgroundColor: '#BD362F'
         }).showToast();
      });
}

/* mark | unmark */
const onMarkingMessage = (message, action = 'mark', shouldWhisper = false) => {
   const foundMessage = chatCurrentMessages.find(el => el.id === message.id);
   if (foundMessage) {
      foundMessage.status = +message.status;
   }
   if (action === 'mark') {
      document.getElementById('chat-message-'+message.id)?.classList.add('marked');
      pushMarkedMessage(message);
   } else if (action === 'unmark') {
      document.getElementById('chat-message-'+message.id)?.classList.remove('marked');
      shiftMarkedMessage(message);
   }
   // console.log(chatMarkedMessages);
   if (shouldWhisper) {
      if (!chatCurrentPrivateChannel) return;
      chatCurrentPrivateChannel.whisper('mark-message', {message, action});
   }
}

const pushMarkedMessage = (message) => {
   // console.log(message);
   chatMarkedMessages.push(message);
   if (!chatMarkedMessagesHTMLElement) return;
   if (chatMarkedMessages.length === 1) {
      chatMarkedMessagesHTMLElement.innerHTML = '';
      chatMarkedMessagesHTMLElement.classList.add('inserted');
   }
   chatMarkedMessagesHTMLElement.insertAdjacentHTML('afterbegin',
      getChatMarkedMessageTemplate(message, message.user_id === chatAuthUserId ? 'right' : ''));
}

const shiftMarkedMessage = (message) => {
   // console.log(message);
   chatMarkedMessages = chatMarkedMessages.filter(el => el.id !== message.id);
   if (!chatMarkedMessagesHTMLElement || !document.getElementById(`chat-marked-message-${message.id}`)) return;
   document.getElementById(`chat-marked-message-${message.id}`).remove();
   if (chatMarkedMessages.length < 1) {
      chatMarkedMessagesHTMLElement.innerHTML = '<p>There are no selected messages yet</p>';
      chatMarkedMessagesHTMLElement.classList.remove('inserted');
   }
}

window.onload = () => {
   console.log('loaded');
   onChatInit();
};
window.setChatVars = setChatVars;
window.onChatInit = onChatInit;
window.showRooms = showRooms;
window.showDialog = showDialog;
window.onTyping = onTyping;
window.sendNewMessage = sendNewMessage;
window.removeMessage = removeMessage;
window.openContextMenu = openContextMenu;
window.goToLink = goToLink;
window.closeLink = closeLink;
window.setLink = setLink;
window.markMessage = markMessage;
