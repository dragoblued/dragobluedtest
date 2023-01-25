import {getUserListChecksTemplate} from './templates';

let selection = [];
let selectionInput;
let usersId = [];
let usersNum = 0;

const fillUserListModal = (data) => {
   const {users, groups} = data;
   if (!users) return;
   usersNum = users.length;
   usersId = users.map(user => user.id);
   selection = selectionInput.value === '' ? usersId : JSON.parse(selectionInput.value);
   const modalTitle = document.getElementById('exampleModalLongTitle');
   const modalBody = document.getElementById('exampleModalCenterBody');
   // const modalFooter = document.getElementById('exampleModalFooter');
   modalTitle.innerHTML = 'Users selection';
   modalBody.innerHTML = getUserListChecksTemplate(users, selection);
   // modalFooter.innerHTML = `
   //          <button type="button" class="btn btn-primary font-weight-bold" data-dismiss="modal"
   //                  onclick="selectUsersFromList('users-selection', ${selectionInputId})">Select</button>
   //          <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Close</button>`;
}

const selectUserFromList = (event, id) => {
   const value = event.target.checked;
   if (selection === null) {
      selection = [];
   }
   if (value === true) {
      selection.push(id);
   } else {
      selection = selection.filter(item => item !== id);
   }
   if (selection.length === usersNum) {
      document.getElementById('users-selection-all').checked = true;
   } else {
      document.getElementById('users-selection-all').checked = false;
   }
   if (selection.length === 0) {
      document.getElementById('users-selection-all').checked = false;
   }
   selectionInput.value = JSON.stringify(selection);
}

const selectAllUsersFromList = (event) => {
   const value = event.target.checked;
   if (value === true) {
      [...document.getElementsByClassName('user-selection-input')].forEach(input => input.checked = true);
      selection = usersId;
      selectionInput.value = null;
   } else {
      [...document.getElementsByClassName('user-selection-input')].forEach(input => input.checked = false);
      selection = [];
      selectionInput.value = JSON.stringify(selection);
   }
}

const showUserSelector = (selectionInputId) => {
   console.log(selectionInputId);
   selectionInput = document.getElementById(selectionInputId);
   if (!selectionInput) return;
   $.ajax({
      type:'GET',
      url:`/admin/user-list`,
      success: function(data) {
         console.log(data);
         fillUserListModal(data, selectionInputId);
         $('#exampleModalCenter').modal('show');
      },
      error: function(error) {
         console.log(error);
      }
   });
}
window.showUserSelector = showUserSelector;
window.selectUserFromList = selectUserFromList;
window.selectAllUsersFromList = selectAllUsersFromList;
