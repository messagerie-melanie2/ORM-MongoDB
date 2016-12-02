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

ORM\Core\Log\ORMLog::InitTraceLog(function($message) {
  echo "[TRACE] $message\r\n";
});

$user = new PHP\User();
$user->uid = 'thomas.test2';

//$user->save();

$result = $user->load();

if ($result) {
  $result = $user->listSharedCalendars();
}

foreach ($result as $res) {
  echo $res->uid . ' / ' . $res->name . '/' . $res->share_value;
  echo "\r\n";
}

// $calendar = new PHP\Calendar($user);
// $calendar->uid = $user->uid;
// $calendar->uid = uniqid();
// $calendar->name = 'TEST3 Thomas - Utilisateur de test';

// $result = $calendar->save();
// $result = $calendar->load();

// $calendar_share = new PHP\CalendarShare($calendar);
// $calendar_share->user_uid = 'thomas.test3';
// $calendar_share->value = PHP\CalendarShare::READ + PHP\CalendarShare::SHOW;

// $calendar_share->save();

// $property = new PHP\Property();
// $property->name = "property1";
// $property->value = "test";
// $property->params = array(
//         "param1" => "test2",
//         "param2" => "test3",
// );
// $property2 = new PHP\Property();
// $property2->name = "property2";
// $property2->value = "test4";


// $calendar->properties = array($property, $property2);

//var_export($calendar->properties[0]);

// $calendar->save();

// $user->preferences->default_addressbook = 'thomas.test1';
// $user->preferences->default_calendar = 'thomas.payen';

// $result = $user->save();

echo "#####RESULT####\r\n";
//var_export($result);
echo "\r\n\r\n";
