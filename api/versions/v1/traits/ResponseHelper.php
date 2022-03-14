<?php

namespace api\versions\v1\traits;

trait ResponseHelper {
    public function sendResponse($code = 200, $msg, $data) {

        $this->response->setStatusCode($code);
        return [
            'message' => $msg,
            'data' => $data
        ];
    }
}