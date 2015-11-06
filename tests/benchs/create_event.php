<?php
/**
 * Ce fichier est développé pour la gestion de la librairie ORM
 * Cette Librairie permet d'accèder aux données sans avoir à implémenter de couche SQL
 * Des objets génériques vont permettre d'accèder et de mettre à jour les données
 *
 * ORM Copyright © 2015  PNE Annuaire et Messagerie/MEDDE
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

// Inclusions
set_include_path(__DIR__.'/../..');
include_once 'includes/orm.php';
include_once 'tests/ubench-1.2.0/src/Ubench.php';

// Appel le namespace
use ORM\API\PHP;

// Gestion des logs
ORM\Core\Log\ORMLog::InitDebugLog(function($message) {
  echo "[DEBUG] $message\r\n";
});
ORM\Core\Log\ORMLog::InitErrorLog(function($message) {
  echo "[ERROR] $message\r\n";
});

// Gestion des benchs
$bench = new Ubench;
$bench->start();

// Génération de l'objet
$event = new PHP\Event();

$event->uid = uniqid().md5(time())."@TestORM";
$event->calendar = \ORM\Tests\Lib\Random::Calendar();
$event->owner = \ORM\Tests\Lib\Random::Owner();
$event->title = \ORM\Tests\Lib\Random::Summary();

if (\ORM\Tests\Lib\Random::bool()) {
  $event->description = \ORM\Tests\Lib\Random::Description();
}

$event->start = \ORM\Tests\Lib\Random::Start();
$event->end = clone $event->start;
$event->end->add(new DateInterval("PT" . \ORM\Tests\Lib\Random::$faker->randomNumber() . "H"));

if (\ORM\Tests\Lib\Random::bool()) {
  $event->organizer->name = \ORM\Tests\Lib\Random::$faker->name;
  $event->organizer->email = \ORM\Tests\Lib\Random::$faker->email;

  $nbatt = rand(1, 5);
  $attendees = array();
  for ($i = 0; $i < $nbatt; $i++) {
    $attendee = new PHP\Attendee();
    $attendee->name = \ORM\Tests\Lib\Random::$faker->name;
    $attendee->email = \ORM\Tests\Lib\Random::$faker->email;
    $attendee->response = \ORM\Tests\Lib\Random::$faker->randomElement(array($attendee::RESPONSE_ACCEPTED, $attendee::RESPONSE_DECLINED, $attendee::RESPONSE_IN_PROCESS, $attendee::RESPONSE_NEED_ACTION, $attendee::RESPONSE_TENTATIVE));
    $attendee->role = \ORM\Tests\Lib\Random::$faker->randomElement(array($attendee::ROLE_CHAIR, $attendee::ROLE_NON_PARTICIPANT, $attendee::ROLE_OPT_PARTICIPANT, $attendee::ROLE_REQ_PARTICIPANT));
    $attendees[] = $attendee;
  }
  $event->attendees = $attendees;
}

if (\ORM\Tests\Lib\Random::bool()) {
  $event->categories = array(\ORM\Tests\Lib\Random::$faker->text, \ORM\Tests\Lib\Random::$faker->text, \ORM\Tests\Lib\Random::$faker->text);
}

$event->status = \ORM\Tests\Lib\Random::$faker->randomElement(array($event::STATUS_CANCELLED, $event::STATUS_CONFIRMED, $event::STATUS_NONE, $event::STATUS_TENTATIVE));
$event->class = \ORM\Tests\Lib\Random::$faker->randomElement(array($event::CLASS_CONFIDENTIAL, $event::CLASS_PRIVATE, $event::CLASS_PUBLIC));

if (\ORM\Tests\Lib\Random::bool()) {
  $attachment = new PHP\Attachment();
  $attachment->type = $attachment::TYPE_URL;
  $attachment->data = \ORM\Tests\Lib\Random::$faker->url;
  $event->attachments = array($attachment);
}

if (\ORM\Tests\Lib\Random::bool()) {
  $event->recurrence->freq = PHP\Recurrence::FREQ_MONTHLY;
  $event->recurrence->count = 5;
}

$result = $event->insert();
$bench->end();


echo "#####RESULTS####\r\n";
// Get elapsed time and memory
echo $bench->getTime(); // 156ms or 1.123s
echo "\r\n";

echo $bench->getMemoryPeak(); // 152B or 90.00Kb or 15.23Mb
echo "\r\n";

var_export(sys_getloadavg());
echo "\r\n";

// Returns the memory usage at the end mark
echo $bench->getMemoryUsage(); // 152B or 90.00Kb or 15.23Mb
echo "\r\n\r\n";