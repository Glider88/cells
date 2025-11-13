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

Backend Swoole:
```shell
bin/php -dxdebug.mode=off public/server.php
```

Other:
```shell
bin/php -S 0.0.0.0:9502 public/server.php
bin/sh ps aux | grep 'public/server.php' | awk '{print $1}' | args bin/sh kill -9 {0}
```
