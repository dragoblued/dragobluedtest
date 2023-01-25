<div class="form-group duration">
   <input type="number" min="0" step="1" class="duration__input"
          name="total_lessons_duration[]" id="duration-h"
          value="{{isset($item) ? gmdate("H", $item->total_lessons_duration) : null}}">
   <small class="text-muted">h</small>
   <span class="mx-2">:</span>
   <input type="number" min="0" step="1" max="59" class="duration__input"
          name="total_lessons_duration[]" id="duration-min"
          value="{{isset($item) ? gmdate("i", $item->total_lessons_duration) : null}}">
   <small class="text-muted">min</small>
   <span class="mx-2">:</span>
   <input type="number" min="0" step="1" max="59" class="duration__input"
          name="total_lessons_duration[]" id="duration-sec"
          value="{{isset($item) ? gmdate("s", $item->total_lessons_duration) : null}}">
   <small class="text-muted">sec</small>
   @isset($item)
   <button class="btn btn-dark btn-sm mx-3" title="{{ gmdate("H:i:s", $item->calc_total_duration) }}" type="button"
           onclick="insertAutoCalcDuration('{{ gmdate("H:i:s", $item->calc_total_duration) }}')"
   >insert auto-calculated duration</button>
   @endisset
</div>

<style>
   .duration__input {
      width: 60px !important;
   }
</style>

<script>
   const insertAutoCalcDuration = (value) => {
      value = value.split(':');
      if (value.length === 3) {
         if (value[0] > -1 && value[1] > -1 && value[2] > -1) {
            document.getElementById('duration-h').value = value[0];
            document.getElementById('duration-min').value = value[1];
            document.getElementById('duration-sec').value = value[2];
         }
      }
      console.log(value);
   }
</script>
