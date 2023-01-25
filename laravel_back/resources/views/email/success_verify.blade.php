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
                        <p style="width: 455px;font-family: Roboto;font-style: normal;font-weight: normal;  font-size: 20px;line-height: 28px;color: #5A5E62;margin: 30px auto;text-align: center;">Your password was successfully changed!</p>
                        <p style="width: 455px;font-family: Roboto;font-style: normal;font-weight: normal;  font-size: 20px;line-height: 28px;color: #5A5E62;margin: 30px auto;text-align: center;">
                            If you did change password, no further action is required. If you did not change password, please inform our support to protect your account
                        </p>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>

@endsection
