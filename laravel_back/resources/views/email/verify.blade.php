@extends('layouts.email')

@section('content')
<style type="text/css">
	@media  only screen and (max-width: 600px){
        .social-icons{
        	margin-left: 0%!important;
        }
    }
</style>

<tr>
    <td class="body_mailru_css_attribute_postfix" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0;padding: 0;width: 100%;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 100%;">
        <table class="inner-body_mailru_css_attribute_postfix" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: #FFFFFF;margin: 0 auto;padding: 0;width: 570px;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 570px;">
            <tbody>
            <tr>
                <td class="content-cell_mailru_css_attribute_postfix" style="box-sizing: border-box;padding: 35px;">
                    <div>
                        <h1 style="width: auto;height: auto;font-family: Roboto;font-style: normal;font-weight: 300;font-size: 40px;line-height: 54px;text-align: center;letter-spacing: 0.04em;text-transform: uppercase;color: #CC9966;margin: 30px auto;word-wrap: break-word; ">
                           @if($isRecovery === true)
                              <span>Account recovery</span>
                           @else
                              <span>Email confirmation</span>
                           @endif

                        </h1>
                        <p style="width: 455px;font-family: Roboto;font-style: normal;font-weight: normal;  font-size: 20px;line-height: 28px;color: #5A5E62;margin: 30px auto;text-align: center;">
                           @if($isRecovery === true)
                              <span>The account linked to this email was deleted. If you requested a recovery of your account, please click "recover" button. Otherwise please ignore this message.</span>
                           @else
                            <span>Thank you for signup! Before you begin, please verify this email. If you don't register on our site, please ignore this message.</span>
                           @endif
                        </p>
                        <a href="{{ $url }}" style="text-decoration:none; display: block; justify-content: center;align-items: center;padding: 15px 40px 15px 80px;font-family: Roboto;font-style: normal;font-weight: bold;font-size: 20px;line-height: 20px;color: white;width: 158px;height: auto;background: #CC9966; border-color: transparent; margin: 30px auto;">
                            <img style="margin-right: 10px; height: 20px; vertical-align: middle;" src="{{$message->embed(asset('media/img/verify.png'))}}">
                           @if($isRecovery === true)
                              <span style="vertical-align: bottom;">Recover</span>
                           @else
                              <span style="vertical-align: bottom;">Verify</span>
                           @endif
                        </a>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>

@endsection
