<?php

namespace common\notifications\traits;

use common\models\Notification;

trait SendDatabaseNotification {
    
     /**
     * @param User[] $notifiables users to send notifications
     * @param array $data notification data
     */
    public function sendDatabaseNotification(array $notifiables, array $data) {
        foreach($notifiables as $notifiable) {
            $sendNotification = new Notification();
            $sendNotification->user_id = $notifiable->id;
            $sendNotification->subject = $data['subject'];
            $sendNotification->body = $data['body'];
            $sendNotification->data = json_encode($data['object']);
            return $sendNotification->save();
        }
    }
}