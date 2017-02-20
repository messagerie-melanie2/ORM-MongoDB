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

/**
 * Objet CalendarShare pour les API PHP
 *
 * @property string $calendar_id UID du calendrier
 * @property string $user_uid UID de l'utilisateur
 * @property string $value Valeur de partage
 * @property Properties[] $properties Propriétés pour le calendrier
 *
 * @method bool load() Chargement du droit en fonction de l'uid du calendrier et de l'uid de l'utilisateur
 * @method bool save() Sauvegarde le droit, création ou mise à jour
 * @method bool exists() Est-ce que le droit existe dans la base de données
 * @method bool delete() Supprime le droit et met à jour l'historique dans la base de données
 */
class CalendarShare extends \ORM\Core\Mapping\ObjectMapping {
  /**
   * Droit d'afficher les privés
   */
  const PRIV = 1;
  /**
   * Droit d'afficher les événements
   */
  const SHOW = 2;
  /**
   * Droit de lire les événements
   */
  const READ = 4;
  /**
   * Droit d'écrire les événements
   */
  const WRITE = 8;
  /**
   * Droit de supprimer les événements
   */
  const DELETE = 16;

  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init($parentObject = null) {
    if (isset($parentObject)) {
      $this->calendar_id = $parentObject->uid;
    }
  }
}