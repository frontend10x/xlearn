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

    public $id;
    public $typeMail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id, $typeMail)
    {
        $this->id = $id;
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
                
                $urlConfimation = URL_BASE . '/api/' . API_VERSION . '/user/changestate/' . $this->id;
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
                
            
            default:
                # code...
                break;
        }

    }
}
