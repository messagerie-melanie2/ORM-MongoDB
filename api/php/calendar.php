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
 * Objet Calendar pour les API PHP
 *
 * @property string $uid UID du calendrier
 * @property string $name Nom du calendrier
 * @property string $owner Propriétaire du calendrier
 * @property Datetime $created Date de création de l'utilisateur
 * @property string $ctag CTag du calendrier
 * @property Properties[] $properties Propriétés pour le calendrier
 *
 * @method bool load() Chargement du calendrier en fonction de son uid
 * @method bool save() Sauvegarde le calendrier, création ou mise à jour
 * @method bool exists() Est-ce que le calendrier existe dans la base de données
 * @method bool delete() Supprime le calendrier et met à jour l'historique dans la base de données
 */
class Calendar extends \ORM\Core\Mapping\ObjectMapping {
  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init($parentObject = null) {
    if (isset($parentObject)) {
      $this->owner = $parentObject->uid;
    }
  }
}