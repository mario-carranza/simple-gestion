<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Neopolitan Confirm Email</title>
  <!-- Designed by https://github.com/kaytcat -->
  <!-- Robot header image designed by Freepik.com -->

  <style type="text/css">
  @import url(http://fonts.googleapis.com/css?family=Droid+Sans);

  /* Take care of image borders and formatting */

  img {
    max-width: 600px;
    outline: none;
    text-decoration: none;
    -ms-interpolation-mode: bicubic;
  }

  a {
    text-decoration: none;
    border: 0;
    outline: none;
    color: #bbbbbb;
  }

  a img {
    border: none;
  }

  /* General styling */

  td, h1, h2, h3  {
    font-family: Helvetica, Arial, sans-serif;
    font-weight: 400;
  }

  td {
    text-align: center;
  }

  body {
    -webkit-font-smoothing:antialiased;
    -webkit-text-size-adjust:none;
    width: 100%;
    height: 100%;
    color: #37302d;
    background: #ffffff;
    font-size: 16px;
  }

   table {
    border-collapse: collapse !important;
  }

  .headline {
    color: #ffffff;
    font-size: 36px;
  }

 .force-full-width {
  width: 100% !important;
 }

 .force-width-80 {
  width: 80% !important;
 }

 :root {
    --blue: #3498DB;
}
  </style>

  <style type="text/css" media="screen">
      @media screen {
         /*Thanks Outlook 2013! http://goo.gl/XLxpyl*/
        td, h1, h2, h3 {
          font-family: 'Droid Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;
        }
      }
  </style>

  <style type="text/css" media="only screen and (max-width: 480px)">
    /* Mobile styles */
    @media only screen and (max-width: 480px) {

      table[class="w320"] {
        width: 320px !important;
      }

      td[class="mobile-block"] {
        width: 100% !important;
        display: block !important;
      }


    }
  </style>
</head>
<body class="body" style="padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none" bgcolor="#ffffff">
<table class="force-full-width" cellspacing="0" cellpadding="0" align="center">
<tbody>
<tr>
<td align="center" valign="top" bgcolor="#ffffff" width="100%"><center>
<table class="w320" style="margin: 0 auto;" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="center" valign="top">
<table class="force-full-width" style="border-collapse: collapse; width: auto;" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td style="font-size: 30px; text-align: center;"><br /><img src="{{ asset('img/logos/logo-crcp.png') }}" alt="CRCP" /><br /><br /></td>
</tr>
</tbody>
</table>
<table class="force-full-width" style="border-collapse: collapse; height: 470px; width: 611px;" cellspacing="0" cellpadding="0" align="center" bgcolor="#4dbfbf">
<tbody>
<tr style="height: 263px;">
<td style="height: 263px; width: 600px; background-color: #e67e23;"><br /><img src="{{asset($logo)}}" alt="" width="" height="" /></td>
</tr>
<tr style="height: 43px;">
<td class="headline" style="height: 43px; width: 600px; background-color: #e67e23;">{{ $header }}</td>
</tr>
<tr style="height: 76px;">
<td style="height: 76px; width: 600px; background-color: #e67e23;"><center>
  <br />
  <br />
  <table
  width="80%"
  cellspacing="0"
  cellpadding="0"
  bgcolor="#ffffff"
  style="
    border-radius: 7px;
    padding: 7px;
"
>
  <tbody>
    <tr>
      <td
        style="
          text-align: center;
          font-size: 30px;
          padding-bottom: 20px;
        "
      >
        {{ $title }}
      </td>
    </tr>
    <tr>
      <td style="padding-bottom: 20px">
        {{ $text }}
      </td>
    </tr>
    <tr>
      <td style="padding-bottom: 20px">
        <strong>Pros: </strong>{{ $pros }}
      </td>
    </tr>
    <tr>
      <td style="padding-bottom: 20px">
        <strong>Cons: </strong>{{ $cons }}
      </td>
    </tr>
  </tbody>
</table>
<table style="margin: 0 auto;" width="60%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td style="color: #ffffff;">
</td>
</tr>
</tbody>
</table>
</center></td>
</tr>
<tr style="height: 88px;">
<td style="height: 88px; width: 600px; background-color: #e67e23;">
<br />
<br />
<br />
<div><!-- [if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:50px;v-text-anchor:middle;width:200px;" arcsize="8%" stroke="f" fillcolor="#178f8f">
                          <w:anchorlock/>
                          <center>
                        <![endif]--> <a style="background-color: #4b566b; border-radius: 4px; color: #ffffff; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 50px; text-align: center; text-decoration: none; width: 200px; -webkit-text-size-adjust: none;" href="{{$buttonLink}}">{{ $buttonText }}</a> <!-- [if mso]>
                          </center>
                        </v:roundrect>
                      <![endif]--></div>
<br /><br /></td>
</tr>
</tbody>
</table>
<table class="force-full-width" style="margin: 0px auto; height: 75px;" cellspacing="0" cellpadding="0" bgcolor="#414141">
<tbody>
<tr style="height: 19px;">
<td style="background-color: #414141; height: 19px; width: 600px;">&nbsp;</td>
</tr>
<tr style="height: 28px;">
<td style="color: #bbbbbb; font-size: 12px; height: 28px; width: 600px;"><a href="{{ $buttonLink }}">Ver en navegador</a><br /><br /></td>
</tr>
<tr style="height: 28px;">
<td style="color: #bbbbbb; font-size: 12px; height: 28px; width: 600px;">&copy; 2020 Todos los derechos reservados - {{ config('app.name') }}<br /><br /></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</center></td>
</tr>
</tbody>
</table>
</body>
</html>