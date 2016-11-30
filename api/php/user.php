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
 * Objet User pour les API PHP
 *
 * @property string $uid UID de l'utilisateur
 * @property Datetime $created Date de création de l'utilisateur
 * @property Preferences $preferences Préférences de l'utilisateur
 *
 * @method boolean load() Chargement de l'utilisateur, en fonction de son uid
 * @method boolean save() Sauvegarde l'utilisateur courant
 * @method boolean exists() Est-ce que l'utilisateur courant existe dans la base de données ?
 * @method boolean delete() Supprime l'utilisateur et met à jour l'historique dans la base de données
 * @method Calendar[] listCalendars() Liste les calendriers de l'utilisateur courant
 * @method Calendar[] listSharedCalendars() Liste les calendriers auquel l'utilisateur courant a accès
 */
class User extends \ORM\Core\Mapping\ObjectMapping {
  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init() {}
}