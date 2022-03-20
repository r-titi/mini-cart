<?php

namespace frontend\models;

use common\models\forms\SendEmailForm;
use Yii;
use yii\base\Model;
use common\models\User;
use yii\base\InvalidParamException;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        $auth = Yii::$app->authManager;
        $roleObject = $auth->getRole('user');
        if (!$roleObject) {  
            throw new InvalidParamException("There is no role user");
        }

        return $user->save() && $this->sendWelcomeEmail() && $this->sendVerifyEmail($user) && $auth->assign($roleObject, $user->id);
    }

    protected function sendWelcomeEmail() {
        $sendEmail = new SendEmailForm;
        $sendEmail->name = $this->username;
        $sendEmail->email = $this->email;
        $sendEmail->subject = 'Welcome Email';
        $sendEmail->view =  ['html' => 'welcomeEmail-html'];
        $sendEmail->params =  ['username' => $this->username];
        return $sendEmail->send();
    }

    protected function sendVerifyEmail($user) {
        $sendEmail = new SendEmailForm;
        $sendEmail->name = $this->username;
        $sendEmail->email = $this->email;
        $sendEmail->subject = 'Account registration at ' . Yii::$app->name;
        $sendEmail->view =  ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'];
        $sendEmail->params = ['user' => $user];
        return $sendEmail->send();
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
