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
namespace ORM\Config;

/**
 * Gestion de la configuration
 */
class Config {
  /**
   * Récupère une valeur de la configuration
   * Possibilité de concaténer avec les '.' pour les valeurs de sous tableaux ex : db.default.driver
   * @param string $name
   */
  public static function get($name) {
    global $config;
    // Découpage du nom
    $names = explode('.', $name);
    // Parcours les noms
    foreach ($names as $n) {
      if (!isset($value) && isset($config[$n])) {
        $value = $config[$n];
      }
      elseif (isset($value[$n])) {
        $value = $value[$n];
      }
      else {
        $value = null;
        break;
      }
    }
    // Retourne la valeur
    return $value;
  }
}