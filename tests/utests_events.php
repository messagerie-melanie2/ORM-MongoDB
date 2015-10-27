<?php
/**
 * Ce fichier est développé pour la gestion de la librairie Mélanie2
 * Cette Librairie permet d'accèder aux données sans avoir à implémenter de couche SQL
 * Des objets génériques vont permettre d'accèder et de mettre à jour les données
 * Ce fichier est un exemple d'utilisation
 *
 * ORM M2 Copyright (C) 2015  PNE Annuaire et Messagerie/MEDDE
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
 *
 * @package Librairie Mélanie2
 * @subpackage Tests
 * @author PNE Messagerie/Apitech
 *
 */

var_dump(gc_enabled());
ini_set('zend.enable_gc', 1);
var_dump(gc_enabled());

$temps_debut = microtime_float();
declare(ticks = 1);

function microtime_float() {
	return array_sum(explode(' ', microtime()));
}

// Configuration du nom de l'application pour l'ORM
if (!defined('CONFIGURATION_APP_LIBM2')) {
    define('CONFIGURATION_APP_LIBM2', 'roundcube');
}

// // Définition des inclusions
set_include_path(__DIR__.'/..');
include_once 'includes/libm2.php';

use ORM\Api\Melanie2\Exception;
use ORM\Api\Melanie2\Attendee;
use ORM\Api\Melanie2\Share;
use ORM\Config\ConfigMelanie;
use ORM\Config\MappingMelanie;
use ORM\Api\Melanie2\Recurrence;
use ORM\Api\Melanie2\User;
use ORM\Api\Melanie2\Event;
use ORM\Api\Melanie2\Calendar;
use ORM\Log\ORMLog;
use ORM\Config\MappingZpush;
use ORM\Api\Melanie2\Organizer;
use ORM\Lib\EventToICS;
use ORM\Lib\ICSToEvent;
use ORM\Config\ConfigSQL;
use ORM\Api\Melanie2\Attachment;


$log = function ($message) {
	echo "[LibM2] $message \r\n";
};
ORMLog::InitDebugLog($log);
ORMLog::InitErrorLog($log);

echo "########################\r\n";

//ConfigSQL::setCurrentBackend(ConfigSQL::$HORDE_1);

// Définition de l'utilisateur
$user = new User();
$user->uid = 'thomas.test1';

// Définition du calendrier associé à l'utilisateur
$calendar = new Calendar($user);
$calendar->id = 'thomas.test1';

// Est-ce que l'utilisateur a les droits d'écriture ?
if ($calendar->load()
    && $calendar->asRight(ConfigMelanie::WRITE)) {
  // Définition de l'événement à créer
  $event = new Event($user, $calendar);
  $event->title = "La réunion MCE";
  $event->location = "Tour séquoia - La défense";
  $event->start = "2015-09-16 09:30:00";
  $event->end = "2015-09-16 17:30:00";

  // Sauvegarde du nouvel événement
  $event->save();
}

if ($event->load()) {
  $vcalendar = $event->vcalendar;
  $vcalendar->expand(new DateTime('2015-03-22'), new DateTime('2015-03-29'));
  echo $vcalendar->serialize();
}

echo "#### 1: ".(memory_get_usage()/1024/1024) . ' MiB'." ######\r\n";
echo "#### Cycles: ".gc_collect_cycles()." ######\r\n";
echo "#### 2: ".(memory_get_usage()/1024/1024) . ' MiB'." ######\r\n";
echo "#### Peak: ".(memory_get_peak_usage(true)/1024/1024) . ' MiB'." ######\r\n";

$temps_fin = microtime_float();
echo "#####################################\r\n";
echo "DUREE EXECUTION: ".round($temps_fin - $temps_debut, 4)."\r\n";
echo "#####################################\r\n";
