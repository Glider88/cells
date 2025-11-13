<?php declare(strict_types=1);

namespace Cells\Model;

use Cells\Helper\Arr;
use Cells\Runtime\WritableStreamResponseInterface;

readonly class World
{
    const int MIN_X = 0;
    const int MIN_Y = 0;

    public function __construct(
        private State $state,
        private int $width,
        private int $height,
    ) {}

    /**
     * @param array<Point> $points
     * @return array<Cell>
     */
    public function cells(array $points): array
    {
        return array_filter(array_map(fn(Point $point) => $this->state->get($point), $points));
    }

    public function move(Point $oldPoint, Point $newPoint): void
    {
        $cell = $this->state->get($oldPoint);
        if ($cell !== null) {
            $this->state->del($oldPoint);
            $this->state->set($newPoint, $cell);
        }
    }

    /** @return array<Point> */
    public function nearestPoints(Point $currentPoint, int $radius): array
    {
        if ($radius <= 0) {
            return [];
        }

        $inBorderX = fn (int $x) => $x >= self::MIN_X && $x < self::MIN_X + $this->width;
        $inBorderY = fn (int $y) => $y >= self::MIN_Y && $y < self::MIN_Y + $this->height;

        $allXs = range($currentPoint->x - $radius, $currentPoint->x + $radius);
        $possibleXs = array_filter($allXs, $inBorderX);

        $allYs = range($currentPoint->y - $radius, $currentPoint->y + $radius);
        $possibleYs = array_filter($allYs, $inBorderY);

        $makePoint = static fn(array $xy) => new Point($xy[0], $xy[1]);

        return array_map($makePoint, Arr::cartesian([$possibleXs, $possibleYs]));
    }

    /** @return list<Cell> */
    public static function randomCells(
        int $number,
        WritableStreamResponseInterface $stream,
        int $width,
        int $height,
    ): array {

        if ($number >= $width * $height) {
            throw new \InvalidArgumentException("Too many cells: $number in " . ($width * $height));
        }

        $cells = [];
        $i = 1;
        while (true) {
            $x = random_int(0, $width - 1);
            $y = random_int(0, $height - 1);

            $cells[] = [
                'id' => $i,
                'x' => $x,
                'y' => $y,
            ];
            $i += 1;

            if (count($cells) >= $number) {
                break;
            }
        }

        $result = [];
        foreach ($cells as $c) {
            $result[] = new Cell($c['id'], new Point($c['x'], $c['y']), new Spy($stream));
        }

        return $result;
    }
}
