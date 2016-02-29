<?php namespace App\Attendize\mailers;

use App\Models\Order;

class OrderMailer extends Mailer {
  
    
    public function sendOrderNotification(Order $order) {
        $this->sendTo($order->account->email, OUTGOING_EMAIL, OUTGOING_EMAIL_NAME, 'New order received on the event '. $order->event->title .' ['. $order->order_reference .']', 'Emails.OrderNotification', [
            'order' => $order
        ]);
    }
    
    public function sendOrderConfirmation(Order $order) {
        
        $ticket_pdf = public_path($order->ticket_pdf_path);
        
        if(!file_exists($ticket_pdf)){
            $ticket_pdf = FALSE;
        }
        
        $this->sendTo($order->email, OUTGOING_EMAIL, $order->event->organiser->name, 'Your tickets & order confirmation for the event '. $order->event->title .' ['. $order->order_reference .']', 'Emails.OrderConfirmation', [
            'order' => $order,
            'email_logo' => $order->event->organiser->full_logo_path
        ], $ticket_pdf);
    }
    
    public function sendTickets(Order $order) {
//       $this->sendTo($order->account->email, OUTGOING_EMAIL, OUTGOING_EMAIL_NAME, 'New order received on the event '. $order->event->title .' ['. $order->order_reference .']', 'Emails.OrderNotification', [
//            'order' => $order
//        ]);
    }
    
    
}
