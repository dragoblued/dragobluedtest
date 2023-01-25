import {getUserInfoTemplate, getProgressTemplate} from './templates';

const fillUserInfoModal = (data, simplified = true) => {
   const {user, courses, topics, lessons, progress} = data;
   if (!user) return;
   const modalBody = document.getElementById('exampleModalCenterBody');
   modalBody.innerHTML = '';
   let template = getUserInfoTemplate(user);
   if (!(simplified && (!courses || !topics || !lessons || !progress))) {
      template += getProgressTemplate(courses, topics, lessons, progress);
   }
   modalBody.innerHTML = template;
}

const showUserInfo = (id, simplified = true) => {
   console.log(id);
   $.ajax({
      type:'GET',
      url:`/admin/users/${id}/info/${simplified}`,
      success: function(data) {
         fillUserInfoModal(data, simplified);
         $('#exampleModalCenter').modal('show');
      },
      error: function(error) {
         console.log(error);
      }
   });
}
window.showUserInfo = showUserInfo;
