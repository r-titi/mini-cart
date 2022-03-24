<?php

namespace common\traits;

use common\models\forms\SendEmailForm;

trait EmailHelper {

    public function sendEmail(array $data) {
        $sendEmail = new SendEmailForm();
        $sendEmail->name = $data['name'];
        $sendEmail->email = $data['email'];
        $sendEmail->subject = $data['subject'];
        $sendEmail->view = ['html' => $data['view']];
        $sendEmail->params = $data['params'];
        return $sendEmail->send();
    }

    public function sendUserOrderEmail($email, $order, $shipping) {
        $sendEmail = new SendEmailForm();
        $sendEmail->name = $shipping->first_name . ' ' . $shipping->last_name;
        $sendEmail->email = $email;
        $sendEmail->subject = 'Order Placed Successfully';
        $sendEmail->view = ['html' => 'orderUserEmail-html'];
        $sendEmail->params = [
            'name' => $shipping->first_name . ' ' . $shipping->last_name,
            'address' => $shipping->address,
            'order' => $order,
        ];
        return $sendEmail->send();
    }

    public function sendSellerOrderEmail($name, $email, $order_items_amount, $order, $shipping) {
        $sendSellerEmail = new SendEmailForm();
        $sendSellerEmail->name = $name;
        $sendSellerEmail->email = $email;
        $sendSellerEmail->subject = 'A new order placed on your items';
        $sendSellerEmail->view = ['html' => 'orderSellerEmail-html'];
        $sendSellerEmail->params = [
            'name' => $name,
            'customer_name' => $shipping->first_name . ' ' . $shipping->last_name,
            'order_items_amount' => $order_items_amount,
            'address' => $shipping->address,
            'order' => $order,
        ];
        return $sendSellerEmail->send();
    }
}