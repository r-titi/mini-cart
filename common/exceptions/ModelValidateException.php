<?php

namespace common\exceptions;

use Throwable;
use yii\base\Exception;

class ModelValidateException extends Exception {
    
    public array $model_errors;

    public function __construct($message, $model_errors, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->model_errors = $model_errors;
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n ";
    }
}