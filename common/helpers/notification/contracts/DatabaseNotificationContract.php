<?php

namespace common\helpers\notification\contracts;

interface DatabaseNotificationContract extends NotificationContract {

    public function viaDatabase();

    public function sendViaDatabase(array $notifiables);
}