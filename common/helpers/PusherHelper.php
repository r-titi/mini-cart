<?php 

namespace common\helpers;

use Pusher\Pusher;
use Yii;

class PusherHelper {
    
    private $pusher;

    public function __construct()
    {
        $options = array(
            'cluster' => 'ap2',
            'useTLS' => true
        );
       
        $this->pusher = new Pusher(
            Yii::$app->params['pusherAuthKey'],
            Yii::$app->params['pusherSecret'],
            Yii::$app->params['pusherAppId'],
            $options
        );
    }

    /**
     * @param String $channel channel title to send notification on it
     * @param String $event event name
     * @param array $data notification body 
     */
    public function trigger(String $channel, String $event, array $data) {
        $this->pusher->trigger($channel, $event, $data);
    }
}