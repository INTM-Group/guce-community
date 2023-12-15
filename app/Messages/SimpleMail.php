<?php

namespace App\Messages;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SimpleMail extends Mailable
{
  use Queueable, SerializesModels;

  protected $data = [];

  /**
   * Create a new message instance.
   *
   * @param  \App\Models\Order  $order
   * @return void
   */
  public function __construct($data = [])
  {
    $this->data = $data;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->markdown('mails.simple', $this->data);
  }
}
