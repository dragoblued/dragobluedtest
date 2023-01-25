<?php

namespace App\Http\Controllers\Api\Admin;

use App\Notification;

class NotificationController extends AdminController
{
    public function __construct ()
    {
        $this->model = Notification::class;
        $this->rules = [
            'user_id' => 'required|integer',
            'is_new' => 'nullable',
            'type' => 'required|string',
            'name' => 'nullable',
            'message_id' => 'required|integer'
        ];
    }

    public function makeOutdated (int $id)
    {
        $item = $this->model::findOrFail($id);
        $item->is_new = false;
        $item->save();
        return null;
    }
}
