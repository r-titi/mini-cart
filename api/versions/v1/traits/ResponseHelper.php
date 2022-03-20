<?php

namespace api\versions\v1\traits;

trait ResponseHelper {
    public function sendResponse($msg, $data, $code = 200) {
        $this->response->setStatusCode($code);
        return [
            'message' => $msg,
            'data' => ($code < 400) ? $data : ['errors' => $data]
        ];
    }
}