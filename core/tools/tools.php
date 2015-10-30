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
namespace ORM\Core\Tools;

/**
 * Outils pour le traitement ORM
 */
class Tools {
  /**
   * Statswith
   * @param string $haystack The string to search in
   * @param string $needle The searched value
   * @return boolean
   */
  public static function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
  }
  /**
   * Endswith
   * @param string $haystack The string to search in
   * @param string $needle The searched value
   * @return boolean
   */
  public static function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
  }

  /**
   * Conversion de la variable en fonction du type de mapping
   * @param mixed $value Valeur à convertir
   * @param string $type Type pour la conversion
   */
  public static function convert(&$value, $type) {
    switch ($type) {
      case 'integer':
        // Conversion en entier
        $value = intval($value);
        break;
      case 'float':
        // Conversion en flotant
        $value = floatval($value);
        break;
      case 'double':
        // Conversion en double flotant
        $value = doubleval($value);
        break;
      case 'datetime':
        try {
          // Conversion en date/time
          if (!$value instanceof \DateTime) {
            if (is_array($value)) {
              if (isset($value['date'])) {
                if (isset($value['timezone'])) {
                  $value = new \DateTime($value['date'], new \DateTimeZone($value['timezone']));
                }
                else {
                  $value = new \DateTime($value['date']);
                }
              }
            }
            else {
              $value = new \DateTime($value);
            }
          }
        }
        catch (Exception $ex) {
          // Une erreur s'est produite, on met une valeur par défaut pour le pas bloquer la lecture des données
          $value = new \DateTime("1970-01-01 00:00:00");
        }
        break;
      case 'timezone':
        try {
          // Conversion du timezone
          if (!$value instanceof \DateTimeZone) {
            if (is_array($value)) {
              if (isset($value['timezone'])) {
                $value = new \DateTimeZone($value['timezone']);
              }
            }
            else {
              $value = new \DateTimeZone($value);
            }
          }
        }
        catch (Exception $ex) {
          // Une exception se produit on met en GMT par défaut
          $value = new \DateTimeZone('GMT');
        }
        break;
      case 'timestamp':
        // Conversion du timestamp
        if ($value instanceof \DateTime) {
          $value = $value->getTimestamp();
        } elseif (!is_int($value)) {
          $value = strtotime($value);
        }
        break;
      case 'array':
        // Conversion en tableau
        if (!is_array($value)) {
          $value = array($value);
        }
        break;
      case 'string':
      default:
        // Gérer la taille si besoin
        if (isset($field_mapping['size'])) {
          $value = substr($value, 0, $field_mapping['size']);
        }
        break;
    }
  }
}