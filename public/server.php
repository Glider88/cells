<?php declare(strict_types=1);

use Cells\Runtime\AmpServer;
use Cells\Runtime\SwooleServer;
use Cells\Runtime\SyncPhpServer;

require  __DIR__ . '/../vendor/autoload.php';

//SyncPhpServer::run();
SwooleServer::run();
//AmpServer::run();
