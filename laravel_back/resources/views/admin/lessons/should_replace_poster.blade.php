<div class="form-group">
   <div class="custom-control custom-checkbox mt-2 mb-3 d-inline-block" onchange="toggleReadonly()">
      <input type="checkbox" class="custom-control-input" name="should_replace_poster" id="should_replace_poster">
      <label class="custom-control-label cursor-pointer" for="should_replace_poster" title="If this option is checked the poster file will be stored(replaced) with uploaded main video frame"
      >Save(replace) poster with uploaded video frame*?</label>
   </div>
   <span class="ml-3">Specify video frame second if you want (15th second by default): </span>
   <input id="video_frame_second" type="number" min="0" step="1" name="video_frame_second" readonly class="w-auto">
</div>

<script>
   const toggleReadonly = () => {
      document.getElementById('video_frame_second').toggleAttribute('readonly');
   }
</script>
