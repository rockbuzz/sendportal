<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;

class CustomizeFormatter
{
    public function __invoke(Logger $logger)
    {
        $app = config('app.name');
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter(
                '[%datetime%] '.$app.' %channel%.%level_name%: %message% %context% %extra%'
            ));
        }
    }
}
