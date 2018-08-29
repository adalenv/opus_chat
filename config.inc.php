<?php

/* MySQL data */
define ('DBPATH','localhost');
define ('DBUSER','root');
define ('DBPASS','admini');
define ('DBNAME','opusdb');
header('Access-Control-Allow-Origin: *');
/* Path to orangechat folder (relative to server root) */
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
define('ORANGE_BASE',$actual_link.'/');

/* Language ISO code. It must be in "lang" folder */
define('LANGUAGE', 'en');

/* Theme name. It must be in "themes" folder. */
define('THEME', 'orange');

/* Time format. See php.net/date */
define('TIMEFORMAT', 'd/m/Y H:i');
