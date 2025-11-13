<?php declare(strict_types=1);

namespace Cells\Runtime\Response;

use Amp\Pipeline\Queue;

readonly class AmpResponse implements WritableStreamResponseInterface
{
    public function __construct(
        private Queue $queue,
    ) {}

    public function write(array $data): void
    {
        $dataJson = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        $result = <<<SSE
event: cells
data: $dataJson


SSE;

        $this->queue->pushAsync($result);
    }
}
