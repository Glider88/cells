# World of Cells

Just playing with asynchronous php, comparing Swoole and Amphp.
Points move randomly within the boundaries of their world.
Data comes from SSE.

![](/assets/screenshot.png)

### Start docker:
```shell
bin/reup  # first time
```
```shell
bin/up  # next times
```

### Start app:

Interface:
```shell
bin/php -S 0.0.0.0:9501 -t public/.
```

One of the available backends:

- Swoole:
    ```shell
    bin/php -dxdebug.mode=off public/swoole_server.php
    ```

- Amphp:
    ```shell
    bin/php -dxdebug.mode=off public/amphp_server.php
    ```

- Sync php:
    ```shell
    bin/php -S 0.0.0.0:9502 public/php_sync_server.php
    ```
    if php server hangs:
    ```shell
    bin/kill
    ```
