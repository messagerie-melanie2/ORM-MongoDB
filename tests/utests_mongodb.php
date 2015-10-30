<?php
set_include_path(__DIR__.'/..');
include_once 'includes/orm.php';

use ORM\API\PHP;

ORM\Log\ORMLog::InitDebugLog(function($message) {
  echo "[DEBUG] $message\r\n";
});

$event = new PHP\Event();

// $event->uid = uniqid().md5(time())."@TestORM";
// $event->uid = "5630e20ca5a44e40717ade13e2eb4e00f19076e4c4802@TestORM";
// $event->calendar = "thomas.test1";
// $event->title = "Test événement 2 [Update]";
// $event->description = "Ceci est un test pour validation";

// $timezone = new DateTimeZone("Europe/Paris");
// $event->start = new DateTime("2015-10-27 17:00:00", $timezone);

// $event->end = new DateTime("2015-10-27 18:00:00", $timezone);

// $event->organizer->name = 'TEST1 Thomas';
// $event->organizer->email = 'thomas.test1@i-carre.net';

// $event->categories = array('Test', 'MongoDB');

// $event->status = $event::STATUS_CONFIRMED;
// $event->class = $event::CLASS_PUBLIC;

// //var_export($event->organizer);
// //var_export($organizer);

// $attendee = new PHP\Attendee();
// $attendee->name = 'TEST2 Thomas';
// $attendee->email = 'thomas.test2@developpement-durable.gouv.fr';
// $attendee->response = $attendee::RESPONSE_ACCEPTED;
// $attendee->role = $attendee::ROLE_REQ_PARTICIPANT;

// //var_export($attendee);

// $event->attendees = array($attendee);

// //$event->attendees[0]->email = 'thomas.test2@i-carre.net';

// $_attendee = $event->attendees[0];
// $_attendee->email = 'thomas.test2@i-carre.net';

// var_export($event);
// $result = $event->save();
// echo "#####RESULT####\r\n";
// var_export($result);
// echo "\r\n\r\n";

$event->uid = "5630e20ca5a44e40717ade13e2eb4e00f19076e4c4802@TestORM";
$event->calendar = "thomas.test1";

echo "#####RESULT####\r\n";
var_export($event->load());
echo "\r\n";
var_export($event);
echo "\r\n";
// var_export($event->exists());
// echo "\r\n\r\n";
