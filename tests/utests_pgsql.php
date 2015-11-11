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
$timezone = new DateTimeZone("Europe/Paris");

// $event->calendar = 'william.test27312';
// $event->uid = '5641c0eedea387234229a4519f1b92c71b39829cd620f@TestORM';
// $timezone = new DateTimeZone("Europe/Paris");

// $event->end = new DateTime("2015-10-29 18:00:00", $timezone);
// $event->start = new DateTime("2015-10-30 14:00:00", $timezone);

// echo "\r\n";
// $event->recurrence->freq = PHP\Recurrence::FREQ_DAILY;
// $event->recurrence->until = new DateTime("2015-12-30 00:00:00", $timezone);
// var_export($event->save());
// echo "\r\n";

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

echo "#####RESULT####\r\n";
//var_export($event->load());
// var_export($event->exists());
// echo "\r\n\r\n";
