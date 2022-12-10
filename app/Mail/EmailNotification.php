<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

define('URL_BASE', validate_environment()['URL_BASE']);
define('URL_FRONT', validate_environment()['URL_FRONT']);

class EmailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $typeMail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $typeMail)
    {
        $this->data = $data;
        $this->typeMail = $typeMail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        switch ($this->typeMail) {

            case 'confirmation_register':
                
                $urlConfimation = URL_BASE . '/api/' . API_VERSION . '/user/changestate/' . $this->data;
                return $this->view('mails.confirmation_register')->with('urlConfimation', $urlConfimation);
                
                break;
            
            case 'assigned_courses':
            
                $urlConfimation = URL_FRONT . '/login';
                return $this->view('mails.assigned_courses')->with('urlConfimation', $urlConfimation);
                
                break;
            
            case 'assigned_leader':
        
                $urlConfimation = URL_FRONT . '/login';
                return $this->view('mails.assigned_courses')->with('urlConfimation', $urlConfimation);
                
                break;
            
            case 'contact_us':

                return $this->view('mails.contact_us')->with('information', $this->data);

                break;
            
            case 'forgot_password':

                $urlRecover = URL_FRONT . '/recuperacion/' . $this->data;
                return $this->view('mails.forgot_password')->with('urlRecover', $urlRecover);

                break;
            
            case 'payment_register':

                $this->data['url'] = URL_FRONT . '/login';
                return $this->view('mails.payment_register')->with('paymentDetails', $this->data);

                break;

            default:
                # code...
                break;
        }

    }
}
