@extends('layouts.email')

@section('content')
<tr>
    <td class="body_mailru_css_attribute_postfix" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0;padding: 0;width: 100%;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 100%;">
        <table class="inner-body_mailru_css_attribute_postfix" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0 auto;padding: 0;width: 570px;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 570px;">
            <tbody>
            <tr>
                <td class="content-cell_mailru_css_attribute_postfix" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;padding: 35px;">
                    <p style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;color: #74787E;font-size: 16px;line-height: 1.5em;margin-top: 0;text-align: center;">
                        Hello{{$user->name ? ', '.$user->name : ''}}!<br>
                        The reservation for {{date("F d - ", strtotime($data->start)).date("d", strtotime($data->end))}} on {{$data->event->title}} live course has been canceled due to booking time was expired.
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
@endsection
