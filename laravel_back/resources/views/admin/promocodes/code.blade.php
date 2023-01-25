<input type="text" name="code" value="{{old('code') ?? ($item->code ?? '') }}" class="w-25 d-inline-block text-uppercase" required>
<button class="btn btn-sm btn-dark ml-3" type="button" onclick="generateCode()">Generate</button>
<script>
   const generateCode = () => {
      $.ajax({
         type:'GET',
         url:`/admin/generate-promocode`,
         success: function(data) {
            console.log(data);
            $('[name="code"]').val(data);
         },
         error: function(error) {
            console.log(error);
         }
      });
   }
</script>
