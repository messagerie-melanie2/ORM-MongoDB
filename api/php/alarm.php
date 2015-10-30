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
 * Objet Alarm pour les API Event
 *
 * @property string $trigger Durée de l'alarme
 * @property Alarm::ACTION_* $action Action à effectuer pour l'alarme
 */
class Alarm extends \ORM\Mapping\ObjectMapping {
  /**
   * CONSTANTES
   */
  // ACTION Fields
  const ACTION_AUDIO = ICS::ACTION_AUDIO;
  const ACTION_DISPLAY = ICS::ACTION_DISPLAY;
  const ACTION_EMAIL = ICS::ACTION_EMAIL;
  const ACTION_PROCEDURE = ICS::ACTION_PROCEDURE;

  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init() {}
}