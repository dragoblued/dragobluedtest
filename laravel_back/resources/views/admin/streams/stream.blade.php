@extends('layouts.admin')
@section('link')
   <link rel="stylesheet" href="{{ asset('css/inc/stream.css') }}">
@endsection
@section('content')
   <div class="stream-wrap">
      <div class="text-right mt-4">
         <div class="stream-btn-group m-1">
            <span class="stream-btn-group__label">Sharing</span>
            <button class="btn btn-dark m-1 fz-0_8rem btn-sm active" onclick="share('fake')">Fake Stream</button>
            <button class="btn btn-dark m-1 fz-0_8rem btn-sm active" onclick="share('webcam')">Webcam</button>
            <button class="btn btn-dark m-1 fz-0_8rem btn-sm" onclick="share('screen')">Screen</button>
         </div>
      </div>
      <div class="stream mb-4">
         <div class="stream__video-wrap bg-dark w-100 text-center">
            <video id="stream-video" controls muted="muted" class="stream__video"></video>
         </div>
         {{--      <video id="user-video" controls></video>--}}
      </div>
      <div class="text-right">
         <div class="stream-btn-group m-1">
            <span class="stream-btn-group__label">Recording</span>
{{--            <span id="recording-file-size"></span>--}}
            <button class="btn btn-danger m-1" onclick="startRecording()"><i class="mr-2 fa fa-circle"></i>Start</button>
            <button class="btn btn-danger m-1" onclick="stopAndSaveRecording()"><i class="mr-2 fa fa-save"></i>Stop and Save</button>
         </div>
         <div class="stream-btn-group m-1">
            <span class="stream-btn-group__label">Broadcasting</span>
            <button class="btn btn-danger m-1" onclick="broadcast()"><i class="mr-2 fa fa-broadcast-tower"></i>Start</button>
            <button class="btn btn-danger m-1" onclick="pauseBroadcasting()"><i class="mr-2 fa fa-pause"></i>Pause</button>
            <button class="btn btn-danger m-1" onclick="closeBroadcasting()"><i class="mr-2 fa fa-times"></i>Close</button>
         </div>
      </div>
      <div class="container participants my-4">
         <h4 class="font-weight-bold">Participants</h4>
         <div id="stream-admin-participants" class="participants__members py-2">
            <p>No one joined yet</p>
         </div>
      </div>
      <div class="container selected-messages my-4 narrow-srollbar">
         <h4 class="font-weight-bold">Selected Questions</h4>
         <div id="stream-admin-selected-messages" class="selected-messages__main chat-dialog__main">
            <p>There are no selected messages yet</p>
         </div>
      </div>
      <div class="container live-chat my-4">
         <h4 class="font-weight-bold">Live Chat</h4>

         <div id="stream-admin-live-chat" class="live-chat__body py-2">
            @include('admin.inc.chat', [
               'displayChatDialog' => true,
               'displayChatDialogFooter' => true,
               'chatClassName' => 'white-skin'
            ])
            @include('admin.inc.modal-dialog')
         </div>
      </div>
      <div id="stream-statuses" class="stream-statuses"></div>
   </div>
@endsection
@section('js')
   <script src="{{ asset('js/inc/stream.js') }}"></script>
{{--   <script src="https://www.WebRTC-Experiment.com/RecordRTC.js"></script>--}}
   <script src="{{ asset('js/inc/record-rtc-handler.js') }}"></script>
   <script src="{{ asset('js/inc/show-user-info.js') }}"></script>
   <script>
      /* Stream */
      const fastUpdateUrl = window.config.appUrl+'/admin/streams-fast-update/';
      const videoStream = getVideoStream('{{$item->name}}', '{{$item->key}}', {{$item->id}},
         window.user, fastUpdateUrl, 'stream-admin-participants');
      const broadcast = () => {
         videoStream.startBroadcasting(() => {
            console.log('start');
            if (!videoStream.isBroadcasting && document.getElementById('stream-status-broadcasting')) {
               document.getElementById('stream-status-broadcasting').classList.remove('animation-blinking');
               document.getElementById('stream-status-broadcasting').innerHTML = '<i class="fa fa-broadcast-tower mr-2"></i>Broadcasting';
            } else {
               document.getElementById('stream-statuses').insertAdjacentHTML('afterbegin',
                  '<div id="stream-status-broadcasting" class="stream-statuses__item bg-danger" onclick="this.remove()"><i class="fa fa-broadcast-tower mr-2"></i>Broadcasting</div>');
               setTimeout(() => {
                  document.getElementById('stream-status-broadcasting')?.classList.add('on');
               }, 500);
            }
         });
      }
      const pauseBroadcasting = () => {
         videoStream.pauseBroadcasting(() => {
            if (document.getElementById('stream-status-broadcasting')) {
               document.getElementById('stream-status-broadcasting').innerHTML = '<i class="fa fa-pause mr-2"></i>Broadcasting';
               document.getElementById('stream-status-broadcasting').classList.add('animation-blinking');
            }
         });
      }
      const closeBroadcasting = () => {
         videoStream.closeBroadcasting(() => {
            document.getElementById('stream-status-broadcasting')?.remove();
         });
         // setTimeout(() => {
         //    window.location.href = window.config.appUrl+'/admin/streams';
         // }, 1500);
      }
      const share = (src) => {
         /* webcam | fake | screen */
         videoStream.setStream(src);
      }

      /* Recording stream */
      const recordHandler = getRecordRTCHandler('{{$item->name}}');

      const startRecording = () => {
         recordHandler.setRecorder(videoStream.myMediaStream);
         recordHandler.startRecording(() => {
            document.getElementById('stream-statuses').insertAdjacentHTML('afterbegin',
               '<div id="stream-status-recording" class="stream-statuses__item bg-danger" onclick="this.remove()"><i class="fa fa-circle mr-2"></i>Recording</div>');
            setTimeout(() => {
               document.getElementById('stream-status-recording')?.classList.add('on');
            }, 500);
         });
      }

      const stopAndSaveRecording = () => {
         recordHandler.stopAndSaveRecording(() => {
            document.getElementById('stream-status-recording')?.remove();
         });
      }

      /* Chat */
      onChatInit();
      setChatVars(window.user.id, window.user.name ?? window.user.email, false, false, 'stream-admin-selected-messages');
      showDialog({{$item->room->id}});

      setTimeout(() => {
         window.scrollTo({top: 0});
      }, 500);
   </script>
@endsection
