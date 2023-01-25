export default class MediaHandler {
   createEmptyAudioTrack = () => {
      const ctx = new AudioContext();
      const oscillator = ctx.createOscillator();
      const dst = oscillator.connect(ctx.createMediaStreamDestination());
      oscillator.start();
      const track = dst.stream.getAudioTracks()[0];
      return Object.assign(track, { enabled: false });
   }

   createEmptyVideoTrack = ({ width, height }) => {
      const canvas = Object.assign(document.createElement('canvas'), { width, height });
      canvas.getContext('2d').fillRect(0, 0, width, height);

      const stream = canvas.captureStream();
      const track = stream.getVideoTracks()[0];

      return Object.assign(track, { enabled: false });
   };

   async getFakeStream() {
      return new Promise((resolve, reject) => {
         resolve(new MediaStream([this.createEmptyAudioTrack(), this.createEmptyVideoTrack({ width:640, height:480 })]));
      });
   }

   async getStream() {
      return new Promise((resolve, reject) => {
         navigator.mediaDevices.getUserMedia({
            video: true,
            audio: {
               autoGainControl: false,
               channelCount: 1,
               echoCancellation: true,
               latency: 0,
               noiseSuppression: true,
               sampleRate: 44100,
               volume: 1.0
            }
         })
            .then((stream) => {
               resolve(stream);
            })
            .catch((error) => {
               reject('Unable to fetch a Webcam. ' + error);
            });
      });
   }

   async getScreenStream() {
      return new Promise((resolve, reject) => {
         navigator.mediaDevices.getDisplayMedia({
            video: {
               cursor: 'always'
            },
            audio: {
               autoGainControl: false,
               channelCount: 1,
               echoCancellation: true,
               latency: 0,
               noiseSuppression: true,
               sampleRate: 44100,
               volume: 0.8
            }
         })
            .then((stream) => {
               resolve(stream);
            })
            .catch((error) => {
               reject('Unable to fetch a Screen. ' + error);
            });
      });
   }
}
