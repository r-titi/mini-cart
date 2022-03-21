<?php

namespace common\jobs;

use common\helpers\notification\NotificationHelper;
use common\helpers\PusherHelper;
use common\notifications\OrderNotification;
use yii\base\BaseObject;

class SendNotificationJob extends BaseObject implements \yii\queue\JobInterface
{
    private $order_event;
    
    public function execute($queue)
    {
        $this->notifyViaHelper($this->order_event);
        $this->notifyViaPusher($this->order_event);
    }

    public function notifyViaHelper($event) {
        $notificationHelper = new NotificationHelper(['mail', 'database']);
        $data = [
            'subject' => 'New Order',
            'body' => $event->shipping->first_name . ' ' . $event->shipping->last_name . ' submit new order',
            'order_items_amount' => $event->amount,
            'shipping' => $event->shipping
        ];

        $notification = new OrderNotification($data, $event->order);
        $notificationHelper->send([$event->user], $notification);
    }

    public function notifyViaPusher($event) {
        $data['message'] = 'New Order';
        $data['body'] = $event->shipping->first_name . ' ' . $event->shipping->last_name . ' submit new order';
        $pusherHelper = new PusherHelper();
        $pusherHelper->trigger('seller-' . $event->user->id, 'submit-order', $data);
    }
}