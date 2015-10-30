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

/**
 * Objet Exception pour les API Event
 *
 * @property string $uid UID de l'exception
 * @property string $owner Créateur de l'exception
 * @property boolean $deleted Si l'exception existe ou est supprimé
 * @property string $title Titre de l'exception
 * @property string $description Description de l'exception
 * @property \DateTime $start Date de début de l'exception
 * @property \DateTime $end Date de fin de l'exception
 * @property timestamp $created Timestamp de création de l'exception
 * @property timestamp $modified Timestamp de dernière modification de l'exception
 * @property Event::CLASS_* $class Classe de l'exception
 * @property Event::STATUS_* $status Statut de l'exception
 * @property array $categories Liste des catégories de l'exception
 *
 * @property Organizer $organizer Organisateur de l'exception
 * @property Attendee[] $attendees Tableau de participants à l'exception
 * @property Alarm $alarm Alarme pour l'exception
 * @property Attachment[] $attachments Liste de pièces jointes pour l'exception
 */
class Exception extends \ORM\Mapping\ObjectMapping {
  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init() {}
}