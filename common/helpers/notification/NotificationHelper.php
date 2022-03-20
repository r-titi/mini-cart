<?php 

namespace common\helpers\notification;

use common\helpers\notification\abstracts\BaseNotification;
use common\models\User;
use yii\base\InvalidConfigException;

class NotificationHelper {

    public const CHANNEL_MAIL = 'mail';
    public const CHANNEL_DATABASE = 'database';
    private $allowed_channels = [self::CHANNEL_MAIL, self::CHANNEL_DATABASE];
    private array $channels;

    /**
     * @param array $channels available channels to use
     */
    public function __construct(array $channels) {
        $this->channels = $channels;
        $this->validateChannels();
    }

    private function validateChannels() {
        foreach($this->channels as $channel) {
            if(!in_array($channel, $this->allowed_channels)) {
                throw new InvalidConfigException(implode(' & ', $this->channels) . ' are only allowed channels');
            }
        }
    }

    /**
     * @param User[] $notifiables users to send notifications
     * @param BaseNotification $notification notification to send
     */
    public function send(array $notifiables, BaseNotification $notification) {
        $notification->send($notifiables, $this->allowed_channels);
    }
}