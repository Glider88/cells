# World of Cells

Points move randomly within the boundaries of their world.
Data comes from SSE.
Different backends: swoole, amphp and simple php sync.

![](/assets/screenshot.png)

## Start docker

First time:
```shell
bin/reup
```

Next times:
```shell
bin/up
```

## Start app

Interface:

```shell
bin/php -S 0.0.0.0:9501 -t public/.
```

Backend Swoole and Amphp (edit public/server.php for switching):
```shell
bin/php -dxdebug.mode=off public/server.php
```

Sync php:
```shell
bin/php -S 0.0.0.0:9502 public/server.php
bin/kill
```
