<div class="form-group">
   <label for="type" class="mb-2">Type</label>
   <select name="type" id="type" onchange="hidePosterInput(this.value)" {{isset($item) ? 'disabled' : null}}>
      <option value="image" {{isset($item) ? ($item->type === 'image' ? 'selected' : null) : null }}>Image</option>
      <option value="video" {{isset($item) ? ($item->type === 'video' ? 'selected' : null) : null }}>Video</option>
   </select>
</div>
<script>
   const hidePosterInput = (value) => {
      console.log(value);
      if (value === 'image') {
         document.querySelector('[name="url"]').setAttribute('accept', 'image/jpeg,image/png');
         document.querySelector('[name="poster_url"]').parentElement.parentElement.classList.add('d-none');
         document.querySelector('[name="should_replace_poster"]').parentElement.parentElement.classList.add('d-none');
      } else {
         document.querySelector('[name="url"]').setAttribute('accept', 'video/mp4');
         document.querySelector('[name="poster_url"]').parentElement.parentElement.classList.remove('d-none');
         document.querySelector('[name="should_replace_poster"]').parentElement.parentElement.classList.remove('d-none');
      }
   }
   document.addEventListener('DOMContentLoaded', () => {
      hidePosterInput(document.getElementById('type').value);
   });
</script>
