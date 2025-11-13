<?php declare(strict_types=1);

namespace Cells\Runtime;

use Cells\Model\Cell;
use Cells\Model\Spy;
use Cells\Model\State;
use Cells\Model\World;

readonly class SyncPhpServer
{
    public static function run(): never
    {
        $sseResponse = new PhpSseResponse();
        $cells = World::randomCells(1000, $sseResponse, 50, 50);
        $serializeFn = static fn(Cell $c) => Spy::jsonCell($c->id, $c->point);
        $sseResponse->write(['init' => array_map($serializeFn, $cells)]);
        $world = new World(new State($cells), 50, 50);

        sleep(1);

        while (true) {
            foreach ($cells as $cell) {
                $cell->move($world);
                usleep(1000);
            }
        }
    }
}

