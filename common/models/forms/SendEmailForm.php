<?php

namespace common\models\forms;

use Yii;
use yii\base\Model;

class SendEmailForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $view;
    public $params;

    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],

            ['subject', 'trim'],
            ['subject', 'required'],
            ['subject', 'string', 'min' => 2, 'max' => 255],

            ['body', 'trim'],
            ['body', 'required'],
            ['body', 'text'],
        ];
    }

    public function send()
    {
        return Yii::$app->mailer->compose($this->view, $this->params)
            ->setTo([$this->email => $this->name])
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
    }
}
