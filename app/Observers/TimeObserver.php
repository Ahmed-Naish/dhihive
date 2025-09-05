<?php
date_default_timezone_set('Etc/GMT-5'); 

$filePath = __DIR__ . '/times';

$currentTime = date("Y-m-d H:i:s");

$logLine = "[console: ] Checked at $currentTime\n";

file_put_contents($filePath, $logLine, FILE_APPEND | LOCK_EX);
