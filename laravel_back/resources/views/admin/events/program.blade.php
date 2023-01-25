<input name="program" type="hidden">
<div id="program" class="program">
   @isset($item)
      @foreach($item->program as $i => $programs)
         <div id="day{{$i + 1}}" class="form__line days">
            <div class="form__signature font-italic">Day {{ $i + 1 }}</div>
            <div class="program__items">
               @foreach($programs as $index => $program)
                  <div class="row" data-index="{{$index}}">
                     <div class="col-6 col-md-2 start">
                        <div class="form__line">
                           <span class="form__signature form__signature_required">Start</span>
                           <input name="start{{$i + 1}}[]" type="text" required
                                  value="{{ $program['start'] ?? '' }}">
                        </div>
                     </div>
                     <div class="col-6 col-md-2 end">
                        <div class="form__line">
                           <span class="form__signature form__signature_required">End</span>
                           <input name="end{{$i + 1}}[]" type="text" required
                                  value="{{ $program['end'] ?? '' }}">
                        </div>
                     </div>
                     <div class="col-12 col-md-8 label">
                        <div class="form__line">
                           <span class="form__signature form__signature_required">Label</span>
                           <input name="label{{$i + 1}}[]" type="text" required
                                  value="{{ $program['label'] ?? '' }}">
                        </div>
                     </div>
                     <div class="col-12 text-right">
                        <button class="btn btn-dark btn-sm" type="button"
                                onclick="removeProgramItem({{$i + 1}}, {{$index}})">-</button>
                     </div>
                  </div>
               @endforeach
            </div>
            <div class="text-right">
               <button id="{{ $i + 1 }}" type="button" class="btn btn-dark btn-sm mt-2"
                       onclick="addProgramItem({{$i + 1}})">+</button>
            </div>
         </div>
      @endforeach
   @endisset
</div>


@section('js')
   <script>
      const program = document.getElementById('program');
      const days = document.getElementsByClassName('days');
      const duration = document.getElementById('duration');
      const countProgram = document.getElementById('countProgram');

      let prevDuration = 0;

      const dayTemplate = (number) => {
         return `<div id="day${number}" class="form__line days">
         <div class="form__signature font-italic">Day ${number}</div>
      <div class="program__items">
         <div class="row" data-index="0">
            <div class="col-6 col-md-2 start">
               <div class="form__line">
                  <span class="form__signature form__signature_required">Start</span>
                  <input name="start${number}[]" type="text" required>
               </div>
            </div>
            <div class="col-6 col-md-2 end">
               <div class="form__line">
                  <span class="form__signature form__signature_required">End</span>
                  <input name="end${number}[]" type="text" required>
               </div>
            </div>
            <div class="col-12 col-md-8 label">
               <div class="form__line">
                  <span class="form__signature form__signature_required">Label</span>
                  <input name="label${number}[]" type="text" required>
               </div>
            </div>
            <div class="col-12 text-right">
                <button class="btn btn-dark btn-sm" type="button" onclick="removeProgramItem(${number}, 0)">-</button>
            </div>
         </div>
      </div>
      <div class="text-right">
         <button class="btn btn-dark btn-sm mt-2" type="button" onclick="addProgramItem(${number})">+</button>
      </div>
   </div>`;
      }

      const programItemTemplate = (dayNumber, index) => {
         return `<div class="col-6 col-md-2 start">
            <div class="form__line">
               <span class="form__signature form__signature_required">Start</span>
               <input name="start${dayNumber}[]" type="text" required>
            </div>
         </div>
         <div class="col-6 col-md-2 end">
            <div class="form__line">
               <span class="form__signature form__signature_required">End</span>
               <input name="end${dayNumber}[]" type="text" required>
            </div>
         </div>
         <div class="col-12 col-md-8 label">
            <div class="form__line">
               <span class="form__signature form__signature_required">Label</span>
               <input name="label${dayNumber}[]" type="text" required>
            </div>
         </div>
         <div class="col-12 text-right">
            <button class="btn btn-dark btn-sm" type="button" onclick="removeProgramItem(${dayNumber}, ${index})">-</button>
         </div>`;
      }

      const setDaysNum = (number) => {
         const currDaysLen = days.length;
         const diff = number - currDaysLen;
         if (diff > 0) {
            for (let i = 1; i <= diff; i++) {
               const container = document.createElement('span');
               container.innerHTML = dayTemplate(currDaysLen + i);
               program.append(container);
            }
            prevDuration = number;
         } else if (diff < 0) {
            if (confirm('Some elements will be removed from program list. Continue?')) {
               for (let i = 0; i > diff; i--) {
                  program.removeChild(program.lastChild);
               }
               prevDuration = number;
            } else {
               duration.value = prevDuration;
            }
         }
      }

      const addProgramItem = (dayNumber) => {
         console.log(dayNumber);
         const day = document.getElementById(`day${dayNumber}`);
         if (day) {
            const itemsWrap = day.querySelector('.program__items');
            const container = document.createElement('div');
            container.classList.add('row');
            container.setAttribute('data-index', itemsWrap.children.length);
            container.innerHTML = programItemTemplate(dayNumber, itemsWrap.children.length);
            itemsWrap.append(container);
         }
      }

      const removeProgramItem = (dayNumber, index) => {
         console.log(dayNumber, index);
         const day = document.getElementById(`day${dayNumber}`);
         if (day) {
            const itemsWrap = day.querySelector('.program__items');
            const deletingItem = itemsWrap.querySelector(`[data-index="${index}"]`);
            console.log(deletingItem);
            itemsWrap.removeChild(deletingItem);
         }
      }

      duration.addEventListener('change', () => {
         console.log(duration.value);
         const newValue = parseInt(duration.value);
         if (newValue > 0 && newValue <= 7) {
            setDaysNum(newValue);
         }
      });
   </script>
@endsection
