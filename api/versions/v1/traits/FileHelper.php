<?php

namespace api\versions\v1\traits;

trait FileHelper {
    public function deleteFile($file_path) {
        if($file_path != '' && file_exists($file_path)) 
            unlink($file_path);
    }
}