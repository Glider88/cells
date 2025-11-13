<?php declare(strict_types=1);

namespace Cells\Model;

readonly class Point
{
    public function __construct(
        public int $x,
        public int $y,
    ) {}
}
