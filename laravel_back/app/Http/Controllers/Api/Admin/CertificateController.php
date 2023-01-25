<?php

namespace App\Http\Controllers\Api\Admin;

use App\Certificate;
use App\Test;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Zend_Pdf;

class CertificateController extends AdminController
{
    public function __construct ()
    {
        $this->model = Certificate::class;
        $this->rules = [
            'test_id'   => 'required|integer',
            'user_id'   => 'required|integer'
        ];
    }

    public function store (Request $request)
    {
        $request->validate($this->rules);
        $test = Test::findOrFail($request->test_id);
        $course = $test->course;
        $user = User::findOrFail($request->user_id);

        $fillData = [
            $course->title,
            $test->max_mark_percent,
            $user->name,
            Carbon::now()->format('d.m.yy')
        ];
        $certificateUrl = $this->saveSertificate($user->id, $course->name, $fillData);
        $request->merge([
            'course_id' => $course->id,
            'file_url' => $certificateUrl
        ]);

        $item = $this->model::create($request->all());

        return $item;
    }

    public function saveSertificate ($userId, $courseName, $fillData): string
    {
//        $pdf = Zend_Pdf::load('templates/certificate_template.pdf');
//        $names = $pdf->getTextFieldNames();
//        foreach ($names as $i => $name) {
//            if (array_key_exists($i, $fillData)) {
//                $pdf->setTextField($name, $fillData[$i]);
//                $pdf->markTextFieldAsReadOnly($name);
//            }
//        }
        /*$path = "media/users/{$userId}";
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
        $url = $path."/{$courseName}_certificate.pdf";
        File::copy($url, storage_path('media/templates/certificate_template.pdf'));*/
//        $pdf->save($url);
        $url = "template/certificate_template.pdf"; 
        return $url;
    }

    public function getTemplate ()
    {
        $pdf = Zend_Pdf::load(storage_path('media/templates/certificate_template2.pdf'));
        return $pdf->getTextFieldNames();
    }
}
