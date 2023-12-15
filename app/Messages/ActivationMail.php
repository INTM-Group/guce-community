<?php

namespace App\Messages;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationMail extends Mailable
{
  use Queueable, SerializesModels;

  public $user = null;
  public $creator = null;


  /**
   * Create a new message instance.
   *
   * @param  \App\Models\Order  $order
   * @return void
   */
  public function __construct(User $user, User $creator)
  {
    $this->user = $user;
    $this->creator = $creator;
    $this->subject('Activation de votre compte '.env('APP_NAME', 'GUCE'));
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->markdown('mails.activation');
  }
}
