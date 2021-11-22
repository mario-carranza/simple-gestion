@php
use App\Models\Product;
@endphp
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Comprobante de pago</title>
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

        td,
        h1,
        h2,
        h3 {
            font-family: Helvetica, Arial, sans-serif;
            font-weight: 400;
        }

        td {
            text-align: center;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
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

        .force-width-90 {
            width: 90% !important;
        }

        .direccion-facturacion-titulo {
            margin-botom: 5px;
        }

        .direccion-envio-titulo {
            margin-botom: 5px;
        }

    </style>

    <style type="text/css" media="screen">
        @media screen {

            /*Thanks Outlook 2013! http://goo.gl/XLxpyl*/
            td,
            h1,
            h2,
            h3 {
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

<body class="body" style="padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none"
    bgcolor="#ffffff">
    <table class="force-full-width" cellspacing="0" cellpadding="0" align="center">
        <tbody>
            <tr>
                <td align="center" valign="top" bgcolor="#ffffff" width="100%">
                    <center>
                        <table class="w320" style="margin: 0 auto;" width="600" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <table class="force-full-width" style="margin: 0 auto;" cellspacing="0"
                                            cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td style="font-size: 30px; text-align: center;"><br /><img
                                                            src="{{ asset('img/logos/logo-crcp.png') }}"
                                                            alt="CRCP" /><br /><br /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                      
                                        <table class="force-full-width" style="margin: 0 auto;" cellspacing="0"
                                            cellpadding="0" bgcolor="#f5774e">
                                            <tbody>
                                                <tr>
                                                    <td style="background-color: #f5774e;">
                                                       <p style="color: #ffffff;">
                                                            Saludos cordiales,
                                                       </p>
                                                       <p style="color: #ffffff;">
                                                            Le notificamos que el mensaje para acordar el envío fue entregado al vendedor. 
                                                       </p>
                                                       <br />
                                                        <center>
                                                           <div>
                                                               <p style="color: #ffffff;"><strong>El número de orden es: #{{$order->id}}</strong></p>
                                                               <p style="color: #ffffff;"><strong>Para el vendedor: {{$seller->visible_name}}</strong></p>
                                                               <p style="color: #ffffff;"><strong>Mensaje:</strong></p>
                                                               <p style="color: #ffffff; max-width:200px;overflow-wrap: break-word;">{{$sellerMessage}}</p>
                                                           </div>
                                                        </center>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="force-full-width" style="margin: 0px auto; height: 75px;"
                                            cellspacing="0" cellpadding="0" bgcolor="#414141">
                                            <tbody>
                                                <tr style="height: 19px;">
                                                    <td style="background-color: #414141; height: 19px; width: 600px;">
                                                        &nbsp;</td>
                                                </tr>
                                                {{-- <tr style="height: 28px;">
                                                    <td
                                                        style="color: #bbbbbb; font-size: 12px; height: 28px; width: 600px;">
                                                        <a href="#">Ver en navegador</a><br /><br />
                                                    </td>
                                                </tr> --}}
                                                <tr style="height: 28px;">
                                                    <td
                                                        style="color: #bbbbbb; font-size: 12px; height: 28px; width: 600px;">
                                                        &copy; 2020 Todos los derechos reservados -
                                                        {{ config('app.name') }}<br /><br />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </center>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
