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

ORM\Core\Log\ORMLog::InitDebugLog(function($message) {
  echo "[DEBUG] $message\r\n";
});

ORM\Core\Log\ORMLog::InitErrorLog(function($message) {
  echo "[ERROR] $message\r\n";
});

$event = new PHP\Event();

$event->calendar = 'william.test27312';
$event->uid = '5641c0eedea387234229a4519f1b92c71b39829cd620f@TestORM';

echo "#####RESULT####\r\n";
var_export($event->load());
echo "\r\n";
$event->recurrence->freq = PHP\Recurrence::FREQ_DAILY;
$event->recurrence->until = new \DateTime("@".time());
var_export($event->save());
echo "\r\n";
// var_export($event->exists());
// echo "\r\n\r\n";
