<?php

namespace App\Messages;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
  use Queueable, SerializesModels;

  public $user = null;


  /**
   * Create a new message instance.
   *
   * @param  \App\Models\Order  $order
   * @return void
   */
  public function __construct(User $user)
  {
    $this->user = $user;
    $this->subject('Réinitialization de votre mot de passe '.env('APP_NAME', 'GUCE'));
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->markdown('mails.reset');
  }
}
