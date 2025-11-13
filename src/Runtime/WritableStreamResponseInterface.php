<?php declare(strict_types=1);

namespace Cells\Runtime;

interface WritableStreamResponseInterface
{
    public function write(array $data): void;
}
