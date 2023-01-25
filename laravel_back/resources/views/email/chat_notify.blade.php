@extends('layouts.email')

@section('content')
<tr>
    <td class="body_mailru_css_attribute_postfix" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0;padding: 0;width: 100%;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 100%;">
        <table class="inner-body_mailru_css_attribute_postfix" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0 auto;padding: 0;width: 570px;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 570px;">
            <tbody>
            <tr>
                <td class="content-cell_mailru_css_attribute_postfix" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;padding: 35px;">
                    <h1 style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;color: #2F3133;font-size: 19px;font-weight: bold;margin-top: 0;text-align: left;">
                        New message from site chat!
                    </h1>
                    <p style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;color: #74787E;font-size: 16px;line-height: 1.5em;margin-top: 0;text-align: left;">
                        user: {{isset($data->user->name) ? $data->user->name :
                                                $data->user->login}}
                    </p>
                    <p style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;color: #74787E;font-size: 16px;line-height: 1.5em;margin-top: 0;text-align: left;">
                        Message text: {{isset($data->text) ? $data->text : 'not set'}}
                    </p>
                    <p style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;color: #74787E;font-size: 16px;line-height: 1.5em;margin-top: 0;text-align: left;">
                        <span>Email:</span>
                        <a href="mailto:{{$data->user->email}}"
                           style="font-family: Roboto, Helvetica, sans-serif;box-sizing: border-box;color: #69A7F7;font-size: 18px;font-weight:bold;text-decoration: underline;text-shadow: 0 1px 0 white;" target="_blank" rel=" noopener noreferrer"
                        >{{$data->user->email}}</a>
                    </p>
                    <p style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;color: #74787E;font-size: 16px;line-height: 1.5em;margin-top: 0;text-align: left;">
                        <span>Site page where the chat from: </span>
                        @if(isset($data->url_from))
                            <a href="{{$data->url_from}}"
                               style="font-family: Roboto, Helvetica, sans-serif;box-sizing: border-box;color: #69A7F7;font-size: 18px;font-weight:bold;text-decoration: underline;text-shadow: 0 1px 0 white;" target="_blank" rel=" noopener noreferrer"
                            >{{$data->url_from}}</a>
                        @else
                            <span>not set</span>
                        @endif
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
@endsection
