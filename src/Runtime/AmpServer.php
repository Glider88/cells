<?php declare(strict_types=1);

namespace Cells\Runtime;

use Amp\ByteStream;
use Amp\ByteStream\ReadableIterableStream;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Pipeline\Queue;
use Cells\Runtime\Response\AmpResponse;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Amp\Future;
use Amp\Http\HttpStatus;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\SocketHttpServer;
use Cells\Model\Cell;
use Cells\Model\Spy;
use Cells\Model\State;
use Cells\Model\World;
use function Amp\async;
use function Amp\delay;
use function Amp\trapSignal;


readonly class AmpServer
{
    public static function run(): void
    {
        $logHandler = new StreamHandler(ByteStream\getStdout());
        $logHandler->pushProcessor(new PsrLogMessageProcessor());
        $logHandler->setFormatter(new ConsoleFormatter);
        $logger = new Logger('amp-server');
        $logger->pushHandler($logHandler);

        $requestHandler = new class() implements RequestHandler {
            public function handleRequest(Request $request) : Response
            {
                return new Response(
                    status: HttpStatus::OK,
                    headers: [
                        'Access-Control-Allow-Origin' => '*',
                        'Content-Type' => 'text/event-stream',
                        'Cache-Control' => 'no-cache',
                        'Connection' => 'keep-alive',
                        'X-Accel-Buffering' => 'no',
                    ],
                    body: new ReadableIterableStream((static function () {
                        $queue = new Queue();
                        $sseResponse = new AmpResponse($queue);
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

                        foreach ($queue->iterate() as $value) {
                            yield $value;
                        }

                        Future\await($coroutines);
                    })())
                );
            }
        };

        $server = SocketHttpServer::createForDirectAccess($logger);
        $server->expose('0.0.0.0:9502');
        $server->start($requestHandler, new DefaultErrorHandler());

        trapSignal([SIGHUP, SIGINT, SIGQUIT, SIGTERM]);

        $server->stop();
    }
}
