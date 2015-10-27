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
 * Objet Event pour les API
 *
 * @property string $calendar Identifiant du calendrier de l'évènement
 * @property string $uid UID de l'évènement
 *
 *
 * @method bool load() Chargement l'évènement, en fonction du calendar et de l'uid
 * @method bool insert() Enregistre un nouvel événement
 * @method bool update() Met à jour l'événement courant
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