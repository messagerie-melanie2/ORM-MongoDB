<?php
/**
 * Ce fichier est développé pour la gestion de la librairie ORM
 * Cette Librairie permet d'accèder aux données sans avoir à implémenter de couche SQL
 * Des objets génériques vont permettre d'accèder et de mettre à jour les données
 *
 * ORM Copyright © 2017  PNE Annuaire et Messagerie/MEDDE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

set_include_path(__DIR__.'/..');
include_once 'includes/orm.php';

use ORM\API\PHP;
use ORM\Core\Mapping\Operators;

ORM\Core\Log\ORMLog::InitDebugLog(function($message) {
  echo "[DEBUG] $message\r\n";
});

ORM\Core\Log\ORMLog::InitErrorLog(function($message) {
  echo "[ERROR] $message\r\n";
});

$event = new PHP\Event();

$timezone = new DateTimeZone("GMT");

// $event->calendar = 'thomas.test1';
// $event->end = new DateTime("2015-10-29 00:00:00", $timezone);
// $event->start = new DateTime("2015-10-30 00:00:00", $timezone);
// $event->recurrence->until = new DateTime("2015-11-30 00:00:00", $timezone);

// $operators = array(
// 		'calendar' => Operators::eq,
// 		'start' => Operators::lt,
// 		'end' => Operators::gt,
// 		'recurrence.until' => Operators::gt,
// );
$filter = array(
  Operators::and_ => array(
  		'calendar' => array(Operators::eq => 'aurelien.test4'),
  		Operators::or_ => array(
  				Operators::and_ => array(
  						'start' => array(Operators::gt => new DateTime("2015-10-01 00:00:00", $timezone)),
			        'end' => array(Operators::lt => new DateTime("2015-12-31 00:00:00", $timezone))
  				),
  				'recurrence.freq' => array(Operators::eq => null),
  		),
  ),
);

$events = $event->list(null, $filter);
//var_export($events);
foreach ($events as $e) {
	echo $e->title . " / " . $e->start->format("Y-m-d H:i:s") . " / " . $e->end->format("Y-m-d H:i:s") . "\n";
}
// $events[0]->attendees[1]->name = 'TEST5 Thomas - SG/SPSSI/CPII/PNE Annuaire et Messagerie';
// $events[0]->title = "Test événement 2 [Update2 13:52]";
// $events[0]->organizer->email = "thomas.test1@i-carre.net";
// $events[0]->save();
// var_export($events[0]->attendees[1]->name);
// echo "\r\n\r\n";

// $event->uid = uniqid().md5(time())."@TestORM";
// $event->uid = "56434dfeecfe679bba0e2619a9043c26a4a7d1f0bf36f@TestORM";
// $event->calendar = "thomas.test1";
// $event->title = "Test événement 2 [Update]";
// $event->description = "Ceci est un test pour validation";

// $timezone = new DateTimeZone("Europe/Paris");
// $event->start = new DateTime("2015-10-30 17:00:00", $timezone);

// $event->end = new DateTime("2015-10-30 18:00:00", $timezone);

// $event->organizer->name = 'TEST1 Thomas';
// $event->organizer->email = 'thomas.test1@developpement-durable.gouv.fr';

// $event->categories = array('Test', 'MongoDB');

// $event->status = $event::STATUS_CONFIRMED;
// $event->class = $event::CLASS_PUBLIC;

// $attendees = array();

// $attendee = new PHP\Attendee();
// $attendee->name = 'TEST2 Thomas';
// $attendee->email = 'thomas.test2@i-carre.net';
// $attendee->response = $attendee::RESPONSE_ACCEPTED;
// $attendee->role = $attendee::ROLE_REQ_PARTICIPANT;
// $attendees[] = $attendee;

// $attendee = new PHP\Attendee();
// $attendee->name = 'TEST3 Thomas';
// $attendee->email = 'thomas.test3@i-carre.net';
// $attendee->response = $attendee::RESPONSE_DECLINED;
// $attendee->role = $attendee::ROLE_REQ_PARTICIPANT;
// $attendees[] = $attendee;

// $event->attendees = $attendees;

// $attachment = new PHP\Attachment();
// $attachment->type = $attachment::TYPE_URL;
// $attachment->data = 'https://www.google.fr/';
// $event->attachments = array($attachment);

// $event->recurrence->freq = PHP\Recurrence::FREQ_MONTHLY;
// $event->recurrence->until = new DateTime("2015-12-28 17:00:00", $timezone);

// //var_export($event);
// echo "\r\n";

// $result = $event->save();
// echo "#####RESULT####\r\n";
// var_export($result);
// echo "\r\n\r\n";

// $event->uid = "5630e20ca5a44e40717ade13e2eb4e00f19076e4c4802@TestORM";
// $event->calendar = "thomas.test1";

// echo "#####RESULT####\r\n";
// var_export($event->load());
// echo "\r\n";
// var_export($event);
// echo "\r\n";
// var_export($event->exists());
// echo "\r\n\r\n";
