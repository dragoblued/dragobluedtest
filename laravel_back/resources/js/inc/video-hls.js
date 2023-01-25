const setHlsVideos = (className) => {
   if (Hls.isSupported()) {
      const hlsjsConfig = {
         // autoStartLoad: false,
         xhrSetup: xhr => {
            xhr.setRequestHeader('Accept', 'application/json'),
            xhr.setRequestHeader('Content-Type', 'application/json'),
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'),
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))
         }
      };

      Array.from(document.getElementsByClassName(className)).forEach(videoEl => {
         if (videoEl.getAttribute('data-src')) {
            const hls = new Hls(hlsjsConfig);
            hls.loadSource(videoEl.getAttribute('data-src'));
            hls.attachMedia(videoEl);
            // const startLoading = () => {
            //    console.log('start loading');
            //    hls.startLoad();
            //    videoEl.removeEventListener('click', startLoading);
            // }
            // videoEl.addEventListener('click', startLoading);
            // setTimeout(() => {
            //    hls.startLoad();
            // }, 2000);
            // hls.on(Hls.Events.MANIFEST_PARSED, function() {
            //    console.log('here');
            //    hls.stopLoad();
            // });
         }
      });
   } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
      Array.from(document.getElementsByClassName(className)).forEach(videoEl => {
         videoEl.src = videoEl.getAttribute('data-src');
      });
   }
}
window.setHlsVideos = setHlsVideos;
