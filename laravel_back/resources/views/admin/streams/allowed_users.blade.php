<span class="form__signature">Allowed users (these users will can join the stream)</span>
<input type="hidden" name="allowed_users" id="allowedUsersInput" value="{{isset($item) ? json_encode($item->allowed_users) : ''}}">
<button class="btn btn-light font-weight-bold" type="button"
        onclick="showUserSelector('allowedUsersInput')">Select users</button>
@include('admin.inc.modal-dialog')
<script src="{{ asset('js/inc/show-user-selector.js') }}"></script>
