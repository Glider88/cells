# Start docker

First time:
```shell
bin/reup
```

Then:
```shell
bin/up
```

# Start app

Interface:

```shell
bin/php -S 0.0.0.0:9501 -t public/.
```

Backend Swoole and Amphp (switch in public/server.php):
```shell
bin/php -dxdebug.mode=off public/server.php
```


Sync php:
```shell
bin/php -S 0.0.0.0:9502 public/server.php
bin/kill
```
