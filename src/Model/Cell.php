<?php declare(strict_types=1);

namespace Cells\Model;

use Cells\Helper\Arr;

readonly class Cell
{
    public function __construct(
        public int $id,
        public Point $point,
        private Spy $spy,
    ) {}

    public function move(World $world): void
    {
        $nearestPoints = $world->nearestPoints($this->point, 1);
        $nearestCells = $world->cells($nearestPoints);

        $possiblePoints = $this->possibleMovePoints($nearestPoints, $nearestCells);

        if (empty($possiblePoints)) {
            $newPoint = $this->point;
        } else {
            $newPoint = Arr::rand($possiblePoints);
        }

        $this->spy->move($this, $this->point, $newPoint);
        $world->move($this->point, $newPoint);
    }

    /**
     * @param array<Point> $nearestPoints
     * @param array<Cell> $nearestCells
     * @return array<Point>
     */
    private function possibleMovePoints(array $nearestPoints, array $nearestCells): array
    {
        $equalFn = static fn(Point $p1, Point $p2) => $p1->x === $p2->x && $p1->y === $p2->y;

        $possiblePoints = [];
        foreach ($nearestPoints as $nearestPoint) {
            $equalToNearestPoint = static fn(Cell $cell) => $equalFn($cell->point, $nearestPoint);
            if (array_any($nearestCells, $equalToNearestPoint)) {
                continue;
            }

            $possiblePoints[] = $nearestPoint;
        }

        return $possiblePoints;
    }
}
