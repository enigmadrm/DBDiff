<?php namespace DBDiff;


class Logger {

    public static function error($msg, $die=false) {
        if (DBDiff::$logging) {
            echo "\033[0;31m✖ ".$msg."\033[0m\n";
        }
        if ($die) die();
    }

    public static function success($msg) {
        if (DBDiff::$logging) {
            echo "\033[0;32m✔ ".$msg."\033[0m\n";
        }
    }

    public static function info($msg) {
        if (DBDiff::$logging) {
            echo "\033[0;36mℹ ".$msg."\033[0m\n";
        }
    }
}
