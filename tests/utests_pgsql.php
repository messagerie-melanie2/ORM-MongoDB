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

$event->calendar = 'thomas.test19';
$event->uid = '563cd1625affc6a8e35f8f220cca288eddd1a0ed8c738@TestORM';

echo "#####RESULT####\r\n";
var_export($event->load());
echo "\r\n";
var_export($event);
echo "\r\n";
// var_export($event->exists());
// echo "\r\n\r\n";
