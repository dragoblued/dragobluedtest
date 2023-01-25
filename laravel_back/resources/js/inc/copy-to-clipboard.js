import Toastify from "toastify-js";

const copyToClipboard = (targetSelectorId) => {
   const block = document.getElementById(targetSelectorId);
   if (!block) return;
   const text = block.innerText;
   const dummy = document.createElement('textarea');
   document.body.appendChild(dummy);
   dummy.value = text;
   dummy.select();
   try {
      document.execCommand('copy');
      Toastify({
         text: 'Text has been copied to clipboard',
         backgroundColor: "#2F96B4",
      }).showToast();
   } catch (err) {
      console.error(err);
      Toastify({
         text: 'Text can\'t be copied to clipboard',
         backgroundColor: '#F89406',
      }).showToast();
   }
   document.body.removeChild(dummy);
}

window.copyToClipboard = copyToClipboard;
