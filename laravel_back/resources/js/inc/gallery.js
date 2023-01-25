// Array.prototype.move1 = function(from, to) {
//    let i, tmp;
//    from = parseInt(from, 10);
//    to = parseInt(to, 10);
//    if (from !== to && 0 <= from && from <= this.length && 0 <= to && to <= this.length) {
//       tmp = this[from];
//       if (from < to) {
//          for (i = from; i < to; i++) {
//             this[i] = this[i + 1];
//          }
//       }
//       else {
//          for (i = from; i > to; i--) {
//             this[i] = this[i - 1];
//          }
//       }
//       this[to] = tmp;
//    }
// }

const move = (arr, from, to) => {
   let i, tmp;
   from = parseInt(from, 10);
   to = parseInt(to, 10);
   if (from !== to && 0 <= from && from <= arr.length && 0 <= to && to <= arr.length) {
      tmp = arr[from];
      if (from < to) {
         for (i = from; i < to; i++) {
            arr[i] = arr[i + 1];
         }
      }
      else {
         for (i = from; i > to; i--) {
            arr[i] = arr[i - 1];
         }
      }
      arr[to] = tmp;
   }
   return arr;
}

let gallerySelectedItemIds = [];
let gallerySelectedItems = [];
let galleryItems = [];

const getImageSelectTemplate = (item) => {
   return `<button class="gallery-item-select-btn" type="button" onclick="toggleItem(${item.id})"></button>
         <img onclick="toggleItem(${item.id})" src="/${item.url.replace('.', '_min.')}" class="gallery-item cursor-pointer"/>`;
}
const getImageTemplate = (item) => {
   return `<button class="gallery-item-remove-btn" type="button" onclick="removeItem(${item.id})">&times;</button>
         <img src="/${item.url.replace('.', '_min.')}" class="gallery-item"/>`;
}

const getVideoSelectTemplate = (item) => {
   return `<button class="gallery-item-select-btn" type="button" onclick="toggleItem(${item.id})"></button>
         <video controls data-src="/${item.url.replace('.m3u8', '_0.m3u8')}" class="gallery-item video-hls"></video>`;
}
const getVideoTemplate = (item) => {
   return `<button class="gallery-item-remove-btn" type="button" onclick="removeItem(${item.id})">&times;</button>
         <video controls data-src="/${item.url.replace('.m3u8', '_0.m3u8')}" class="gallery-item video-hls"></video>`;
}

const removeSelectedId = (id) => {
   const index = gallerySelectedItemIds.indexOf(id);
   if (index > -1) {
      gallerySelectedItemIds.splice(index, 1);
      gallerySelectedItems = gallerySelectedItems.filter(item => item.id !== id);
   }
}

const removeItem = (id) => {
   const block = document.getElementById('gallery-item-'+id);
   if (block) {
      block.remove();
      removeSelectedId(id);
      updateGalleryInput();
   }
}

const toggleItem = (id) => {
   // cleanSelect();
   const block = document.getElementById('gallery-item-select-'+id);
   if (block) {
      if (block.classList.contains('selected')) {
         block.classList.remove('selected');
         removeSelectedId(id);
      } else {
         block.classList.add('selected');
         gallerySelectedItemIds.push(id);
      }
   }
}

const appendItemTo = (item, block, isSelect = false) => {
   if (item.url) {
      const el = document.createElement('div');
      el.setAttribute('id', 'gallery-item-'+(isSelect ? 'select-' : '')+item.id);
      el.classList.add('gallery-item-wrap');
      if (gallerySelectedItemIds.indexOf(item.id) !== -1) {
         el.classList.add('selected');
      }
      if (isSelect === true) {
         console.log(item.type === 'video');
         el.innerHTML = item.type === 'video' ? getVideoSelectTemplate(item) : getImageSelectTemplate(item);
      } else {
         el.innerHTML = item.type === 'video' ? getVideoTemplate(item) : getImageTemplate(item);
      }

      block.append(el);
   }
}
const fillModal = (items, shouldClean = false) => {
   // console.log(items);
   const modalBody = document.getElementById('exampleModalCenterBody');
   modalBody.innerHTML = '';
   items.forEach(item => {
      appendItemTo(item, modalBody, true);
   });
}

const showGallery = (isLoadMore = false, limit = 10, page = 0) => {
   $.ajax({
      type:'GET',
      url:`/admin/gallery`,
      success: function(data) {
         galleryItems = data;
         fillModal(data);
         setHlsVideos('video-hls');
         $('#exampleModalCenter').modal('show');
      },
      error: function(error) {
         console.log(error);
      }
   });
}

const loadMore = () => {

}

const selectVideo = () => {
   const container = document.getElementById('galleryContainer');
   container.innerHTML = '';
   gallerySelectedItemIds.forEach(selectedId => {
      const selectedItem = galleryItems.find(el => el.id === selectedId);
      if (selectedItem) {
         gallerySelectedItems.push(selectedItem);
         appendItemTo(selectedItem, container);
      }
   });
   updateGalleryInput();
   Sortable.create(container, {
      animation: 100,
      group: 'list-1',
      direction: 'horizontal',
      draggable: '.gallery-item-wrap',
      handle: '.gallery-item-wrap',
      filter: '.sortable-disabled',
      chosenClass: 'active',
      onEnd: function(event) {
         gallerySelectedItemIds = move(gallerySelectedItemIds, event.oldIndex, event.newIndex);
         gallerySelectedItems = move(gallerySelectedItems, event.oldIndex, event.newIndex);
         updateGalleryInput();
         console.log(gallerySelectedItemIds);
      }
   });
   setHlsVideos('video-hls');
}

const updateGalleryInput = () => {
   document.getElementById('gallery').value = JSON.stringify(gallerySelectedItemIds);
}

const setGalleryVariables = (items) => {
   if (items instanceof Array) {
      galleryItems = items;
      items.forEach(item => {
         gallerySelectedItemIds.push(item.id);
      });
      selectVideo();
   }
   console.log(items);
}
window.setGalleryVariables = setGalleryVariables;
window.selectVideo = selectVideo;
window.showGallery = showGallery;
window.toggleItem = toggleItem;
window.removeItem = removeItem;
