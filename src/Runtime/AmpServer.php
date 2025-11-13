<?php declare(strict_types=1);

namespace Cells\Runtime;

use Amp\Future;
use Cells\Model\Cell;
use Cells\Model\Spy;
use Cells\Model\State;
use Cells\Model\World;
use function Amp\async;
use function Amp\delay;

readonly class AmpServer
{
    public static function run(): void
    {
        $sseResponse = new PhpSseResponse();
        $cells = World::randomCells(1000, $sseResponse, 50, 50);
        $serializeFn = static fn(Cell $c) => Spy::jsonCell($c->id, $c->point);
        $sseResponse->write(['init' => array_map($serializeFn, $cells)]);
        $world = new World(new State($cells), 50, 50);

        delay(1.0);

        $coroutines = [];
        foreach ($cells as $cell) {
            $coroutines[] = async(static function () use ($cell, $world): void {
                while (true) {
                    $cell->move($world);
                    $delay = random_int(1, 1000) / 1000;
                    delay($delay);
                }
            });
        }

        Future\await($coroutines);
    }
}
