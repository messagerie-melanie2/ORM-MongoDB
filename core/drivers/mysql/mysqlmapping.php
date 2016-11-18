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
namespace ORM\Core\Drivers\MySQL;

/**
 * Classe de mapping pour le driver MySQL
 */
class MySQLMapping extends \ORM\Core\Drivers\PDO\PDOMapping {
  /**
   * Récupère l'instruction OFFSET si besoin
   * @return string
   */
  public function getOffset() {
    $offset = "";
    if (isset($this->_offset)
        && isset($this->_limit)) {
      $offset = ", " . $this->_offset;
    }
    return $offset;
  }

  /**
   * Conversion d'une valeur de l'ORM en SQL
   * @param mixed $value
   * @param string $mappingKey
   * @return string
   */
  protected function _convertToSql($value, $mappingKey = null) {
    if (is_array($value)) {
      if (isset($mappingKey) && $this->_isJson($mappingKey)) {
        $convertedValue = json_encode($value);
      }
      else {
        $convertedValue = serialize($value);
      }
    }
    else if (isset($mappingKey) && $this->_isDateTime($mappingKey) || $value instanceof \DateTime) {
      $convertedValue = $value->format('c');
    }
    else {
      $convertedValue = $value;
    }
    return $convertedValue;
  }
  /**
   * Conversion d'une valeur SQL en valeur de l'ORM
   * @param string $value
   * @param string $mappingKey
   * @return mixed
   */
  protected function _convertFromSql($value, $mappingKey) {
    if ($this->_isArray($mappingKey)) {
      $convertedValue = unserialize($value);
    } else if ($this->_isJson($mappingKey)) {
      $convertedValue = json_decode($value, true);
    }
    else if ($this->_isDateTime($mappingKey)) {
      $convertedValue = new \DateTime($value, new \DateTimeZone('GMT'));
      $tz = $this->_fields[$mappingKey.'_tz'];
      if (isset($tz)) {
      	$convertedValue->setTimezone($timezone);
      }
    }
    else {
      $convertedValue = $value;
    }
    return $convertedValue;
  }
}