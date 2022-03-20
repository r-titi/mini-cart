<?php

namespace common\helpers\notification\contracts;

interface MailNotificationContract extends NotificationContract {
    
    public function viaMail();

    public function sendViaMail(array $notifiables);
}