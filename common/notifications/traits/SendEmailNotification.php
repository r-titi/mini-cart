<?php

namespace common\notifications\traits;

use common\models\forms\SendEmailForm;

trait SendEmailNotification {
    
    /**
     * @param User[] $notifiables users to send notifications
     * @param array $data notification data
     */
    public function sendEmailNotification(array $notifiables, array $data) {
        foreach($notifiables as $notifiable) {
            $sendSellerEmail = new SendEmailForm();
            $sendSellerEmail->name = $notifiable->username;
            $sendSellerEmail->email = $notifiable->email;
            $sendSellerEmail->subject = $data['subject'];
            $sendSellerEmail->view = ['html' => $data['view']];
            $sendSellerEmail->params = [
                'name' => $notifiable->username,
                'customer_name' => $data['shipping']->first_name . ' ' . $data['shipping']->last_name,
                'order_items_amount' => $data['order_items_amount'],
                'address' => $data['shipping']->address,
                'order' => $data['object']
            ];
            return $sendSellerEmail->send();
        }
    }
}