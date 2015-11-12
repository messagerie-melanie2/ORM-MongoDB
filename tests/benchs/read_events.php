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

ORM\Core\Log\ORMLog::InitDebugLog(function($message) {
  error_log("[Debug] $message", 0, "/var/tmp/orm_run_process.log");
});
ORM\Core\Log\ORMLog::InitErrorLog(function($message) {
  error_log("[Error] $message", 0, "/var/tmp/orm_run_process_error.log");
});

// $max_events = $argv[1];
// $start_events = $argv[2];
// $stop_events = $argv[3];
$max_events = 100000;
$start_events = 0;
$stop_events = 20;
$count_events = 0;

include_once 'tests/ubench-1.2.0/src/Ubench.php';

// Gestion des benchs
$bench = new Ubench;
$bench->start();

for ($i = $start_events; $i < $stop_events; $i ++) {
  $events = \ORM\Tests\Lib\Crud::ReadRandomEvents($i, $max_events);
  $count_events += count($events);
}

/****** FIN du TRAITEMENT ICI ***/
$bench->end();

echo "#####RESULTS####\r\n";
echo "count events: $count_events\r\n";
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