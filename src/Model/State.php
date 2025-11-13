<?php declare(strict_types=1);

namespace Cells\Model;

class State
{
    /** @var array<string, Cell> */
    private array $cells;

    /** @param array<Cell> $cells */
    public function __construct(
        array $cells,
    ) {
        $keyToCell = [];
        foreach ($cells as $cell) {
            $key = $this->key($cell->point);
            $keyToCell[$key] = $cell;
        }

        $this->cells = $keyToCell;
    }

    public function set(Point $point, Cell $cell): void
    {
        $this->cells[$this->key($point)] = $cell;
    }

    public function get(Point $point): ?Cell
    {
        return $this->cells[$this->key($point)] ?? null;
    }

    public function del(Point $point): void
    {
        unset($this->cells[$this->key($point)]);
    }

    private function key(Point $point): string
    {
        return "$point->x:$point->y";
    }
}
