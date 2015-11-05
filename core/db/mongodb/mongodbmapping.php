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
namespace ORM\Core\DB\MongoDB;

/**
 * Classe de mapping pour le driver MongoDB
 */
class MongoDBMapping extends \ORM\Core\DB\DriverMapping {
  /**
   * Nom de la collection courante
   * @var string
   */
  protected $_collectionName;

  /**
   * Mapping des opérateurs
   * @var array
   */
  private static $_operatorsMapping = array(
          \ORM\Core\Mapping\Operators::eq => '$eq',
          \ORM\Core\Mapping\Operators::and_ => '$and',
          \ORM\Core\Mapping\Operators::or_ => '$or',
          \ORM\Core\Mapping\Operators::gt => '$gt',
          \ORM\Core\Mapping\Operators::gte => '$gte',
          \ORM\Core\Mapping\Operators::in => '$in',
          \ORM\Core\Mapping\Operators::not_in => '$nin',
          \ORM\Core\Mapping\Operators::like => '$regex',
          \ORM\Core\Mapping\Operators::lt => '$lt',
          \ORM\Core\Mapping\Operators::lte => '$lte',
          \ORM\Core\Mapping\Operators::neq => '$neq',
          \ORM\Core\Mapping\Operators::not => '$not',
          \ORM\Core\Mapping\Operators::nor => '$nor',
  );

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

  /**
   * Méthode de mapping pour un champ
   * @param string $key
   * @param multiple $value
   * @param array $array
   */
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
          $array = &$array[$kv];
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
    // Ré-init
    $this->_fields = array();
    $this->_hasChanged = array();
    // Parcours les champs pour les associer aux valeurs
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
   * @param boolean $usePrimaryKeys [Optionnel] Utiliser les clés primaires pour la recherche
   * @param array $fieldsForSearch [Optionnel] Liste des champs à utiliser pour la recherche
   * @return array
   */
  public function getSearchFields() {
    $searchFields = array();
    if (isset($this->_filter)) {
      // Un filtre est présent, on génére le filtre mongodb
      $searchFields = $this->_filterToMongo($this->_filter);
    }
    else {
      if (!isset($this->_fieldsForSearch)) {
        if (isset($this->_mapping['primaryKeys']) && $this->_usePrimaryKeys) {
          $fieldsForSearch = $this->_mapping['primaryKeys'];
        }
        else {
          $fieldsForSearch = $this->_hasChanged;
        }
      }
      else {
        $fieldsForSearch = $this->_fieldsForSearch;
      }
      // Parcours les champs pour retourner la recherche
      foreach ($fieldsForSearch as $key => $use) {
        if ($use) {
          if (isset($this->_operators[$key])) {
            if ($this->_operators[$key] == \ORM\Core\Mapping\Operators::like) {
              // C'est un like on utilise une regex
              $regex = new \MongoRegex("/" . $this->_fields[$key] . "/i");
              $searchFields[$key] = $regex;
            }
            else {
              // C'est un operateur particulier
              $searchFields[$key] = array(self::$_operatorsMapping[$this->_operators[$key]] => $this->_fields[$key]);
            }
          }
          else {
            // Recherche classique avec égal
            $searchFields[$key] = $this->_fields[$key];
          }
        }
      }
    }
    return $searchFields;
  }

  /**
   * Génère un filtre Mongo en fonction du filtre passé en tableau
   * @param array $filters
   * @param string $operators
   * @return array
   */
  private function _filterToMongo($filters, $operators = null) {
    $mongoDbFilter = array();

    foreach ($filters as $op => $filter) {
      if (!isset($mongoDbFilter[$op])) {
        $mongoDbFilter[$op] = array();
      }
      if (is_array($filter)) {
        // C'est un tableau, donc un nouveau filtre, on fait un appel recursif
        $mongoDbFilter[$op][] = $this->_filterToMongo($filter, $op);
      }
      else {
        // On génère le filtre
        if (isset($this->_operators[$filter])) {
          if ($this->_operators[$key] == \ORM\Core\Mapping\Operators::like) {
            // C'est un like on utilise une regex
            $regex = new \MongoRegex("/" . $this->_fields[$filter] . "/i");
            $search .= array($filter => $regex);
          }
          else {
            // C'est un operateur particulier
            $search .= array($filter => array(self::$_operatorsMapping[$this->_operators[$filter]] => $this->_fields[$filter]));
          }
        }
        else {
          $search .= array($filter => $this->_fields[$filter]);
        }
        $mongoDbFilter[$op][] = $search;
      }
    }

    return $mongoDbFilter;
  }

  /**
   * Liste les champs à insérer
   * @return array
   */
  public function getCreateFields() {
    return $this->getMappingFields();
  }

  /**
   * Liste les champs à mettre à jour
   * @return array
   */
  public function getUpdateFields() {
    $updateFields = array();
    // Parcours les champs pour retourner la recherche
    foreach ($this->_hasChanged as $key => $haschanged) {
      if ($haschanged) {
        $this->_mapField($key, $this->_fields[$key], $updateFields);
      }
    }
    return array('$set' => $updateFields);
  }

  /**
   * Récupération des options pour la requête
   * @return array
   */
  public function getOptions() {
    return array();
  }

  /**
   * Appel l'initialisation
   * @see \ORM\Core\DB\DriverMapping::init()
   */
  public function init() {
    $this->_collectionName = $this->_mapping['CollectionName'];
  }
}