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
  error_log("[Debug] $message", 3, "/var/tmp/orm_run_process.log");
});

// Gestion des benchs
$bench = new Ubench;
$bench->start();
$time_start = time();

$max_children = 100000000;
$nb_children = 0;

$nb_threads = 20;
$duration = 60*60;

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
  //echo "Fin du process $status\n";
  if ($duration > time() - $time_start) {
    $pid = pcntl_fork();
    $nb_children++;

    if (!$pid) {
      createProcess($status);
    }
  }
}

function createProcess($i) {
  //print "Creation du process $i\n";
  $events = \ORM\Tests\Lib\Crud::ReadRandomEvents($nb_children, $max_children);
  if ($nb_children%5 === 0) {
    \ORM\Tests\Lib\Crud::CreateLightRandomEvent($nb_children, $max_children);
  } else if ($nb_children%6 === 0) {
    if (count($events) > 0) {
      $event = $events[($nb_children%count($events))-1];
      \ORM\Tests\Lib\Crud::UpdateRandomEvent($nb_children, $max_children, $event->uid, $event->calendar);
    }
  } else if ($nb_children%11 === 0) {
    if (count($events) > 0) {
      $event = $events[($nb_children%count($events))-1];
      \ORM\Tests\Lib\Crud::DeleteEvent($event->uid, $event->calendar);
    }
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
