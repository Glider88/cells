<?php declare(strict_types=1);

namespace Cells\Runtime\Response;

interface WritableStreamResponseInterface
{
    public function write(array $data): void;
}
