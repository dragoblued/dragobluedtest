import MediaHandler from "../mediaHandler";
import Peer from 'peerjs';
import Toastify from 'toastify-js';

export default class VideoStream {

   user;
   fastUpdateUrl;
   streamName;
   streamKey;
   streamId;
   streamPresenceChannel;
   mediaHandler;
   videoEl;
   userVideoEl;
   myPeer;
   myPeerId;
   peers = {};
   myMediaStream = null;
   curSharingSrc = null;

   participants = new Map();
   participantsHTMLElement;
   participantsHTMLElementId;

   isBroadcasting = false;

   constructor(streamName, streamKey, streamId, user, fastUpdateUrl, participantsHTMLElementId) {
      this.streamName = streamName;
      this.streamKey = streamKey;
      this.streamId = streamId;
      this.user = user;
      this.fastUpdateUrl = fastUpdateUrl;
      this.participantsHTMLElementId = participantsHTMLElementId;
      this.mediaHandler = new MediaHandler();
      this.#onInit();
   }

   #onInit() {
      window.onload = () => {
         this.getVideo();
         this.getParticipantsHTMLElement();
         // this.startMyPeer();
         this.setStream();
      }
   }

   getVideo() {
      this.videoEl = document.getElementById('stream-video');
      // this.userVideoEl = document.getElementById('user-video');
   }

   getParticipantsHTMLElement() {
      this.participantsHTMLElement = document.getElementById(this.participantsHTMLElementId);
   }

   handleStream = (stream) => {
      this.myMediaStream = stream;
      try {
         this.videoEl.srcObject = stream;
      } catch (error) {
         this.videoEl.src = URL.createObjectURL(stream);
      }
      this.videoEl.onloadedmetadata = () => this.videoEl.play();
      this.replaceTracksIfAlreadyBroadcasting();
   }

   /* webcam | fake | screen */
   setStream(src = 'webcam') {
      if (!this.videoEl) return;
      if (this.curSharingSrc === src) {
         Toastify({
            text: 'This source is sharing now.',
            backgroundColor: "#2F96B4"
         }).showToast();
         return;
      }
      switch (src) {
         case 'webcam':
            this.mediaHandler.getStream()
               .then((stream) => {
                  this.handleStream(stream);
                  this.curSharingSrc = 'webcam';
               })
               .catch(error => {
                  if (confirm(error + '\nUse a fake stream instead?')) {
                     this.setStream('fake');
                  } else {
                     this.curSharingSrc = null;
                  }
               });
            break;
         case 'fake':
            this.mediaHandler.getFakeStream()
               .then((stream) => {
                  this.handleStream(stream);
                  this.curSharingSrc = 'fake';
               })
               .catch(error => {
                  alert(error);
                  this.curSharingSrc = null;
               });
            break;
         case 'screen':
            this.mediaHandler.getScreenStream()
               .then((stream) => {
                  this.handleStream(stream);
                  this.curSharingSrc = 'screen';
               })
               .catch(error => {
                  if (confirm(error + '\nUse a fake stream instead?')) {
                     this.setStream('fake');
                  } else {
                     this.curSharingSrc = null;
                  }
               });
            break;
         default:
            this.curSharingSrc = null;
            break;
      }
   }

   replaceTracksIfAlreadyBroadcasting() {
      if (!this.isBroadcasting) return;
      for (const [peerId, peer] of Object.entries(this.peers)) {
         for(const sender of peer.peerConnection.getSenders()){
            if(sender.track.kind === 'audio') {
               if(this.myMediaStream.getAudioTracks().length > 0){
                  sender.replaceTrack(this.myMediaStream.getAudioTracks()[0]);
               }
            }
            if(sender.track.kind === 'video') {
               if(this.myMediaStream.getVideoTracks().length > 0){
                  sender.replaceTrack(this.myMediaStream.getVideoTracks()[0]);
               }
            }
         }
      }
   }

   connectToNewUser = async (userId, stream) => {
      console.log('CONNECT TO NEW USER', userId, stream);
      if (this.streamPresenceChannel && userId && stream) {
         if (this.peers.hasOwnProperty(userId)) {
            console.log('THIS USER ALREADY CONNECTED', userId);
            return;
         }
         await this.myPeer.connect(userId);
         const newPeer = await this.myPeer.call(userId, stream);

         newPeer.on('close', () => {
            this.closePeer(userId);
         });
         this.peers[userId] = newPeer;
         console.log('PEERS', this.peers);

         // newPeer.on('stream', (stream) => {
         //    try  {
         //       this.userVideoEl.srcObject = stream;
         //    } catch (error) {
         //       this.userVideoEl.src = URL.createObjectURL(stream);
         //    }
         //    this.userVideoEl.onloadedmetadata = () => this.userVideoEl.play();
         // });

      }
   }

   closePeer = (peerId) => {
      const closingPeer = this.peers[peerId];
      console.log('ON CLOSE PEER', peerId, closingPeer);
      if (closingPeer?.peerConnection) {
         closingPeer.peerConnection.close();
      }
      delete this.peers[peerId];
      console.log('PEERS', this.peers);
   }

   async startMyPeer() {
      const peerConfig = {
         key: 'peerjs',
         host: 'admin.algirdaspuisys.com',
         port: 443,
         // host: '127.0.0.1',
         // port: 9000,
         path: '/peer',
         secure: true
      };
      return new Promise((resolve) => {
         /* Нэйминг должен совпадать с angular -> src/app/site/page-stream/page-stream.component.ts
          * -> func startMyPeer() -> new Peer()
         * */
         this.myPeer = new Peer(this.streamName+'-stream-user-'+this.user.id, peerConfig);
         this.myPeer.on('open', id => {
            this.myPeerId = id;
            resolve();
            console.log('MY PEER ON OPEN', id);
         });
         this.myPeer.on('call', (call) => {
            console.log('CALL', call);
            call.answer(this.myMediaStream);
            // call.on('stream', userVideoStream => {
            //    try  {
            //       this.userVideoEl.srcObject = userVideoStream;
            //    } catch (error) {
            //       this.userVideoEl.src = URL.createObjectURL(userVideoStream);
            //    }
            //    this.userVideoEl.onloadedmetadata = () => this.userVideoEl.play();
            //    console.log('MY PEER -> CALL -> STREAM', userVideoStream);
            // });
         });
      });
   }

   startBroadcasting = async (callback = () => {}) => {
      console.log('startBroadcasting');
      if (!this.myMediaStream) {
         Toastify({
            text: 'Unable start broadcasting. Stream is not set.',
            backgroundColor: "#F89406",
            duration: 100000
         }).showToast();
         return;
      }
      if (this.isBroadcasting === true) {
         Toastify({
            text: 'Already broadcasting',
            backgroundColor: "#2F96B4"
         }).showToast();
         return;
      }
      if (!this.myPeer) {
         await this.startMyPeer();
      }
      if (!this.streamPresenceChannel) {
         this.connectPresenceChannel();
      } else {
         this.streamPresenceChannel.whisper('broadcasting', this.myPeerId);
      }
      this.updateStatus(this.fastUpdateUrl, 1);
      callback();
      this.isBroadcasting = true;
   }

   connectPresenceChannel() {
      this.streamPresenceChannel = window.Echo.join(`stream.${this.streamName}.${this.streamKey}`);
      this.streamPresenceChannel
         .here((members) => {
            console.log('HERE', this.myPeerId);
            console.table(members);
            if (this.myPeerId) {
               this.streamPresenceChannel.whisper('broadcasting', this.myPeerId);
            }
            Toastify({
               text: 'Broadcasting is started',
               backgroundColor: "#2F96B4"
            }).showToast();
            this.fillParticipants(members);
         })
         .joining((joiningMember) => {
            console.log('JOINING', joiningMember);
            Toastify({
               text: (joiningMember.name ? joiningMember.name : joiningMember.email) + ' has joined',
               backgroundColor: "#2F96B4"
            }).showToast();
            this.addParticipant(joiningMember);
            if (this.myPeerId) {
               this.streamPresenceChannel.whisper('broadcasting', this.myPeerId);
            }
         })
         .leaving((leavingMember) => {
            console.log('LEAVING', leavingMember);
            Toastify({
               text: (leavingMember.name ? leavingMember.name : leavingMember.email) + ' has left',
               backgroundColor: "#2F96B4"
            }).showToast();
            this.removeParticipant(leavingMember);
            if (this.myPeerId) {
               /* Нэйминг должен совпадать с src/app/site/page-stream/page-stream.component.ts
                * -> func startMyPeer() -> new Peer()
               * */
               this.closePeer(`${this.streamName}-stream-user-${leavingMember.id}`);
            }
         })
         // .listenForWhisper('broadcasting',(event) => {
         //    console.log('BROADCSTING', event);
         //    this.connectToNewUser(event, this.myMediaStream)
         // })
         .listenForWhisper('user-connected',(event) => {
            console.log('WHISPER CONNECTED', event);
            this.connectToNewUser(event, this.myMediaStream);
         });
   }

   closeAllConnections = () => {
      for (const peerId of Object.keys(this.peers)) {
         this.closePeer(peerId);
      }
   }

   pauseBroadcasting = (callback = () => {}) => {
      console.log('closeBroadcasting');
      if (this.streamPresenceChannel && this.myMediaStream) {
         this.closeAllConnections();
         this.streamPresenceChannel.whisper('broadcasting-paused', this.myPeerId);
         // window.Echo.leave(`stream.${this.streamName}.${this.streamKey}`);
         // this.streamPresenceChannel = null;
         this.updateStatus(this.fastUpdateUrl, 2);
         Toastify({
            text: 'Broadcasting is paused',
            backgroundColor: "#2F96B4"
         }).showToast();
         callback();
         this.isBroadcasting = false;
      }
   }

   closeBroadcasting = (callback = () => {}) => {
      console.log('closeBroadcasting');
      if (this.streamPresenceChannel && this.myMediaStream) {
         this.closeAllConnections();
         this.streamPresenceChannel.whisper('broadcasting-closed', this.myPeerId);
         window.Echo.leave(`stream.${this.streamName}.${this.streamKey}`);
         this.streamPresenceChannel = null;
         this.updateStatus(this.fastUpdateUrl, 3);
         Toastify({
            text: 'Broadcasting is closed',
            backgroundColor: "#2F96B4"
         }).showToast();
         callback();
         this.isBroadcasting = false;
      }
   }

   getParticipantTemplate(participant) {
      if (!participant) return '';
      // console.log(participant);
      return `<div class="participant mr-3 my-1" id="stream-participant-${participant.id}" title="${participant.email}">
                <img class="participant__avatar bdrs-50p mr-2" height="20" width="20"
                    src="/${participant.avatar_url ?? 'media/img/avatar-default_preload.jpg'}" alt="Participant Avatar">
                <span class="participant__name">${participant.name ? participant.name : participant.email}</span>
            </div>`;
   }

   fillParticipants(members) {
      if (members instanceof Array) {
         members.forEach(member => {
            this.participants.set(member.id, member);
         });
         this.fillParticipantsHTMLElement();
      }
   }

   fillParticipantsHTMLElement() {
      let result = '';
      this.participants.forEach(participant => {
         result += this.getParticipantTemplate(participant);
      });
      this.participantsHTMLElement.innerHTML = result;
   }

   addParticipant(participant) {
      if (!participant) return;
      this.participants.set(participant.id, participant);
      this.addParticipantHTMLElement(participant);
   }

   addParticipantHTMLElement(participant) {
      if (!document.getElementById(`stream-participant-${participant.id}`)) {
         this.participantsHTMLElement.insertAdjacentHTML('afterbegin', this.getParticipantTemplate(participant));
      }
   }

   removeParticipant(participant) {
      if (!participant) return;
      this.participants.delete(participant.id);
      this.removeParticipantHTMLElement(participant);
   }

   removeParticipantHTMLElement(participant) {
      document.getElementById(`stream-participant-${participant.id}`)?.remove();
   }

   /* 0 - pending
      1 - broadcasting
      2 - broadcasting paused
      3 - broadcasting ended
      4 - broadcasting errored
   */
   updateStatus(updateUrl, statusCode) {
      const body = {
         broadcaster_id: this.user.id,
         status: statusCode
      };
      // if (statusCode === 1) {
      //    body['start_at'] = moment('YYYY-MM-DD HH:mm');
      // } else if (statusCode === 3) {
      //    body['end_at'] = moment('YYYY-MM-DD HH:mm');
      // }
      // console.log(body);
      fetch(updateUrl + this.streamId, {
         method: 'PUT',
         headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.config.CSRFToken,
            'X-Socket-Id': window.Echo.socketId()
         },
         body: JSON.stringify(body)
      })
         .then(response => response.json())
         .then(data => {
            console.log(data);
         })
         .catch((error) => {
            console.error(error);
         });
   }
}

window.getVideoStream = (streamName, streamKey, streamId, user, fastUpdateUrl, participantsHTMLElementId) => {
   return new VideoStream(streamName, streamKey, streamId, user, fastUpdateUrl, participantsHTMLElementId);
}
