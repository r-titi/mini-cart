<?php

namespace common\traits;

trait FileHelper {
    public function deleteFile($file_path) {
        if($file_path != '' && file_exists($file_path)) 
            unlink($file_path);
    }
}