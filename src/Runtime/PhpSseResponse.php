<?php declare(strict_types=1);

namespace Cells\Runtime;

readonly class PhpSseResponse implements WritableStreamResponseInterface
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        set_time_limit(0);   // making maximum execution time unlimited
        ob_implicit_flush();          // Send content immediately to the browser on every statement which produces output
        ob_end_flush();               // deletes the topmost output buffer and outputs all of its contents
    }

    public function write(array $data): void
    {
        $dataJson = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        $result = <<<SSE
event: cells
data: $dataJson


SSE;

        echo $result;
        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }
}
