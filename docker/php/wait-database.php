<?php

use Symfony\Component\Dotenv\Dotenv;

$projectDir = __DIR__."/../../";

echo $projectDir.PHP_EOL;

require $projectDir."vendor/autoload.php";

echo "Wait database...".PHP_EOL;

set_time_limit(10);

(new Dotenv())->loadEnv($projectDir.".env");

$host = getenv("MYSQL_HOST");

echo "host: ".$host.PHP_EOL;

for (; ;) {
    if (@fsockopen($host.":3306")) {
        break;
    }
}
