const getAccordionTemplate = (accordionId, body) => {
   return `
   <ul id="${accordionId}" class="list-group list-group-flush tree">${body}</ul>`;
}
const getCardTemplate = (cardId, parentAccordionId, heading, body, isShow = false) => {
   const show = isShow ? 'show' : '';
   return `
   <li class="list-group-item tree__item tree-item">
      <div class="tree-item__header cursor-pointer"
           data-toggle="collapse" data-target="#${cardId}" aria-expanded="${isShow}" aria-controls="${cardId}">
        <h5 class="mb-0 d-flex justify-content-between align-items-center">
           <i class="fa fa-chevron-right mr-2 fz-0_7rem tree-item__arrow-icon"></i>
           ${heading}
        </h5>
      </div>

      <div id="${cardId}" class="collapse ${show}" data-parent="#${parentAccordionId}">
        <div class="tree-item__body">${body}</div>
      </div>
   </li>`;
}

const getProgressTemplate = (courses, topics, lessons, progress) => {
   let coursesT = '';
   courses.forEach((course, courseIdx) => {
      let topicsT = '';
      topics[course.id]?.forEach((topic, topicIdx) => {
         let lessonsT = '<ul class="list-group list-group-flush">';
         lessons[course.id]?.[topic.id]?.forEach((lesson, lessonIdx) => {
            const viewedBadgeColor = lesson.is_viewed ? 'dark' : 'light';
            const viewedBadgeLabel = lesson.is_viewed ? 'Viewed' : 'Not viewed';
            lessonsT += `<li id="lesson${lessonIdx}" class="list-group-item d-flex justify-content-between align-items-center"
                ><span>${lesson.title}</span><span class="badge badge-${viewedBadgeColor} badge-pill">${viewedBadgeLabel}</span></li>`;
         });
         lessonsT += '</ul>';
         const viewedBadgeCount = topic.lessons_count;
         const viewedBadgeViewCount = topic.lessons_view_count ?? 0;
         const viewedBadgeColor = topic.lessons_view_count >= topic.lessons_count ? 'dark' : 'light';
         const purchasedBadge = topic.is_purchased ? '<span class="badge badge-info badge-pill mr-1">Purchased</span>' : '';
         topicsT += getCardTemplate(
            'topic'+topicIdx,
            'topicsAccordion',
            `<span class="mr-auto">${topic.title}</span>
                    ${purchasedBadge}
                    <span class="badge badge-${viewedBadgeColor} badge-pill">Viewed ${viewedBadgeViewCount} / ${viewedBadgeCount}</span>`,
            lessonsT
         );
      });
      const topicsAcc = getAccordionTemplate('topicsAccordion', topicsT);
      const viewedBadgeCount = course.lessons_count;
      const viewedBadgeViewCount = course.lessons_view_count ?? 0;
      const viewedBadgeColor = course.lessons_view_count >= course.lessons_count ? 'dark' : 'light';
      const purchasedBadge = course.is_purchased ? '<span class="badge badge-info badge-pill mr-1">Purchased</span>' : '';

      coursesT += getCardTemplate(
         'course'+courseIdx,
         'coursesAccordion',
         `<span class="mr-auto">${course.title}</span>
                    ${purchasedBadge}
                    <span class="badge badge-${viewedBadgeColor} badge-pill">Viewed ${viewedBadgeViewCount} / ${viewedBadgeCount}</span>`,
         topicsAcc);
   });
   const coursesAcc = getAccordionTemplate('coursesAccordion', coursesT);
   return getAccordionTemplate(
      'firstAccordion',
      getCardTemplate(
         'firstCard',
         'firstAccordion',
         '<span class="mr-auto">Video Courses</span>',
         coursesAcc,
         true
      )
   );
}

const getUserInfoTemplate = (user) => {
   if (!user) return '';

   let res = '<div class="mb-3">';
   const avatarUrl = user.avatar_url ?? 'media/img/avatar-default.jpg';
   res += `<img src="/${avatarUrl}" class="modal-card__avatar" alt="User Avatar"/>`;
   if (user.name) {
      res += `<p class="modal-card__line mx-auto"><span class="font-weight-bold">Name: </span><span class="ml-3">${user.name} ${user.surname ?? ''}</span></p>`;
   }
   if (user.login) {
      res += `<p class="modal-card__line mx-auto"><span class="font-weight-bold">Login: </span><span class="ml-3">${user.login}</span></p>`;
   }
   if (user.email) {
      res += `<p class="modal-card__line mx-auto"><span class="font-weight-bold">Email: </span><a class="ml-3" href="mailto:${user.email}">${user.email}</a></p>`;
   }
   if (user.role) {
      res += `<p class="modal-card__line mx-auto"><span class="font-weight-bold">Role: </span><span class="ml-3">${user.role?.name}</span></p>`;
   }
   if (user.phone) {
      res += `<p class="modal-card__line mx-auto"><span class="font-weight-bold">Email: </span><a class="ml-3 phone-mask" href="tel:${user.phone}">${user.phone}</a></p>`;
   }
   res += '</div>';

   return res;
}

const getUserListChecksTemplate = (users = [], selected = []) => {
   console.log(users, selected);
   const allChecked = !selected || users.length === selected?.length ? 'checked="checked"' : '';
   let list =
      `<div class="custom-control custom-checkbox mb-1">
         <input type="checkbox" class="custom-control-input" name="users-selection-all" id="users-selection-all"
                onchange="selectAllUsersFromList(event)" ${allChecked}>
         <label class="custom-control-label cursor-pointer" for="users-selection-all">Select all</label>
      </div>`;
   list += '<ul class="form-group pl-4">';
   users.forEach(user => {
      const userName = user.email + (user.name ? ` (${user.name})` : '');
      const checked = !selected || selected?.includes(user.id) ? 'checked="checked"' : '';
      list +=
         `<li class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input user-selection-input" name="users-selection[]"
                   id="users-selection-${user.id}" onchange="selectUserFromList(event, ${user.id})" ${checked}>
            <label class="custom-control-label cursor-pointer" for="users-selection-${user.id}">${userName}</label>
         </li>`;
   });
   list += '</ul>';
   return list;
}

export {getProgressTemplate, getUserInfoTemplate, getUserListChecksTemplate};
