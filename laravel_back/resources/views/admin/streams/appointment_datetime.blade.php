<span class="form__signature">Appointment date and time</span>
<input id="datetimepicker" type="text" name="appointment_datetime"
       value="{{ isset($item) ? $item->appointment_datetime : '' }}">

<script>
   window.onload = () => {
      $('#datetimepicker').datetimepicker({
         format: 'YYYY-MM-DD HH:mm',
         icons: {
            time:'fa fa-clock'
         }
      });
   }
</script>
