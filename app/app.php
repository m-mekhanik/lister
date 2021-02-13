<?php
namespace App;

require __DIR__ . '/../vendor/autoload.php';


    //PATH
define('APP_DIR','C:\php\lister\\');

use App\Components\ParseDirectoryComponent;

$app = new ParseDirectoryComponent();
$app->run();