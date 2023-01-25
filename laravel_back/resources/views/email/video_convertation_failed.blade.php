@extends('layouts.email')

@section('content')
<tr>
    <td class="body_mailru_css_attribute_postfix" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0;padding: 0;width: 100%;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 100%;">
        <table class="inner-body_mailru_css_attribute_postfix" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0 auto;padding: 0;width: 570px;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 570px;">
            <tbody>
            <tr>
                <td class="content-cell_mailru_css_attribute_postfix" style="box-sizing: border-box;padding: 35px;">
                    <p style="width: 455px;font-family: Roboto;font-style: normal;font-weight: normal; font-size: 20px;line-height: 28px;color: #5A5E62;margin: 30px auto;text-align: left;">
                        The "<a href="{{config('app.url').'/admin/lessons?highlight='.$data->id}}" style="font-family: Roboto, Helvetica, sans-serif;box-sizing: border-box;color: #69A7F7;font-size: 18px;font-weight:bold;text-decoration: underline;text-shadow: 0 1px 0 white;" target="_blank" rel="noopener noreferrer">{{$data->title}}</a>" video convertation has been <span style="color: red;">failed</span>.
                       <br>Please, contact developers to get help.
                       <br>Follow the link to check <a href="{{config('app.url').'/admin/lessons'}}" style="font-family: Roboto, Helvetica, sans-serif;box-sizing: border-box;color: #69A7F7;font-size: 18px;font-weight:bold;text-decoration: underline;text-shadow: 0 1px 0 white;" target="_blank" rel="noopener noreferrer">all video tutorials</a>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
@endsection
