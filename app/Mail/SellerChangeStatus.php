<?php

namespace App\Mail;

use App\Models\Seller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerChangeStatus extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $text;
    public $rejectedText;
    public $buttonText, $buttonLink;
    public $logo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Seller $seller)
    {
        $this->rejectedText = '';
        if ($seller->getReviewStatus() == 'Aprobado') {
            $this->title = '¡Buenas noticias!';
            $this->text = 'Felicitaciones <strong>' . $seller->visible_name . '</strong>, hemos habilitado la cuenta de tu tienda.<br /><br />Para iniciar sesión haz click en el botón "Ingresar a mi tienda" e ingresa tus datos.<br /><br /><strong>Email:</strong> ' .$seller->email . '<br /><strong>Contraseña:</strong> (RUT empresa sin puntos, ej: 11222333-4)<br /><br />Recuerde cualquier consulta escribir a filsavirtual@prolibro.cl o llamar a  26720348 o 569 76163284.';

            $this->buttonText = 'Ingresar a mi tienda';
            $this->buttonLink = config('app.url') . '/admin';
        } else {
            $this->title = 'Se ha rechazado la solicitud.';
            $this->text = 'Lo sentimos <strong>' . $seller->visible_name . '</strong>, hemos rechazado tu solicitud para abrir una cuenta de vendedor.';

            if (strlen($seller->rejected_reason) > 0) {
                $this->rejectedText = 'Motivo de rechazo: ' . $seller->rejected_reason;
            }

            $this->buttonText = 'Ir al sitio';
            $this->buttonLink = config('app.url');
        }

        $this->logo = 'img/filsa-banner.jpg';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Respuesta de su solicitud')->view('maileclipse::templates.welcomeCustomer');
    }
}
