<?php

namespace common\notifications;

use common\helpers\notification\abstracts\BaseNotification;
use common\helpers\notification\contracts\DatabaseNotificationContract;
use common\helpers\notification\contracts\MailNotificationContract;
use common\notifications\traits\SendDatabaseNotification;
use common\models\Order;
use common\notifications\traits\SendEmailNotification;

class OrderNotification extends BaseNotification implements MailNotificationContract, DatabaseNotificationContract {

    use SendDatabaseNotification, SendEmailNotification;

    private array $data;
    private Order $order;

    public function __construct(array $data, $order) {
        $this->data = $data;
        $this->order = $order;
    }

    /**
     * notification config for mail channel
     */
    public function viaMail() {
        return [
            'view'     => 'orderSellerEmail-html',
            'subject'  => $this->data['subject'],
            'body'     => $this->data['body'],
            'object'   => $this->order,
            'shipping' => $this->data['shipping'],
            'order_items_amount' => $this->data['order_items_amount']
        ];
    }

    public function sendViaMail($notifiables) {
        return $this->sendEmailNotification($notifiables, $this->viaMail());
    }
    
    /**
     * notification config for database channel
     */
    public function viaDatabase() {
        return [
            'subject' => $this->data['subject'],
            'body'    => $this->data['body'],
            'object'  => $this->order
        ];
    }

    public function sendViaDatabase($notifiables) {
        return $this->sendDatabaseNotification($notifiables, $this->viaDatabase());
    }
}