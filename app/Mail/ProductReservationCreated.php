<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductReservationCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $text;
    public $rejectedText;
    public $buttonText, $buttonLink;
    public $logo;
    public $receiver;
    public $productReservation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($productReservation, string $receiver)
    {
        $this->receiver = $receiver;

        $this->productReservation = $productReservation;

        $this->logo = 'img/logo-pyme.png';

        if ($receiver === 'seller') {
            $this->title = 'Tienes una nueva solicitud de reserva';
            $this->text = 'Has recibido una nueva solicitud de reserva para tu servicio <strong>' . $productReservation->product->name . '</strong>. Puedes acceder al panel de administrador para aprobarla o rechazarla.';
            $this->buttonText = 'Ir al panel';
            $this->buttonLink = route('productreservation.index');
        } else if ($receiver === 'customer') {
            $this->title = 'Tu solicitud de reserva ha sido enviada';
            $this->text = 'Tu solicitud de reserva para <strong>' . $productReservation->product->name . '</strong> ha sido enviada. Una vez que el vendedor apruebe tu solicitud, recibirás un correo con mas información para completar el pago.';
            $this->text .= '<br><br>';
            $this->text .= '<b>Información de tu reserva</b>';
            $this->text .= '<br><br>';
            $this->text .= '<b>Servicio:</b> ' . $productReservation->product->name . '<br>';
            $this->text .= '<b>Precio:</b> ' . currencyFormat($productReservation->price, 'CLP', true) . '<br>';
            $this->text .= '<b>Nombre:</b> ' . $productReservation->name . '<br>';
            $this->text .= '<b>Correo:</b> ' . $productReservation->email . '<br>';
            $this->text .= '<b>Teléfono:</b> ' . $productReservation->cellphone . '<br>';

            if ($productReservation->product->is_tour) {
                $this->text .= '<b>Fecha de la experiencia:</b> ' . Carbon::parse($productReservation->check_in_date)->format('d/m/Y h:i a ') . '<br>';
            }

            if ($productReservation->product->is_housing) {
                $this->text .= '<b>Fecha de Check In:</b> ' . $productReservation->check_in_date->format('d/m/Y') . '<br>';
                $this->text .= '<b>Fecha de Check Out:</b> ' . $productReservation->check_out_date->format('d/m/Y') . '<br>';
            }

            $this->text .= '<b>Cantidad de adultos:</b> ' . $productReservation->adults_number . '<br>';
            $this->text .= '<b>Cantidad de niños:</b> ' . $productReservation->childrens_number . '<br>';
            $this->buttonText = null;
            $this->buttonLink = null;
        } else if ($receiver === 'admin') {
            $this->title = $productReservation->product->seller->visible_name . ' ha recibido una solicitud de reserva';
            $this->text = $productReservation->product->seller->visible_name . ' ha recibido una solicitud de reserva para <strong>' . $productReservation->product->name . '</strong>. Una vez que el vendedor apruebe o rechace  la solicitud, el cliente recibira un mensaje.';
            $this->text .= '<br><br>';
            $this->text .= '<b>Información de la reserva</b>';
            $this->text .= '<br><br>';
            $this->text .= '<b>Servicio:</b> ' . $productReservation->product->name . '<br>';
            $this->text .= '<b>Precio:</b> ' . currencyFormat($productReservation->price, 'CLP', true) . '<br>';
            $this->text .= '<b>Nombre:</b> ' . $productReservation->name . '<br>';
            $this->text .= '<b>Correo:</b> ' . $productReservation->email . '<br>';
            $this->text .= '<b>Teléfono:</b> ' . $productReservation->cellphone . '<br>';

            if ($productReservation->product->is_tour) {
                $this->text .= '<b>Fecha de la experiencia:</b> ' . Carbon::parse($productReservation->check_in_date)->format('d/m/Y h:i a ') . '<br>';
            }

            if ($productReservation->product->is_housing) {
                $this->text .= '<b>Fecha de Check In:</b> ' . $productReservation->check_in_date->format('d/m/Y') . '<br>';
                $this->text .= '<b>Fecha de Check Out:</b> ' . $productReservation->check_out_date->format('d/m/Y') . '<br>';
            }

            $this->text .= '<b>Cantidad de adultos:</b> ' . $productReservation->adults_number . '<br>';
            $this->text .= '<b>Cantidad de niños:</b> ' . $productReservation->childrens_number . '<br>';
            $this->buttonText = null;
            $this->buttonLink = null;
        }
        
        $this->rejectedText = '';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->receiver === 'seller') {
            return $this->subject('Tienes una nueva solicitud de reserva')->view('maileclipse::templates.basicEmailTemplate');
        } else if ($this->receiver === 'customer') {
            return $this->subject('Tu solicitud de reserva ha sido enviada')->view('maileclipse::templates.basicEmailTemplate');
        } else if ($this->receiver == 'admin') {
            return $this->subject($this->productReservation->product->seller->visible_name . ' ha recibido una solicitud de reserva')->view('maileclipse::templates.basicEmailTemplate');

        }
    }
}
