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

// Inclusions
set_include_path(__DIR__.'/../..');
include_once 'includes/orm.php';
include_once 'tests/ubench-1.2.0/src/Ubench.php';

// Gestion des benchs
$bench = new Ubench;
$bench->start();

//$nb_events = 100000;
$nb_events = 1000000;
//$nb_threads = 4;
$nb_threads = 10;
//$nb_threads = 25;

/****** TRAITEMENT ICI *******/
$events_by_thread = $nb_events / $nb_threads;

for ($i = 0; $i < $nb_threads; $i++) {
  $pid = pcntl_fork();

  if (!$pid) {
    usleep(1000);
    print "Creation du process $i\n";
    $start_events = $i * $events_by_thread;
    $stop_events = $start_events + $events_by_thread;

    for ($j = $start_events; $j < $stop_events; $j++) {
      \ORM\Tests\Lib\Crud::CreateLightRandomEvent($j, $nb_events);
      usleep(10000);
    }
    exit($i);
  }
}

while (pcntl_waitpid(0, $status) != -1) {
  $status = pcntl_wexitstatus($status);
  echo "Fin du process $status\n";
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
