<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Course;
use App\Lesson;
use App\User;
class UserStatisticController extends AdminController
{
    
   protected $model;

   public function __construct ()
   {
      parent::__construct();
   }

   public function init ()
   {
      $this->setPage([
         'route' => 'admin.statistic',
         'title' => 'General statistics - [ ADMIN ]',
         'h1'    => 'General statistics',
         'func' => []
      ]);
     // $this->setModel(Date::class);
      $this->setForm();
      $this->setRules();
   }

    public function index(Request $request)
    {

      $ip1 = \Request::ip();
      $ip2 = $this->getIp();
      $users = User::orderBy('id', 'desc')->get();
      $usersIdx = [];
      $userLessons = [];
      $result3 = [];
       foreach ($users as $key => $user){
     		foreach ($user->user_lessons as $key => $user_lesson){
     			$userLessons[] = $user_lesson;
     			$usersIdx[$user->id] = $user->id;
     		}
       }
      $courses = Course::all();

      foreach ($courses as $key => $course) {
        $result3[] = $course->id;
      }
      $this->init();
      $items = [];
      
      $data = [
         'page'  => $this->getPage(),
         'items' => $courses,
         'users' => $users,
         'usersIdx' => $usersIdx,
         'result2' => $userLessons,
         'result3' => $result3,
         'userIp' => $ip1,
         'userIp2' => $ip2
      ];
      return view('admin.statistic', $data);
    }


    public function getIp(){
      foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
          if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
          }
      }
      return request()->ip(); // it will return server ip when no client ip found
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
}
