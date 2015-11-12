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
   *
   * @var string
   */
  protected $_collectionName;

  /**
   * Mapping des opérateurs
   *
   * @var array
   */
  private static $_operatorsMapping = array(
          \ORM\Core\Mapping\Operators::eq => '$eq',
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
          \ORM\Core\Mapping\Operators::and_0 => '$and',
          \ORM\Core\Mapping\Operators::and_1 => '$and',
          \ORM\Core\Mapping\Operators::and_2 => '$and',
          \ORM\Core\Mapping\Operators::and_3 => '$and',
          \ORM\Core\Mapping\Operators::and_4 => '$and',
          \ORM\Core\Mapping\Operators::and_5 => '$and',
          \ORM\Core\Mapping\Operators::and_6 => '$and',
          \ORM\Core\Mapping\Operators::and_7 => '$and',
          \ORM\Core\Mapping\Operators::and_8 => '$and',
          \ORM\Core\Mapping\Operators::and_9 => '$and',
          \ORM\Core\Mapping\Operators::or_0 => '$or',
          \ORM\Core\Mapping\Operators::or_1 => '$or',
          \ORM\Core\Mapping\Operators::or_2 => '$or',
          \ORM\Core\Mapping\Operators::or_3 => '$or',
          \ORM\Core\Mapping\Operators::or_4 => '$or',
          \ORM\Core\Mapping\Operators::or_5 => '$or',
          \ORM\Core\Mapping\Operators::or_6 => '$or',
          \ORM\Core\Mapping\Operators::or_7 => '$or',
          \ORM\Core\Mapping\Operators::or_8 => '$or',
          \ORM\Core\Mapping\Operators::or_9 => '$or',
  );

  /**
   * Getter pour le nom de la collection
   *
   * @return string
   */
  public function getCollectionName() {
    return $this->_collectionName;
  }

  /**
   * Setter pour le nom de la collection
   *
   * @param string $collectionName
   */
  public function setCollectionName($collectionName) {
    $this->_collectionName = $collectionName;
  }

  /**
   * Récupération des champs mappés pour l'insertion dans la base de données
   *
   * @return array
   */
  public function getMappingFields() {
    $mappingFields = array();
    foreach ($this->_fields as $key => $value) {
      $rKey = $this->_getReverseKey($key);
      if ($this->_isObjectType($rKey)) {
        if ($this->_isObjectList($rKey)) {
          // Génère un tableau
          $objects = array();
          foreach ($value as $k => $v) {
            // Récupère les champs du drivermapping
            $objects[$k] = $v->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getMappingFields();
          }
          $this->_mapField($key, $objects, $mappingFields);
        } else {
          // Récupère les champs du drivermapping
          $this->_mapField($key, $value->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getMappingFields(), $mappingFields);
        }
      } else {
        $this->_mapField($key, $value, $mappingFields);
      }
    }
    return $mappingFields;
  }

  /**
   * Méthode de mapping pour un champ
   *
   * @param string $key
   * @param multiple $value
   * @param array $array
   */
  private function _mapField($key, $value, &$array) {
    if (strpos($key, '.') !== false) {
      $keys = explode('.', $key);
      foreach ($keys as $kk => $kv) {
        if (count($keys) == $kk + 1) {
          $array[$kv] = $this->_convertToMongo($value, $this->_getReverseKey($key));
        } else {
          if (! isset($array[$kv])) {
            $array[$kv] = array();
          }
          $array = &$array[$kv];
        }
      }
    } else {
      $array[$key] = $this->_convertToMongo($value, $this->_getReverseKey($key));
    }
  }

  /**
   * Défini les champs mappés suite à une lecture dans la base de données
   * @recursive
   *
   * @param array $mappingFields
   * @param
   *          string clé parente
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
        $rKey = $this->_mapping['reverse'][$key];
        if ($this->_isObjectType($rKey)) {
          // Nom de la classe à instancier
          $class_name = "ORM\\API\\" . $this->_mapping['fields'][$rKey]['ObjectType'];
          if ($this->_isObjectList($rKey)) {
            // C'est une liste d'objets, on génère le tableau
            $this->_fields[$key] = array();
            foreach ($mappingField as $k => $v) {
              // Instancie le nouvel objet et l'ajout au tableau
              $object = new $class_name();
              $object->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->setMappingFields($v);
              $this->_fields[$key][$k] = $object;
            }
          } else {
            // Instancie le nouvel objet, et l'ajoute à la liste des champs
            $object = new $class_name();
            $object->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->setMappingFields($mappingField);
            $this->_fields[$key] = $object;
          }
        } else {
          $this->setField($key, $mappingField, $rKey);
        }

      } else if (is_array($mappingField)) {
        $this->setMappingFields($mappingField, $key);
      }
    }
  }

  /**
   * Retourne la liste des champs de recherche avec les valeurs
   * TODO: Associer plus d'informations via les operations etc
   * Voir le getList de l'ORM
   *
   * @param boolean $usePrimaryKeys
   *          [Optionnel] Utiliser les clés primaires pour la recherche
   * @param array $fieldsForSearch
   *          [Optionnel] Liste des champs à utiliser pour la recherche
   * @return array
   */
  public function getSearchFields() {
    $searchFields = array();
    if (isset($this->_filter)) {
      // Un filtre est présent, on génére le filtre mongodb
      $searchFields = $this->_filterToMongo($this->_filter);
    } else {
      if (! isset($this->_fieldsForSearch)) {
        if (isset($this->_mapping['primaryKeys']) && $this->_usePrimaryKeys) {
          $fieldsForSearch = $this->_mapping['primaryKeys'];
        } else {
          $fieldsForSearch = $this->_hasChanged;
        }
      } else {
        $fieldsForSearch = $this->_fieldsForSearch;
      }
      // Parcours les champs pour retourner la recherche
      foreach ($fieldsForSearch as $key => $use) {
        if ($use) {
          $value = $this->getField($key);
          $searchKey = $key;
          if (isset($value['date'])) {
            $value = $value['date'];
            $searchKey .= ".date";
          }
          if (isset($this->_operators[$key]) && $this->_operators[$key] != \ORM\Core\Mapping\Operators::eq) {
            if ($this->_operators[$key] == \ORM\Core\Mapping\Operators::like) {
              // C'est un like on utilise une regex
              $regex = new \MongoRegex("/" . $value . "/i");
              $searchFields[$searchKey] = $regex;
            } else {
              // C'est un operateur particulier
              $searchFields[$searchKey] = array(
                  self::$_operatorsMapping[$this->_operators[$key]] => $value
              );
            }
          } else {
            // Recherche classique avec égal
            $searchFields[$searchKey] = $value;
          }
        }
      }
    }
    return $searchFields;
  }

  /**
   * Génère un filtre Mongo en fonction du filtre passé en tableau
   *
   * @param array $filters
   * @param string $operators
   * @return array
   */
  private function _filterToMongo($filters, $operators = null) {
    $mongoDbFilter = array();

    foreach ($filters as $op => $filter) {
      if (is_array($filter) && isset(self::$_operatorsMapping[$op])) {
        // C'est un tableau, donc un nouveau filtre, on fait un appel recursif
        if (! isset($operators)) {
          $mongoDbFilter[self::$_operatorsMapping[$op]] = $this->_filterToMongo($filter, $op);
        } else {
          $mongoDbFilter[] = array(
              self::$_operatorsMapping[$op] => $this->_filterToMongo($filter, $op)
          );
        }
      } else if (is_array($filter)) {
        // La valeur et l'opérateur sont présent dans le filtre
        reset($filter);
        $_op = key($filter);
        $key = $op;
        if (strrpos($key, '_') === (strlen($key) - 2)) {
          $key = substr($key, 0, strlen($key) - 2);
        }
        if (strpos($key, '.') !== false) {
          $_f = explode('.', $key, 2);
          $searchKey = $this->_getMapFieldName($_f[0]) . '.' . $_f[1];
        } else {
          $searchKey = $this->_getMapFieldName($key);
        }
        $value = $this->_convertToMongo($filter[$_op]);
        if (isset($value['date'])) {
          $value = $value['date'];
          $searchKey .= ".date";
        }
        if ($_op == \ORM\Core\Mapping\Operators::like) {
          // C'est un like on utilise une regex
          $regex = new \MongoRegex("/" . $value . "/i");
          $mongoDbFilter[] = array(
              $searchKey => $regex
          );
        } else if ($_op == \ORM\Core\Mapping\Operators::neq && ! isset($value)) {
          // C'est un operateur particulier
          $mongoDbFilter[] = array(
              $searchKey => array(
                  '$exists' => true
              )
          );
        } else if ($_op == \ORM\Core\Mapping\Operators::eq && ! isset($value)) {
          // C'est un operateur particulier
          $mongoDbFilter[] = array(
              $searchKey => array(
                  '$exists' => false
              )
          );
        } else {
          // C'est un operateur particulier
          $mongoDbFilter[] = array(
              $searchKey => array(
                  self::$_operatorsMapping[$_op] => $value
              )
          );
        }
      } else {
        // C'est une valeur unique, on la récupère depuis fields
        if (strpos($filter, '.') !== false) {
          $_f = explode('.', $filter, 2);
          $key = $this->_getMapFieldName($_f[0]);
          $value = $this->_fields[$key]->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getField($_f[1]);
          $searchKey = $this->_getMapFieldName($_f[0]) . '.' . $_f[1];
          $key = $filter;
        } else {
          $key = $this->_getMapFieldName($filter);
          $value = $this->getField($key, $filter);
          $searchKey = $key;
        }
        if (isset($value['date'])) {
          $value = $value['date'];
          $searchKey .= ".date";
        }
        // On génère le filtre
        if (isset($this->_operators[$key])) {
          if ($this->_operators[$key] == \ORM\Core\Mapping\Operators::like) {
            // C'est un like on utilise une regex
            $regex = new \MongoRegex("/" . $value . "/i");
            $mongoDbFilter[] = array(
                $searchKey => $regex
            );
          } else if ($this->_operators[$key] == \ORM\Core\Mapping\Operators::neq && ! isset($value)) {
            // C'est un operateur particulier
            $mongoDbFilter[] = array(
                $searchKey => array(
                    '$exists' => true
                )
            );
          } else if ($this->_operators[$key] == \ORM\Core\Mapping\Operators::eq && ! isset($value)) {
            // C'est un operateur particulier
            $mongoDbFilter[] = array(
                $searchKey => array(
                    '$exists' => false
                )
            );
          } else {
            // C'est un operateur particulier
            $mongoDbFilter[] = array(
                $searchKey => array(
                    self::$_operatorsMapping[$this->_operators[$key]] => $value
                )
            );
          }
        } else {
          $mongoDbFilter[] = array(
              $searchKey => $value
          );
        }

      }
    }
    return $mongoDbFilter;
  }

  /**
   * Liste les champs à insérer
   *
   * @return array
   */
  public function getCreateFields() {
    return $this->getMappingFields();
  }

  /**
   * Liste les champs à mettre à jour
   *
   * @return array
   */
  public function getUpdateFields() {
    $updateFields = array();
    // Parcours les champs pour retourner la recherche
    foreach ($this->_hasChanged as $key => $haschanged) {
      if ($haschanged) {
        // Récupération de la clé
        $rKey = $this->_getReverseKey($key);
        if (! $this->_isObjectType($rKey)) {
          // $this->_mapField($key, $this->_fields[$key], $updateFields);
          $updateFields[$key] = $this->getField($key);
        }
      }
    }
    // Parcours tous les champs pour savoir si un champ complexe a été modifié
    foreach ($this->_fields as $key => $value) {
      // Récupération de la clé
      $rKey = $this->_getReverseKey($key);
      if ($this->_isObjectType($rKey)) {
        if ($this->_isObjectList($rKey)) {
          foreach ($value as $k => $v) {
            foreach ($v->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getUpdateFields() as $kk => $vv) {
              $updateFields[$key . '.' . $k . '.' . $kk] = $vv;
            }
          }
        } else {
          foreach ($value->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getUpdateFields() as $k => $v) {
            $updateFields[$key . '.' . $k] = $v;
          }
        }
      }
    }
    return $updateFields;
  }

  /**
   * Récupération des options pour la requête
   *
   * @return array
   */
  public function getOptions() {
    return array();
  }

  /**
   * Récupération de la valeur d'un champ converti au format de la base de données
   *
   * @param string $key
   *          Clé du champ
   * @param string $rKey
   *          [Optionnel] Clé reverse
   * @return mixed
   */
  public function getField($key, $rKey = null) {
    return $this->_convertToMongo($this->_fields[$key], isset($rKey) ? $rKey : $this->_getReverseKey($key));
  }

  /**
   * Assigne la valeur d'un champ converti depuis la base de données
   *
   * @param string $key
   *          Clé du champ
   * @param mixed $value
   *          Valeur à convertir
   * @param string $rKey
   *          [Optionnel] Clé reverse
   */
  public function setField($key, $value, $rKey = null) {
    $this->_fields[$key] = $this->_convertFromMongo($value, isset($rKey) ? $rKey : $this->_getReverseKey($key));
  }

  /**
   * Appel l'initialisation
   *
   * @see \ORM\Core\DB\DriverMapping::init()
   */
  public function init() {
    $this->_collectionName = $this->_mapping['CollectionName'];
  }

  /**
   * Conversion d'une valeur de l'ORM en Mongo
   *
   * @param mixed $value
   * @param string $mappingKey
   * @return string
   */
  protected function _convertToMongo($value, $mappingKey = null) {
    if (isset($mappingKey) && $this->_isDateTime($mappingKey) || $value instanceof \DateTime) {
      $convertedValue = array();
      // Conversion des dates en GMT
      $convertedValue['tz'] = $value->getTimezone()->getName();
      $value->setTimezone(new \DateTimeZone('GMT'));
      $convertedValue['date'] = new \MongoDate($value->getTimestamp());
    } else {
      $convertedValue = $value;
    }
    return $convertedValue;
  }

  /**
   * Conversion d'une valeur Mongo en valeur de l'ORM
   *
   * @param string $value
   * @param string $mappingKey
   * @return mixed
   */
  protected function _convertFromMongo($value, $mappingKey) {
    if ($this->_isDateTime($mappingKey)) {
      $convertedValue = $value['date']->toDateTime();
      $convertedValue->setTimezone(new \DateTimeZone($value['tz']));
    } else {
      $convertedValue = $value;
    }
    return $convertedValue;
  }
}