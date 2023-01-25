import { Ajax, Action, Auth, Form, List, Images, Utilities } from './common';

Ajax.csrf();
Auth.exit('.js-logout');
Form.style('file');
Form.dataTable();
Form.wysiwyg();
Form.files();
Form.dateRange();
List.bindDeletes();
Images.fancy();
Utilities.mask('.phone-mask', '+0(000)000-00-00');
Utilities.inputMask('.phone-inputmask', '+9(999)999-99-99');
window.fetchServerAction = Action.fetchServerAction;

$(document).ready(function() {
   $('.list__item').click(function() {
      $(this).siblings('.list__item').removeClass('current');
      $(this).toggleClass('current');
   });

   // slug routes -->
   let route = $("input[name='route']");
   let name = $("input[name='title']");
   
   if (window.location.pathname === '/admin/events/create') {
      name = $("input[name='name']");
   }
   console.log('name', name);
   
   name.on("keydown", function() {
      setTimeout(function(elem){
         route.val(slugify(elem.val()));
      }, 100, $(this));
   });

   function slugify(str) {
      str = str.replace(/^\s+|\s+$/g, ''); // trim
      str = str.toLowerCase();

      let from = [
         'а', 'б', 'в', 'г', 'д', 'е', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
         'х', 'ц', 'ч', 'ш','щ', 'ъ', 'ь', 'ю', 'я'];
      let to = [
         'a', 'b', 'v', 'g', 'd', 'e', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
         'h', 'c', 'ch', 'sh','sht', 'y', '', 'iu', 'ia'];
      for (let key in from) {
         str = str.replace(new RegExp(from[key], 'g'), to[key])
      }

      str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
         .replace(/\s+/g, '-') // collapse whitespace and replace by -
         .replace(/-+/g, '-'); // collapse dashes

      return str;
   }

   // admin route gallery
   // let cols = $('.content__col'); // content col
   //
   // let col = cols.find('select'); // content col > select
   //
   // if(col.val() === 'image') { // content col > select > option = image
   //    cols[2].style.display = 'block';
   //    cols[3].style.display = 'none';
   // } else if(col.val() === 'video') {
   //    cols[2].style.display = 'block';
   //    cols[3].style.display = 'block';
   // }
   //
   // col.on('change', function() {
   //    if(this.value === 'image') { // change select, content col > select > option = image
   //       cols[2].style.display = 'block';
   //       cols[3].style.display = 'none';
   //    } else if(this.value === 'video') {
   //       cols[2].style.display = 'block';
   //       cols[3].style.display = 'block';
   //    }
   // });

   // $('#datepicker').datepicker({
   //    changeYear: true,
   //    showButtonPanel: true,
   //    dateFormat: 'yy',
   //    onClose: function(dateText, inst) {
   //       var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
   //       $(this).datepicker('setDate', new Date(year, 1));
   //    }
   // });

   // $(".date-picker-year").focus(function () {
   //    $(".ui-datepicker-month").hide();
   // });

   // $("#btnDates").click(function() {
   //    $('input[name="daterange[]"]').daterangepicker({
   //       opens: 'center',
   //       locale: {
   //          format: 'YYYY-MM-DD'
   //       }
   //    });
   // });

   /**
    * Burger menu
    */
   $('.header__burger').click(function(event) {
      $('.header__burger,.header__menu').toggleClass('active');
      $('body').toggleClass('lock');
   });

   resize();
});

$(window).resize(function(){
   resize();
})


/**
 * Burger menu
 *
 * @media (max-width: 767px)
 */
let menu = $('.menu');
let header__body = $('.header__body');
let header__list = $('.header__list');
let menu__user = $('.menu__user');
let menu__links = $('.menu__links')

function resize()
{
   if($(window).width() < 768) {
      header__body.after(menu__user);
      header__list.append(menu__links);
   }
   else {
      menu.append(menu__user);
      menu__user.after(menu__links);
      // .prepend( $('.menu__user'));
   }
}
