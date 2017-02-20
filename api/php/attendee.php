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
namespace ORM\API\PHP;

use \ORM\Plugins\Messaging\ICS;

/**
 * Objet Attendee pour les API
 *
 * @property string $name Nom du participant
 * @property string $email Email du participant
 * @property string $role Role du participant
 * @property string $response Reponse du participant
 */
class Attendee extends \ORM\Core\Mapping\ObjectMapping {
  // Attendee Response Fields
  const RESPONSE_NEED_ACTION = ICS::PARTSTAT_NEEDS_ACTION;
  const RESPONSE_ACCEPTED = ICS::PARTSTAT_ACCEPTED;
  const RESPONSE_DECLINED = ICS::PARTSTAT_DECLINED;
  const RESPONSE_IN_PROCESS = ICS::PARTSTAT_IN_PROCESS;
  const RESPONSE_TENTATIVE = ICS::PARTSTAT_TENTATIVE;

  // Attendee Role Fields
  const ROLE_CHAIR = ICS::ROLE_CHAIR;
  const ROLE_REQ_PARTICIPANT = ICS::ROLE_REQ_PARTICIPANT;
  const ROLE_OPT_PARTICIPANT = ICS::ROLE_OPT_PARTICIPANT;
  const ROLE_NON_PARTICIPANT = ICS::ROLE_NON_PARTICIPANT;

  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init() {}
}