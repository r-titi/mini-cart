<?php

namespace common\helpers\notification\abstracts;

abstract class BaseNotification {

    private $method_prefix = 'sendVia';

    /**
     * @param User[] $notifiables users to send notifications
     * @param array $allowedChannels allowed channels
    */
    public function send(array $notifiables, array $allowedChannels) {
        foreach($allowedChannels as $channel) {
            $channel = $this->method_prefix . ucfirst($channel);
            $this->$channel($notifiables);
        }
    }
}