import {MRecordRTC, bytesToSize, invokeSaveAsDialog} from 'recordrtc';
import Toastify from 'toastify-js';

export default class RecordRTCHandler {

   fileName = 'stream';
   recorder;
   recorderBlob = null;
   /* none | started | stopped */
   recordingStatus = 'none';

   constructor(fileName) {
      this.fileName = fileName;
   }


   // getFileSize() {
   //    console.log(this.recorderBlob);
   //    if (!this.recorderBlob) return null;
   //    return bytesToSize(this.recorderBlob.size);
   // }

   setRecorder(stream) {
      if (this.recorder) return;
      if (!stream) {
         Toastify({
            text: 'Stream is not set. Choose sharing source first',
            backgroundColor: '#F89406'
         }).showToast();
         return;
      }
      this.recorder = new MRecordRTC();
      this.recorder.addStream(stream);
      this.recorder.mediaType = {
         audio: true,
         video: true
      };
      this.recorder.mimeType = {
         audio: 'audio/wav',
         video: 'video/webm;codecs=vp8'
      };
   }

   startRecording(callback = () => {}) {
      if (!this.recorder) return;
      if (this.recordingStatus === 'started') {
         Toastify({
            text: 'Already recording',
            backgroundColor: '#2F96B4'
         }).showToast();
      } else {
         this.recorder.startRecording();
         this.recordingStatus = 'started';
         callback();
      }
   }

   stopAndSaveRecording(callback = () => {}) {
      if (!this.recorder) return;
      if (this.recordingStatus === 'stopped') {
         Toastify({
            text: 'Already stopped',
            backgroundColor: '#2F96B4'
         }).showToast();
      } else {
         this.recorder.stopRecording((url, type) => {
            console.log(url, type);
            this.saveRecording();
         });
         this.recordingStatus = 'stopped';
         callback();
      }
   }

   saveRecording() {
      console.log(this.recorder.getBlob());
      if (!this.recorder) return;
      // getSeekableBlob(blob, function(seekableBlob) {
      //    invokeSaveAsDialog(seekableBlob, 'stream.webm');
      // });
      this.saveToServer(this.recorder.getBlob());
      this.recorder.save();
      // invokeSaveAsDialog(this.recorder.getBlob(), this.fileName + '.webm');
   }

   saveToServer(blob) {
      // save blob to stream directory
   }
}

window.getRecordRTCHandler = (fileName) => {
   return new RecordRTCHandler(fileName);
}
