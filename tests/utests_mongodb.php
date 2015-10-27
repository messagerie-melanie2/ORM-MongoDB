<?php
set_include_path(__DIR__.'/..');
include_once 'includes/orm.php';

use ORM\API\PHP;

$event = new PHP\Event();

// $event->uid = uniqid().md5(time())."@TestORM";
// $event->calendar = "thomas.test1";
// $event->title = "Test événement";
// $event->description = "Ceci est un test pour validation";

// $timezone = new DateTimeZone("Europe/Paris");
// $event->start = new DateTime("2015-10-27 17:00:00", $timezone);
// $event->start_timezone = $timezone;

// $event->end = new DateTime("2015-10-27 18:00:00", $timezone);
// $event->end_timezone = $timezone;

// var_export($event);

// $result = $event->insert();
// var_export($result);

$event->uid = "562fa0a935bdc44cea1137060cb33809e14f14774729e@TestORM";
$event->calendar = "thomas.test1";

$result = $event->load();
var_export($result);
var_export($event);

