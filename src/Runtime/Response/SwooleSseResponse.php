<?php declare(strict_types=1);

namespace Cells\Runtime\Response;

use Swoole\Http\Response;

readonly class SwooleSseResponse implements WritableStreamResponseInterface
{
    public function __construct(
        private Response $response,
        private string $topic,
    ) {
        $this->response->header('Access-Control-Allow-Origin', '*');
        $this->response->header('Content-Type', 'text/event-stream');
        $this->response->header('Cache-Control', 'no-cache');
        $this->response->header('Connection', 'keep-alive');
        $this->response->header('X-Accel-Buffering', 'no');
    }

    public function write(array $data): void
    {
        $dataJson = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        $result = <<<SSE
event: $this->topic
data: $dataJson


SSE;

        $this->response->write($result);
    }
}
