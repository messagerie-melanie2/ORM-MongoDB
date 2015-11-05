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
 * Objet Event pour les API
 *
 * @property string $calendar Identifiant du calendrier de l'évènement
 * @property string $uid UID de l'évènement
 * @property string $owner Créateur de l'événement
 * @property string $etag Etag de l'événement
 * @property boolean $deleted Si l'événement existe ou est supprimé
 * @property string $title Titre de l'événement
 * @property string $description Description de l'événement
 * @property \DateTime $start Date de début de l'événement
 * @property \DateTime $end Date de fin de l'événement
 * @property timestamp $created Timestamp de création de l'événement
 * @property timestamp $modified Timestamp de dernière modification de l'événement
 * @property Event::CLASS_* $class Classe de l'événement
 * @property Event::STATUS_* $status Statut de l'événement
 * @property array $categories Liste des catégories de l'événement
 *
 * @property Organizer $organizer Organisateur de l'événement
 * @property Attendee[] $attendees Tableau de participants à l'événement
 * @property Alarm $alarm Alarme pour l'événement
 * @property Recurrence $recurrence Récurrence pour l'événement
 * @property Attachment[] $attachments Liste de pièces jointes pour l'événement
 * @property Exception[] $exceptions Liste des exceptions pour l'événement
 *
 * @method bool load() load() Chargement l'évènement, en fonction du calendar et de l'uid
 * @method bool insert() insert() Enregistre un nouvel événement
 * @method bool update() update() Met à jour l'événement courant
 * @method bool save() save() Sauvegarde l'événement courant
 * @method bool exists() exists() Est-ce que l'événement courant existe dans la base de données ?
 * @method bool delete() delete() Supprime l'évènement et met à jour l'historique dans la base de données
 * @method Event[] list() list(array listFields, array filter, array operators, array orderBy, boolean asc, int limit, int offset, array unsensitiveFields) Liste les éléments en fonction des paramètres
 */
class Event extends \ORM\Core\Mapping\ObjectMapping {
  /**
   * CONSTANTES
   */
  // CLASS Fields
  const CLASS_PRIVATE = ICS::CLASS_PRIVATE;
  const CLASS_PUBLIC = ICS::CLASS_PUBLIC;
  const CLASS_CONFIDENTIAL = ICS::CLASS_CONFIDENTIAL;

  // STATUS Fields
  const STATUS_TENTATIVE = ICS::STATUS_TENTATIVE;
  const STATUS_CONFIRMED = ICS::STATUS_CONFIRMED;
  const STATUS_CANCELLED = ICS::STATUS_CANCELLED;
  const STATUS_NONE = 'NONE';

  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init() {}
}