<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Carbon;
use App\Models\ProductReservation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductReservationChangeStatus extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $text;
    public $rejectedText;
    public $buttonText;
    public $buttonLink;
    public $logo;
    public $receiver;
    public $status;
    public $sellerName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($productReservation, string $receiver, string $status)
    {
        $this->sellerName = $productReservation->product->seller->visible_name;

        $this->status = $status;

        $this->receiver = $receiver;

        $this->logo = 'img/logo-pyme.png';

        $this->rejectedText = '';

        switch ($status) {
            case ProductReservation::ACCEPTED_STATUS:
                if ($receiver === 'customer') {
                    $this->title = 'Tu solicitud de reserva ha sido aprobada';
                    $this->text = 'Tu solicitud de reserva para <strong>' . $productReservation->product->name . '</strong> ha sido aprobada. Abajo encontrarás la información de la reserva y un enlace con el cual podrás completar el pago.';
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
                    
                    if ($productReservation->seller_comment) {
                        $this->text .= '<br><br>';
                        $this->text .= '<b>Comentario del vendedor:</b>';
                        $this->text .= '<br>';
                        $this->text .= $productReservation->seller_comment;
                    }

                    $this->buttonText = 'Completar pago';
                    $this->buttonLink = route('product-reservation.add-to-cart', ['hash' => $productReservation->hash]);
                } elseif ($receiver === 'admin') {
                    $this->title =  $this->sellerName . ' ha aprobado una solicitud de reserva';
                    $this->text = $this->sellerName . ' ha aprobado una solicitud de reserva para <strong>' . $productReservation->product->name . '</strong>. Abajo encontrarás la información de la reserva.';
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
                    
                    if ($productReservation->seller_comment) {
                        $this->text .= '<br><br>';
                        $this->text .= '<b>Comentario del vendedor:</b>';
                        $this->text .= '<br>';
                        $this->text .= $productReservation->seller_comment;
                    }

                    $this->buttonText = null;
                    $this->buttonLink = null;
                }
                break;

            case ProductReservation::REJECTED_STATUS:
                if ($receiver === 'customer') {
                    $this->title = 'Tu solicitud de reserva ha sido rechazada';
                    $this->text = 'Tu solicitud de reserva para <strong>' . $productReservation->product->name . '</strong> ha sido rechazada. Si deseas conocer mas detalles sobre el motivo de rechazo, contacta directamente con el vendedor';
                } elseif ($receiver === 'admin') {
                    $this->title = $this->sellerName . ' ha rechazado una solicitud de reserva';
                    $this->text = $this->sellerName . ' ha rechazado una solicitud de reserva para <strong>' . $productReservation->product->name . '</strong>. Si deseas conocer mas detalles sobre el motivo de rechazo, contacta directamente con el vendedor';
                }

                if ($productReservation->seller_comment) {
                    $this->text .= '<br><br>';
                    $this->text .= '<b>Comentario del vendedor:</b>';
                    $this->text .= '<br>';
                    $this->text .= $productReservation->seller_comment;
                }

                break;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        switch ($this->status) {
            case ProductReservation::ACCEPTED_STATUS:
                if ($this->receiver === 'customer') {
                    return $this->subject('Tu solicitud de reserva ha sido aprobada')->view('maileclipse::templates.basicEmailTemplate');
                } elseif ($this->receiver === 'admin') {
                    return $this->subject($this->sellerName . ' ha aprobado una solicitud de reserva')->view('maileclipse::templates.basicEmailTemplate');
                }
                break;
            case ProductReservation::REJECTED_STATUS:
                if ($this->receiver === 'customer') {
                    return $this->subject('Tu solicitud de reserva ha sido rechazada')->view('maileclipse::templates.basicEmailTemplate');
                } elseif ($this->receiver === 'admin') {
                    return $this->subject($this->sellerName . ' ha rechazado una solicitud de reserva')->view('maileclipse::templates.basicEmailTemplate');
                }
                break;
        }
    }
}
