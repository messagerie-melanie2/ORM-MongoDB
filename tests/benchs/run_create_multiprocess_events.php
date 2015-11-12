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

ORM\Core\Log\ORMLog::InitDebugLog(function($message) {
  error_log("[Debug] $message\r\n", 3, "/var/tmp/orm_run_process.log");
});

// Gestion des benchs
$bench = new Ubench;
$bench->start();
$time_start = time();

$nb_threads = 25;
$max_children = 10000;
$nb_children = 0;
$create_by_child = 10;

/****** TRAITEMENT ICI *******/
for ($i = 0; $i < $nb_threads; $i++) {
  $pid = pcntl_fork();
  $nb_children++;
  if (!$pid) {
    createProcess($i);
  }
}

while (pcntl_waitpid(0, $status) != -1) {
  $status = pcntl_wexitstatus($status);
  //echo "Fin du process $status / nb children : $nb_children\n";
  if ($max_children > $nb_children) {
    $pid = pcntl_fork();
    $nb_children++;
    if (!$pid) {
      createProcess($status);
    }
  }
}

function createProcess($i) {
  global $max_children, $nb_children, $create_by_child;
  //print "Creation du process $i\n";
  for ($j = 0; $j < $create_by_child; $j++) {
    \ORM\Tests\Lib\Crud::CreateLightRandomEvent($nb_children + $j, $max_children * $create_by_child);
  }
  usleep(10000);
  exit($i);
}
/****** FIN du TRAITEMENT ICI ***/
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
