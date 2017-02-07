<?php
ini_set('max_execution_time', 1800);
date_default_timezone_set("Asia/Manila");
require_once('../secret/lib/google/apiclient/vendor/autoload.php');
require_once('lib/google/php-gds/src/GDS/Entity.php');
require_once('lib/google/php-gds/src/GDS/Schema.php');
require_once('lib/google/php-gds/src/GDS/Store.php');
require_once('lib/google/php-gds/src/GDS/Gateway.php');
require_once('lib/google/php-gds/src/GDS/Mapper.php');
require_once('lib/google/php-gds/src/GDS/Gateway/RESTv1.php');
require_once('lib/google/php-gds/src/GDS/Mapper/RESTv1.php');
require_once('lib/google/php-gds/src/GDS/Mapper/ProtoBuf.php');
require_once('lib/google/php-gds/src/GDS/Mapper/ProtoBufGQLParser.php');
require_once('lib/google/php-gds/src/GDS/Exception/GQL.php');
require_once('../secret/secret.php');

putenv('GOOGLE_APPLICATION_CREDENTIALS=../secret/secret.json');
$obj_gateway_fetch = new GDS\Store('user', new \GDS\Gateway\RESTv1($app_id));
$obj_gateway_lead = new GDS\Store('lead', new \GDS\Gateway\RESTv1($app_id));

?>
