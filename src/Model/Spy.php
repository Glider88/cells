<?php declare(strict_types=1);

namespace Cells\Model;

use Cells\Runtime\Response\WritableStreamResponseInterface;

readonly class Spy
{
    private const int CELL_WIDTH = 10;
    private const int CELL_HEIGHT = 10;

    public function __construct(
        private WritableStreamResponseInterface $response,
    ) {}

    public function move(Cell $cell, Point $oldPoint, Point $newPoint): void
    {
        $newCell = self::jsonCell($cell->id, $newPoint);
        $this->response->write($newCell);
    }

    public static function jsonCell(int $id, Point $point): array
    {
        return [
            'id' => $id,
            'x' => $point->x * self::CELL_WIDTH,
            'y' => $point->y * self::CELL_HEIGHT,
        ];
    }
}
