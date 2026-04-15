# Msg
A PHP Bootstrap web app for storing files by keys and procedures for remote calls.

For secure, app uses `Nginx` configuration to protect direct access to files and `fail2ban` configuration to ban too active users.

## Images
![Lctsy - Capricot](<img/Lctsy - Capricot.png>)

## Deploy
Initially you must to create configuration file with defined keys and templates. For examples of this files see `examples` folder. To run app, you minimally should have PHP 8 installed. Base page available with simple PHP server:

```sh
php -S localhost:8080
```

List of available urls:
- /
- /index.php
- /config.php
- /functions.php

To protect app, set up Nginx and fail2ban on server. See config folder for configuration files, that adds functions of bans and restrictions for files and folders. 

To setup Nginx, add nginx.conf to your sites-available configuration at server level. Also add these instructions in nginx.conf to set up access limit and banlist:

```conf
# Must be defined at http level of /etc/nginx/nginx.conf
limit_req_zone $binary_remote_addr zone=limreq:5m rate=4r/s;
include /etc/nginx/banlist.conf;
```
