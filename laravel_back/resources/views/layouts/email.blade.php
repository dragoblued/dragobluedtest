<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <title></title>
    <meta content="text/html; charset=utf-8" http-equiv="content-type" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="format-detection" content="telephone=no" />
	<meta name="format-detection" content="date=no" />
	<meta name="format-detection" content="address=no" />
	<meta name="format-detection" content="email=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="{{ asset('media/img/logo.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
    <!--[if gte mso 9]>
		<xml>
  		<o:OfficeDocumentSettings>
    		<o:AllowPNG/>
    		<o:PixelsPerInch>96</o:PixelsPerInch>
 		</o:OfficeDocumentSettings>
		</xml>
	<![endif]-->
</head>
<body>
<style>
    @media  only screen and (max-width: 600px){
        .class_1575401922 .inner-body_mailru_css_attribute_postfix{
            width:100% !important;
        }.class_1575401922 .footer_mailru_css_attribute_postfix{
             width:100% !important;
         }
    }
    @media  only screen and (max-width: 500px){
        .class_1575401922 .button_mailru_css_attribute_postfix{
            width:100% !important;
        }
    }
</style>

<table class="wrapper_mailru_css_attribute_postfix" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;background-color: white;margin: 0;padding: 0;width: 100%;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 100%;">
    <tbody>
    <tr>
        <td align="center" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;">
            <table class="content_mailru_css_attribute_postfix" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;margin: 0;padding: 0;width: 100%;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 100%;">
                <tbody>
                <tr>
                    <td class="header_mailru_css_attribute_postfix" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;padding: 25px 0;text-align: center; height: 155px;
            background: #1E1E1E !important;
            margin-bottom: 78px;">
                        <a href="{{ config('app.site_url') }}" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;color: #bbbfc3;font-size: 19px;font-weight: bold;text-decoration: none;text-shadow: 0 1px 0 white;" target="_blank" rel=" noopener noreferrer">
                            <img width="117px" height="76px" src="{{$message->embed(asset('media/img/logo-white-large.png'))}}">
                        </a>
                    </td>
                </tr>

                @yield('content')

                <tr>
                    <td style="margin-top:-30px; font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;">
                        <table class="footer_mailru_css_attribute_postfix" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;margin: 0 auto;padding: 0;text-align: center;width: 570px;-premailer-cellpadding: 0;-premailer-cellspacing: 0;-premailer-width: 570px;">
                            <tbody>
                            <tr>
                               <hr class="line" style="width: 676px;border: 1px solid #CC9966;">
                            </tr>
                            <tr>
                                <td class="content-cell_mailru_css_attribute_postfix" align="center" style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;padding: 0 35px 35px 35px;">
                                    <p class="text-center text-sm" style="position: static;width: 441px;font-family: Roboto;font-style: normal;font-weight: normal;font-size: 12px;line-height: 16px;text-align: center;color: #828282;margin: 25px auto;"
                                    >{{ $settings->email_newsletter ?? $settings['email_newsletter'] }}<br>
                                      <a href="mailto:{{ $settings->email ?? '' }}"style="width: 441px;font-family: Roboto;font-style: normal;font-weight: normal;font-size: 12px;line-height: 16px;text-align: center;text-decoration-line: underline;color: #2D9CDB;margin: 0 auto;"
                                      >{{ $settings->email ?? $settings['email'] }}</a>
                                    </p>
                        <!--              -->
                                    <p style="font-family: Avenir, Helvetica, sans-serif;box-sizing: border-box;line-height: 1.5em;margin-top: 15px;color: #AEAEAE;font-size: 12px;text-align: center;">
                                        <a style="text-decoration: none;" href="{{ $settings->social_urls['youtube']['url'] ?? $settings['social_urls']['youtube']['url']}}">
                                            <img src="{{$message->embed(asset('media/img/youtube.png'))}}" width="88px" height="35px">
                                        </a>
                                        <a style="text-decoration: none;" href="{{ $settings->social_urls['facebook']['url'] ?? $settings['social_urls']['facebook']['url']}}">
                                            <img style="margin-left: 20px;" src="{{$message->embed(asset('media/img/facebook.png'))}}" width="35px" height="35px">
                                        </a>
                                        <a style="text-decoration: none;" href="{{ $settings->social_urls['instagram']['url'] ?? $settings['social_urls']['instagram']['url']}}">
                                            <img style="margin-left: 20px;" src="{{$message->embed(asset('media/img/instagram.png'))}}" width="35px" height="35px">
                                        </a>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>

</body>
</html>
