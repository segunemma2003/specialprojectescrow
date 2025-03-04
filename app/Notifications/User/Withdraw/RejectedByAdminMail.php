<?php

namespace App\Notifications\User\Withdraw;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class RejectedByAdminMail extends Notification
{
    use Queueable;

    public $user;
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$data)
    {
        $this->user = $user;
        $this->data = $data;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $user = $this->user;
        $data = $this->data;
        $trx_id = $this->data->trx_id;
        $date = Carbon::now();
        $dateTime = $date->format('Y-m-d h:i:s A');
            return (new MailMessage)
            ->greeting("Hello ".$user->fullname." !")
            ->subject("Withdraw Money Via ". @$data->gateway_currency->name."Rejected by admin")
            ->line("Your withdraw money request rejected successfully via ".@$data->gateway_currency->name." , details of withdraw money:")
            ->line("Request Amount: " . get_amount(@$data->sender_request_amount,@$data->sender_currency_code))
            ->line("Exchange Rate: " ." 1 ". @$data->sender_currency_code.' = '. get_amount(@$data->exchange_rate,$data->gateway_currency->currency_code))
            ->line("Fees & Charges: " .get_amount( @$data->transaction_details->total_charge,2).' '. @$data->gateway_currency->currency_code)
            ->line("Will Get: " . get_amount(@$data->sender_request_amount,$data->sender_currency_code))
            ->line("Total Payable Amount: " . get_amount(@$data->total_payable,@$data->gateway_currency->currency_code))
            ->line("Transaction Id: " .@$data->trx_id)
            ->line("Status: Success")
            ->line("Date And Time: " .@$dateTime)
            ->line('Thank you for using our application!'); 
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
