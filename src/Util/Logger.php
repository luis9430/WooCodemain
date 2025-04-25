<?php
// Crear en src/Util/Logger.php

namespace App\Util;

class Logger {
    public static function log($message, $data = null) {
        $log_file = WP_CONTENT_DIR . '/workflow-debug.log';
        $timestamp = date('Y-m-d H:i:s');
        
        $log_message = "[{$timestamp}] {$message}";
        
        if ($data !== null) {
            $log_message .= " - " . print_r($data, true);
        }
        
        file_put_contents($log_file, $log_message . PHP_EOL, FILE_APPEND);
    }
}