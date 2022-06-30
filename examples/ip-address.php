<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use SharkMachine\Lib\StopForumSpan\Client;
use Symfony\Component\HttpClient\Psr18Client;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$client = new Client(new Psr18Client(), new Psr17Factory(), new Psr17Factory());
$isSpammer = $client->ipAddressInList('192.210.195.133');
echo ($isSpammer->isAppears() ? 'true' : 'false') . "\n";
