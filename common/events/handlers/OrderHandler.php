<?php

namespace common\events\handlers;

use common\events\OrderEvent;
use common\helpers\notification\NotificationHelper;
use common\helpers\PusherHelper;
use common\jobs\SendNotificationJob;
use common\notifications\OrderNotification;
use Yii;

class OrderHandler {
    const EVENT_SUBMIT_ORDER = 'submit-order';

    public static function handleSubmitOrder(OrderEvent $event) {
        self::notifyViaHelper($event);
        self::notifyViaPusher($event);
        // Yii::$app->queue->push(new SendNotificationJob([
        //     'order_event' => $event
        // ]));
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
        $pusherHelper->trigger('seller-' . $event->user->id, self::EVENT_SUBMIT_ORDER, $data);
    }
}