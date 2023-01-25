<select name="subject_type" id="subject_type" onchange="getItems(this.value)" class="w-25 mr-4">
   <option value=""></option>
   <option value="App\Course" {{isset($item) ? ($item->subject_type === 'App\Course' ? 'selected="selected"' : '') : ''}}>Video Course</option>
   <option value="App\Topic" {{isset($item) ? ($item->subject_type === 'App\Topic' ? 'selected="selected"' : ''): ''}}>Video Topic</option>
   <option value="App\Event" {{isset($item) ? ($item->subject_type === 'App\Event' ? 'selected="selected"' : ''): ''}}>Live Course</option>
</select>
<select name="subject_id" id="subject_id" class="w-auto d-none"></select>
<script>
   let subjectIdSelect;
   const fillSubjectIds = (items, selectedId = null) => {
      let options = '';
      if (items instanceof Array) {
         items.forEach(item => {
            options += `<option value="${item.id}" ${item.id === selectedId ? 'selected="selected"' : ''}>${item.title}</option>`;
         });
      } else if (items instanceof Object) {
         for (const [key, subitems] of Object.entries(items)) {
            options += `<optgroup label="${key}">`;
            subitems.forEach(subitem => {
               if (!(subitem instanceof Array)) {
                  options += `<option value="${subitem.id}" ${subitem.id === selectedId ? 'selected="selected"' : ''}>${subitem.title}</option>`;
               }
            });
            options += '</optgroup>';
         }
      }
      subjectIdSelect.innerHTML = options;
   }
   const getItems = (type, selectedId = null) => {
      console.log('getItems', type, selectedId);
      if (!type) {
         subjectIdSelect.classList.add('d-none');
         return;
      }
      $.ajax({
         type:'GET',
         url:`/admin/promocodes-get-items/${type}`,
         success: function(data) {
            console.log(data);
            fillSubjectIds(data, selectedId);
            subjectIdSelect.classList.remove('d-none');
         },
         error: function(error) {
            console.log(error);
         }
      });
   }

   let promocodeSubjectCounter = 0;
   const interval = setInterval(() => {
      subjectIdSelect = document.getElementById('subject_id');
      if (subjectIdSelect) {
         getItems("{{isset($item) ? json_encode($item->subject_type, JSON_HEX_QUOT) : ''}}".replaceAll('&quot;', ''), {{isset($item) ? $item->subject_id : null}});
         clearInterval(interval);
      }
      if (promocodeSubjectCounter > 2000) {
         clearInterval(interval);
      }
      promocodeSubjectCounter += 200;
   }, 200);

</script>
