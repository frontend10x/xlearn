<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmationRegisterRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $API_VERSION = env('API_VERSION');
        $URL_BASE = ( env('AMBIENT') === 'DEV' ) ? env('URL_BASE_LOCAL') : env('URL_BASE_PRODUCTION');

        $urlConfimation = $URL_BASE . '/api/' . $API_VERSION . '/user/changestate/' . $this->id;
        return $this->view('mails.confirmation_register')->with('urlConfimation', $urlConfimation);
    }
}
