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
namespace ORM\API\PHP;

use \ORM\Plugins\Messaging\ICS;

/**
 * Objet Attendee pour les API
 *
 * @property Recurrence::FREQ_* $freq Fréquence de répétition pour la récurrence
 * @property int $count Nombre de répétition
 * @property int $interval Intervale de répétition
 * @property string $byday Liste des jours de répétition
 * @property string $bymonth Liste des mois de répétition
 * @property string $bymonthday Liste des jours de répétition pour le mois
 * @property string $byyearday Liste des jours de répétition pour l'année
 * @property \DateTime $until Date de fin de récurrence
 * @property string $wkst Week start, jour de démarrage de la semaine
 */
class Recurrence extends \ORM\Mapping\ObjectMapping {
  /**
   * CONSTANTES
   */
  // FREQ Fields
  const FREQ_DAILY = ICS::FREQ_DAILY;
  const FREQ_HOURLY = ICS::FREQ_HOURLY;
  const FREQ_MINUTELY = ICS::FREQ_MINUTELY;
  const FREQ_MONTHLY = ICS::FREQ_MONTHLY;
  const FREQ_SECONDLY = ICS::FREQ_SECONDLY;
  const FREQ_WEEKLY = ICS::FREQ_WEEKLY;
  const FREQ_YEARLY = ICS::FREQ_YEARLY;
  // DAY Fields
  const DAY_SUNDAY = ICS::SU;
  const DAY_MONDAY = ICS::MO;
  const DAY_TUESDAY = ICS::TU;
  const DAY_WEDNESDAY = ICS::WE;
  const DAY_THURSDAY = ICS::TH;
  const DAY_SATURDAY = ICS::SA;

  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init() {}
}