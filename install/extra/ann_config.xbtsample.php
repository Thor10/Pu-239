<?php
error_reporting(E_ALL);
////////////////// GLOBAL VARIABLES /////////////////////////////////////
//== Php poop
$finished = $plist = $corupptthis = '';
$agent = $_SERVER['HTTP_USER_AGENT'];
$detectedclient = $_SERVER['HTTP_USER_AGENT'];
define('INCL_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('ROOT_DIR', realpath(INCL_DIR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
define('CACHE_DIR', ROOT_DIR.'cache'.DIRECTORY_SEPARATOR);
define('CLASS_DIR', INCL_DIR.'class'.DIRECTORY_SEPARATOR);
define('XBT_TRACKER', true);
$INSTALLER09['cache'] = ROOT_DIR.'cache';
require_once CLASS_DIR.'class_cache.php';
require_once CLASS_DIR.'class_bt_options.php';
$INSTALLER09['pic_base_url'] = './pic/';
require_once CACHE_DIR.'class_config.php';
require_once CACHE_DIR.'hit_and_run_settings.php';
if (version_compare(PHP_VERSION, '5.1.0RC1', '>=')) {
    date_default_timezone_set('Europe/London');
}
$mc1 = new CACHE();
//$mc1->MemcachePrefix = 'Pu239_';
define('TIME_NOW', time());
define('ANN_SQL_DEBUG', 1);
define('ANN_SQL_LOGGING', 0);
define('ANN_IP_LOGGING', 1);
$INSTALLER09['announce_interval'] = 60 * 30;
$INSTALLER09['min_interval'] = 60 * 15;
$INSTALLER09['connectable_check'] = 1;

$INSTALLER09['ann_sql_error_log'] = 'sqlerr_logs/ann_sql_err_'.date('M_D_Y').'.log';
$INSTALLER09['ann_sql_log'] = 'sqlerr_logs/ann_sql_query_'.date('M_D_Y').'.log';
$INSTALLER09['crazy_hour'] = false; //== Off for XBT
$INSTALLER09['happy_hour'] = false; //== Off for XBT
$INSTALLER09['ratio_free'] = false;
// DB setup
$INSTALLER09['baseurl'] = '#baseurl';
$INSTALLER09['mysql_host'] = '#mysql_host';
$INSTALLER09['mysql_user'] = '#mysql_user';
$INSTALLER09['mysql_pass'] = '#mysql_pass';
$INSTALLER09['mysql_db'] = '#mysql_db';
$INSTALLER09['expires']['user_passkey'] = 3600 * 8; // 8 hours
$INSTALLER09['expires']['contribution'] = 3 * 86400; // 3 * 86400 3 days
$INSTALLER09['expires']['happyhour'] = 43200; // 43200 1/2 day
$INSTALLER09['expires']['sitepot'] = 86400; // 86400 1 day
$INSTALLER09['expires']['torrent_announce'] = 86400; // 86400 1 day
$INSTALLER09['expires']['torrent_details'] = 30 * 86400; // = 30 days
