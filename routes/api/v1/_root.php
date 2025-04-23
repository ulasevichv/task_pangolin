<?php

use App\Helpers\FileHelper;

defined('API_VERSION_PREFIX') or define('API_VERSION_PREFIX', 'api/v1');

$filePaths = FileHelper::getFileNames(__DIR__, true, true);
foreach ($filePaths as $filePath) {
    if ($filePath === __FILE__) {
        continue;
    }
    include($filePath);
}