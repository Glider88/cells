<?php declare(strict_types=1);

namespace Cells\Runtime;

use Cells\Model\Cell;
use Cells\Model\Spy;
use Cells\Model\State;
use Cells\Model\World;
use Cells\Runtime\Response\SwooleSseResponse;
use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

readonly class SwooleServer
{
    public static function run(): void
    {
        $server = new Server('0.0.0.0', 9502);
        $server->set([
            'enable_coroutine'   => true,
            'max_coroutine'      => 10000, // worker_num*10000
            'reactor_num'        => swoole_cpu_num() * 2,
            'worker_num'         => swoole_cpu_num() * 2,
            'max_request'        => 100000,
            'buffer_output_size' => 4 * 1024 * 1024, // 4MB
            'log_level'          => SWOOLE_LOG_WARNING,
//            'log_file'           => __DIR__ . '/../../logs/swoole.log',
        ]);


        $server->on('request', static function (Request $request, Response $response) {
            $sseResponse = new SwooleSseResponse($response, 'cells');
            $cells = World::randomCells(1000, $sseResponse, 50, 50);
            $serializeFn = static fn(Cell $c) => Spy::jsonCell($c->id, $c->point);
            $sseResponse->write(['init' => array_map($serializeFn, $cells)]);
            $world = new World(new State($cells), 50, 50);

            Coroutine::sleep(1.0);

            $coroutines = [];
            foreach ($cells as $cell) {
                $coroutines[] = go(static function () use ($cell, $world) {
                    while (true) {
                        $cell->move($world);
                        $delay = random_int(1, 1000) / 1000;
                        Coroutine::sleep($delay);
                    }
                });
            }

            Coroutine::join($coroutines, -1);
        });

        $server->start();
    }
}
