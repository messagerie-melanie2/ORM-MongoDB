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
namespace ORM\API\V1;

/**
 * Objet Event pour les API
 *
 * @property string $id Identifiant unique de l'évènement
 * @property string $calendar Identifiant du calendrier de l'évènement
 * @property string $uid UID de l'évènement
 * @property string $owner Créateur de l'évènement
 * @property string $keywords Keywords
 * @property string $title Titre de l'évènement
 * @property string $description Description de l'évènement
 * @property string $category Catégorie de l'évènment
 * @property string $location Lieu de l'évènement
 * @property Event::STATUS_* $status Statut de l'évènement
 * @property Event::CLASS_* $class Class de l'évènement (privé/public)
 * @property int $alarm Alarme en minute (TODO: class Alarm)
 * @property Attendee[] $attendees Tableau d'objets Attendee
 * @property string $start String au format compatible DateTime, date de début
 * @property string $end String au format compatible DateTime, date de fin
 * @property int $modified Timestamp de la modification de l'évènement
 * @property Recurrence $recurrence objet Recurrence
 * @property Organizer $organizer objet Organizer
 * @property Exception[] $exceptions Liste d'exception
 * @property Attachment[] $attachments Liste des pièces jointes associées à l'évènement (URL ou Binaire)
 *
 * @property bool $deleted Défini si l'exception est un évènement ou juste une suppression
 * @property-read string $realuid UID réellement stocké dans la base de données (utilisé pour les exceptions) (Lecture seule)
 * @property string $ics ICS associé à l'évènement courant, calculé à la volée en attendant la mise en base de données
 * @property-read VObject\Component\VCalendar $vcalendar Object VCalendar associé à l'évènement, peut permettre des manipulations sur les récurrences
 * @property $move Il s'ajout d'un MOVE, les participants sont conservés
 *
 * @method bool load() Chargement l'évènement, en fonction du calendar et de l'uid
 * @method bool exists() Test si l'évènement existe, en fonction du calendar et de l'uid
 * @method bool save() Sauvegarde l'évènement et l'historique dans la base de données
 * @method bool delete() Supprime l'évènement et met à jour l'historique dans la base de données
 */
class Event extends \ORM\Mapping\ObjectMapping {
  /**
   * CONSTANTES
   */
  // CLASS Fields
  const CLASS_PRIVATE = 'PRIVATE';
  const CLASS_PUBLIC = 'PUBLIC';
  const CLASS_CONFIDENTIAL = 'CONFIDENTIAL';
  // STATUS Fields
  const STATUS_TENTATIVE = 'TENTATIVE';
  const STATUS_CONFIRMED = 'CONFIRMED';
  const STATUS_CANCELLED = 'CANCELLED';
  const STATUS_NONE = 'NONE';

  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init() {

  }
}