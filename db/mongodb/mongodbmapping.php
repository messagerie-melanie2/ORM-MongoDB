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
namespace ORM\DB\MongoDB;

/**
 * Classe de mapping pour le driver MongoDB
 */
class MongoDBMapping extends \ORM\DB\DriverMapping {
  /**
   * Nom de la collection courante
   * @var string
   */
  protected $_collectionName;

  /**
   * Liste des champs à lister pour une requête de type find/findOne
   * @var array
   */
  protected $_listFields;
  /**
   * Getter pour le nom de la collection
   * @return string
   */
  public function getCollectionName() {
    return $this->_collectionName;
  }
  /**
   * Setter pour le nom de la collection
   * @param string $collectionName
   */
  public function setCollectionName($collectionName) {
    $this->_collectionName = $collectionName;
  }
  /**
   * Récupération des champs mappés pour l'insertion dans la base de données
   * @return array
   */
  public function getMappingFields() {
    $mappingFields = array();
    foreach ($this->_fields as $key => $value) {
      $this->_mapField($key, $value, $mappingFields);
    }
    return $mappingFields;
  }

  private function _mapField($key, $value, &$array) {
    if (strpos($key, '.') !== false) {
      $keys = explode('.', $key);
      foreach ($keys as $kk => $kv) {
        if (count($keys) == $kk + 1) {
          $array[$kv] = $value;
        }
        else {
          if (!isset($array[$kv])) {
            $array[$kv] = array();
          }
          $array = $array[$kv];
        }
      }
    }
    else {
      $array[$key] = $value;
    }
  }

  /**
   * Défini les champs mappés suite à une lecture dans la base de données
   * @recursive
   * @param array $mappingFields
   * @param string clé parente
   */
  public function setMappingFields($mappingFields, $mKey = null) {
    foreach ($mappingFields as $key => $mappingField) {
      if (isset($mKey)) {
        $key = "$mKey.$key";
      }
      if (isset($this->_mapping['reverse'][$key])) {
        $this->_fields[$key] = $mappingField;
      }
      else if (is_array($mappingField)) {
        $this->setMappingFields($mappingField, $key);
      }
    }
  }

  /**
   * Retourne la liste des champs de recherche avec les valeurs
   * TODO: Associer plus d'informations via les operations etc
   * Voir le getList de l'ORM
   * @return array
   */
  public function getSearchFields() {
    $searchFields = array();
    foreach ($this->_hasChanged as $key => $haschanged) {
      if ($haschanged) {
        $searchFields[$key] = $this->_fields[$key];
      }
    }
    return $searchFields;
  }

  /**
   * Appel l'initialisation
   * @see \ORM\DB\DriverMapping::init()
   */
  public function init() {
    $this->_collectionName = $this->_mapping['CollectionName'];
  }
}