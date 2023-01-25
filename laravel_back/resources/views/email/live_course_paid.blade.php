@extends('layouts.email')

@section('content')
<tr>
    <td class="body_mailru_css_attribute_postfix" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0;padding: 0;width: 100%;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 100%;">
        <table class="inner-body_mailru_css_attribute_postfix" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0 auto;padding: 0;width: 570px;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 570px;">
            <tbody>
            <tr>
                <td class="content-cell_mailru_css_attribute_postfix" style="box-sizing: border-box;padding: 35px;">
                    <h1 style="width: 634px;height: auto;font-family: Roboto;font-style: normal;font-weight: 300;font-size: 40px;line-height: 54px;text-align: center;letter-spacing: 0.04em;text-transform: uppercase;color: #CC9966;margin: 30px auto;word-wrap: break-word; ">
                        You have purchased a live-course
                    </h1>

                    <p style="width: 455px;font-family: Roboto;font-style: normal;font-weight: normal; font-size: 20px;line-height: 28px;color: #5A5E62;margin: 30px auto;text-align: center;">
                        Hello{{$user->name ? ', '.$user->name : ''}}!<br>
                        Thank you for purchasing the live course. You can find the course information to follow the link
                    </p>

                    <a style="display:inline-block;padding:20px;width:634px;height:auto;background-color:#f5f2ec;margin:10px 35px"
                       href="{{config('app.site_url').'/live-courses/'.$data->event->route}}"
                    >
                        @isset($data->event->poster_url)
                            <img style="display:inline-block; vertical-align:middle;" src="{{$message->embed(asset($data->event->poster_url))}}" width="88px" height="88px">
                        @endisset
                        <div  style="display:inline-block; width: 75%; vertical-align: middle;margin: 20px 30px;">
                            @isset($data->event->title)
                                <h3 style="display:inline-block;margin:0 0 5px; width:100%;height:auto;font-family:Roboto;font-style:normal;font-weight:300;font-size:24px;line-height:1.3;letter-spacing:0.04em;text-transform:uppercase;color:#cc9966;"
                                >{{$data->event->title}}</h3>
                            @endisset
                            @isset($data->event->sub_title)
                                <h4 style="width:100%;height:auto;font-family:Roboto;font-style:normal;font-weight:bold;font-size:20px;line-height:23px;color:#5a5e62;margin:0;word-wrap:break-word"
                                >{{$data->event->sub_title}}</h4>
                            @endisset
                            @isset($data->event->subsign)
                                <p style="width:100%;height:auto;font-family:Roboto;font-style:normal;font-weight:300;font-size:20px;line-height:23px;color:#5a5e62;margin:5px 0"
                                >{{$data->event->subsign}}</p>
                            @endisset
                        </div>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
@endsection
