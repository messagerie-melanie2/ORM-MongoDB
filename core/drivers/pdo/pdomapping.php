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
namespace ORM\Core\Drivers\PDO;

/**
 * Classe de mapping pour le driver PDO
 */
class PDOMapping extends \ORM\Core\Drivers\DriverMapping {
  /**
   * Nom de la table courante
   *
   * @var string
   */
  protected $_tableName;

  /**
   * Mapping des opérateurs
   *
   * @var array
   */
  private static $_operatorsMapping = array(
          \ORM\Core\Mapping\Operators::eq => '=',
          \ORM\Core\Mapping\Operators::gt => '>',
          \ORM\Core\Mapping\Operators::gte => '>=',
          \ORM\Core\Mapping\Operators::in => 'IN',
          \ORM\Core\Mapping\Operators::not_in => 'NOT IN',
          \ORM\Core\Mapping\Operators::like => 'LIKE',
          \ORM\Core\Mapping\Operators::lt => '<',
          \ORM\Core\Mapping\Operators::lte => '<=',
          \ORM\Core\Mapping\Operators::neq => '<>',
          \ORM\Core\Mapping\Operators::not => 'NOT',
          \ORM\Core\Mapping\Operators::nor => '$nor',
          \ORM\Core\Mapping\Operators::and_0 => 'AND',
          \ORM\Core\Mapping\Operators::and_1 => 'AND',
          \ORM\Core\Mapping\Operators::and_2 => 'AND',
          \ORM\Core\Mapping\Operators::and_3 => 'AND',
          \ORM\Core\Mapping\Operators::and_4 => 'AND',
          \ORM\Core\Mapping\Operators::and_5 => 'AND',
          \ORM\Core\Mapping\Operators::and_6 => 'AND',
          \ORM\Core\Mapping\Operators::and_7 => 'AND',
          \ORM\Core\Mapping\Operators::and_8 => 'AND',
          \ORM\Core\Mapping\Operators::and_9 => 'AND',
          \ORM\Core\Mapping\Operators::or_0 => 'OR',
          \ORM\Core\Mapping\Operators::or_1 => 'OR',
          \ORM\Core\Mapping\Operators::or_2 => 'OR',
          \ORM\Core\Mapping\Operators::or_3 => 'OR',
          \ORM\Core\Mapping\Operators::or_4 => 'OR',
          \ORM\Core\Mapping\Operators::or_5 => 'OR',
          \ORM\Core\Mapping\Operators::or_6 => 'OR',
          \ORM\Core\Mapping\Operators::or_7 => 'OR',
          \ORM\Core\Mapping\Operators::or_8 => 'OR',
          \ORM\Core\Mapping\Operators::or_9 => 'OR',
  );

  /**
   * Getter pour le nom de la table
   *
   * @return string
   */
  public function getTableName() {
    return $this->_tableName;
  }

  /**
   * Setter pour le nom de la table
   *
   * @param string $tableName
   */
  public function setTableName($tableName) {
    $this->_tableName = $tableName;
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
          if ($this->_isJson($rKey)) {
            // Ajoute le tableau au format JSON
            $mappingFields[$key] = json_encode($objects);
          } elseif ($this->_isArray($rKey)) {
            // Ajoute le tableau serializé
            $mappingFields[$key] = serialize($objects);
          } else {
            // Ajoute le tableau (il sera serialisé plus tard si besoin)
            $mappingFields[$key] = $objects;
          }
        } else {
          $fields = $value->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getMappingFields();
          $mappingFields = array_merge($mappingFields, $fields);
        }
      } else {
        $mappingFields[$key] = $this->_convertToSql($value, $this->_getReverseKey($key));
        if (is_array($mappingFields[$key])) {
          if ($this->_isJson($rKey)) {
            $mappingFields[$key] = json_encode($mappingFields[$key]);
          } elseif ($this->_isArray($rKey)) {
            $mappingFields[$key] = serialize($mappingFields[$key]);
          }
        }
      }
    }
    return $mappingFields;
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
    if (is_array($mappingFields)) {
      foreach ($mappingFields as $key => $mappingField) {
        $rKey = $this->_getReverseKey($key);
        if ($this->_isObjectType($rKey) && ! empty($mappingField)) {
          // Nom de la classe à instancier
          $class_name = "ORM\\API\\" . $this->_mapping['fields'][$rKey]['ObjectType'];
          if ($this->_isObjectList($rKey)) {
            if ($this->_isJson($rKey)) {
              $mappingField = json_decode($mappingField, true);
            }
            elseif ($this->_isArray($rKey)) {
              $mappingField = unserialize($mappingField);
            }
            // C'est une liste d'objets, on génère le tableau
            $this->_fields[$key] = array();
            foreach ($mappingField as $k => $v) {
              // Instancie le nouvel objet et l'ajout au tableau
              $object = new $class_name();
              $object->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->setMappingFields($v);
              $this->_fields[$key][$k] = $object;
            }
          } else {
            $oldKey = $key;
            if (isset($this->_mapping['fields'][$rKey]) && isset($this->_mapping['fields'][$rKey]['name']) && $this->_mapping['fields'][$rKey]['name'] != $key) {
              $key = $this->_mapping['fields'][$rKey]['name'];
            }
            // Instancie le nouvel objet, et l'ajoute à la liste des champs
            if (! isset($this->_fields[$key])) {
              $object = new $class_name();
              $this->_fields[$key] = $object;
            }
            if ($this->_isJson($rKey)) {
              $mappingField = json_decode($mappingField, true);
            }
            elseif ($this->_isArray($rKey)) {
              $mappingField = unserialize($mappingField);
            }
            // Ajoute la nouvelle valeur
            $this->_fields[$key]->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->setMappingFields($mappingField);
          }
        } else {
          $this->setField($key, $mappingField, $rKey);
        }
      }
    }
  }

  /**
   * Retourne la liste des champs de recherche avec les valeurs
   * TODO: Associer plus d'informations via les operations etc
   * Voir le getList de l'ORM
   *
   * @return array|string
   */
  public function getSearchFields() {
    $searchFields = "";
    $searchValues = array();

    if (isset($this->_filter)) {
      $result = $this->_filterToSql($this->_filter);
      $searchFields = $result['string'];
      $searchValues = $result['values'];
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
          $rKey = $this->_getReverseKey($key);
          if ($searchFields != "") {
            $searchFields .= " AND ";
          }
          if (isset($this->_operators[$key])) {
            $searchFields .= "$key " . self::$_operatorsMapping[$this->_operators[$key]] . " :$key";
          } else {
            $searchFields .= "$key = :$key";
          }
          $searchValues[$key] = $this->getField($key);
          if (is_array($searchValues[$key])) {
            if ($this->_isJson($rKey)) {
              $searchValues[$key] = json_encode($searchValues[$key]);
            } elseif ($this->_isArray($rKey)) {
              $searchValues[$key] = serialize($searchValues[$key]);
            }
          }
        }
      }
    }
    // Retourne les résultats
    return array(
        'searchFields' => $searchFields,
        'searchValues' => $searchValues
    );
  }

  /**
   * Génère un filtre SQL en fonction du filtre passé en tableau
   *
   * @param array $filters
   * @param string $operators
   * @return array
   */
  private function _filterToSql($filters, $operators = null) {
    $string = "";
    $values = array();
    $par = false;

    if (count($filters) > 1) {
    	$string = "(";
    	$par = true;
    }

    foreach ($filters as $op => $filter) {
      // Ajoute l'operateur de transition
      if ($string != "" && $string != "(") {
        $string .= " " . self::$_operatorsMapping[$operators] . " ";
      }
      if (is_array($filter) && isset(self::$_operatorsMapping[$op])) {
        // C'est un tableau, donc un nouveau filtre, on fait un appel recursif
        $result = $this->_filterToSql($filter, $op);
        $string .= $result['string'];
        $values = array_merge($values, $result['values']);
      } else if (is_array($filter)) {
      	// La valeur et l'opérateur sont présent dans le filtre
      	reset($filter);
      	$_op = key($filter);
      	$key = $op;
      	$post_key = "";
      	if (strrpos($key, '_') === (strlen($key) - 2)) {
      	  $post_key = substr($key, strlen($key) - 2);
      	  $key = substr($key, 0, strlen($key) - 2);
      	}
      	if (strpos($key, '.') !== false) {
      		$key = str_replace('.', '_', $key);
      	}
      	$searchKey = $this->_getMapFieldName($key);
      	$value = $this->_convertToSql($filter[$_op]);
      	if (!isset($value) && $_op == \ORM\Core\Mapping\Operators::eq) {
      	  $string .= "$searchKey IS NULL";
      	} else if (!isset($value) && $_op == \ORM\Core\Mapping\Operators::neq) {
      	  $string .= "$searchKey IS NOT NULL";
      	} else {
      	  $string .= "$searchKey " . self::$_operatorsMapping[$_op] . " :$searchKey$post_key";
      	  $values[$searchKey.$post_key] = $value;
      	}
      } else {
      	$key = $filter;
      	$rKey = $this->_getReverseKey($key);
      	if (strpos($key, '.') !== false) {
      	  $key = str_replace('.', '_', $key);
      	}
      	$key = $this->_getMapFieldName($key);

        $value = $this->getField($key);
        if (is_array($value)) {
          if ($this->_isJson($rKey)) {
            $value = json_encode($value);
          } elseif ($this->_isArray($rKey)) {
            $value = serialize($value);
          }
        }
        if (!isset($value) && $_op == \ORM\Core\Mapping\Operators::eq) {
      	  $string .= "$searchKey IS NULL";
      	} else if (!isset($value) && $_op == \ORM\Core\Mapping\Operators::neq) {
      	  $string .= "$searchKey IS NOT NULL";
      	} else {
      	  // On génère le filtre
      	  if (isset($this->_operators[$key])) {
      	    $string .= "$key " . self::$_operatorsMapping[$this->_operators[$filter]] . " :$key";
      	  } else {
      	    $string .= "$key = :$key";
      	  }
      	  $values[$key] = $value;
      	}
      }
    }

    if ($par) {
    	$string .= ")";
    }

    return array(
        "string" => $string,
        "values" => $values
    );
  }

  /**
   * Liste les champs à insérer
   *
   * @return array
   */
  public function getCreateFields() {
    $insertRequest = "";
    $insertFields = "";
    $insertValues = array();
    // Parcours les champs pour retourner la recherche
    foreach ($this->_hasChanged as $key => $haschanged) {
      if ($haschanged) {
        $rKey = $this->_getReverseKey($key);
        if ($this->_isObjectType($rKey)) {
          if ($insertRequest != "") {
            $insertRequest .= ", ";
            $insertFields .= ", ";
          }
          $insertFields .= "$key";
          $insertRequest .= ":$key";
          if ($this->_isObjectList($rKey)) {
            // Génère un tableau
            $objects = array();
            foreach ($this->_fields[$key] as $k => $v) {
              // Récupère les champs du drivermapping
              $objects[$k] = $v->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getMappingFields();
            }
            if ($this->_isJson($rKey)) {
              $insertValues[$key] = json_encode($objects);
            } elseif ($this->_isArray($rKey)) {
              $insertValues[$key] = serialize($objects);
            } else {
              $insertValues[$key] = $objects;
            }
          } else {
            $fields = $this->_fields[$key]->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getMappingFields();
            if ($this->_isJson($rKey)) {
              $insertValues[$key] = json_encode($fields);
            } elseif ($this->_isArray($rKey)) {
              $insertValues[$key] = serialize($fields);
            }
            else {
              $insertValues[$key] = $fields;
            }
          }
        } else {
          if ($insertRequest != "") {
            $insertRequest .= ", ";
            $insertFields .= ", ";
          }
          $insertFields .= "$key";
          $insertRequest .= ":$key";
          // Récupère la valeur converti en SQL
          $insertValues[$key] = $this->getField($key);
          if (is_array($insertValues[$key])) {
            if ($this->_isJson($rKey)) {
              $insertValues[$key] = json_encode($insertValues[$key]);
            } elseif ($this->_isArray($rKey)) {
              $insertValues[$key] = serialize($insertValues[$key]);
            }
          }
        }
      }
    }
    return array(
        "insertRequest" => $insertRequest,
        "insertFields" => $insertFields,
        "insertValues" => $insertValues
    );
  }

  /**
   * Liste les champs à mettre à jour
   *
   * @return array
   */
  public function getUpdateFields() {
    $updateFields = "";
    $updateValues = array();
    // Parcours les objets pour trouver les hasChanged
    foreach ($this->_fields as $key => $value) {
      // Récupération de la clé
      $rKey = $this->_getReverseKey($key);
      if ($this->_isObjectType($rKey)) {
        if ($this->_isObjectList($rKey) && is_array($value)) {
          foreach ($value as $k => $v) {
            foreach ($v->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->hasChanged() as $objectKey => $objectHasChanged) {
              if ($objectHasChanged) {
                $this->_hasChanged[$key] = true;
                break;
              }
            }
            if (isset($this->_hasChanged[$key]) && $this->_hasChanged[$key]) {
              break;
            }
          }
        } elseif ($value instanceof \ORM\Core\Mapping\ObjectMapping) {
          foreach ($value->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->hasChanged() as $objectKey => $objectHasChanged) {
            if ($objectHasChanged) {
              $this->_hasChanged[$key] = true;
              break;
            }
          }
        }
      }
    }
    // Parcours les champs pour retourner la recherche
    foreach ($this->_hasChanged as $key => $haschanged) {
      if ($haschanged && !$this->_isPrimaryKey($key)) {
        $rKey = $this->_getReverseKey($key);
        if ($updateFields != "") {
          $updateFields .= ", ";
        }
        $updateFields .= "$key = :$key";
        if (!$this->_isObjectType($rKey)) {
          $updateValues[$key] = $this->getField($key);
          if (is_array($updateValues[$key])) {
            if ($this->_isJson($rKey)) {
              $updateValues[$key] = json_encode($updateValues[$key]);
            } elseif ($this->_isArray($rKey)) {
              $updateValues[$key] = serialize($updateValues[$key]);
            }
          }
        }
        else {
          $value = $this->_fields[$key];
          if (is_array($value)) {
            $mappingFields = array();
            foreach ($value as $k => $v) {
              // Récupère les champs update
              $mappingFields[$k] = $v->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getMappingFields();
            }
            if ($this->_isJson($rKey)) {
              $updateValues[$key] = json_encode($mappingFields);
            } elseif ($this->_isArray($rKey)) {
              $updateValues[$key] = serialize($mappingFields);
            } else {
              $updateValues[$key] = $mappingFields;
            }
          }
          else {
            // Récupère les champs update
            $mappingFields = $value->getDriverMappingInstanceByDriver($this->_mapping['Driver'])->getMappingFields();
            if ($this->_isJson($rKey)) {
              $updateValues[$key] = json_encode($mappingFields);
            } elseif ($this->_isArray($rKey)) {
              $updateValues[$key] = serialize($mappingFields);
            } else {
              $updateValues[$key] = $mappingFields;
            }
          }
        }
      }
    }
    return array(
        "updateFields" => $updateFields,
        "updateValues" => $updateValues
    );
  }

  /**
   * Donne la liste des champs à selectionner dans la requête
   *
   * @return string
   */
  public function getSelectFields() {
    $selectFields = "";
    if ($this->isCount()) {
      // On recherche un count
      $selectFields = "count(*)";
    } elseif (isset($this->_selectFields)) {
      // On recherche une liste de champs spécifiques
      foreach ($this->_selectFields as $field) {
        if ($selectFields != "") {
          $selectFields .= ", ";
        }
        $selectFields .= $field;
      }
    } else {
      // On recherche tous les champs
      $selectFields = "*";
    }
    return $selectFields;
  }

  /**
   * Récupère l'instruction ORDER BY si besoin
   *
   * @return string
   */
  public function getOrderBy() {
    $orderby = "";
    if (isset($this->_orderBy)) {
      $orderby = " ORDER BY " . $this->_orderBy;
      if (isset($this->_asc) && $this->_asc) {
        $orderby .= " ASC";
      } else {
        $orderby .= " DESC";
      }
    }
    return $orderby;
  }

  /**
   * Récupère l'instruction LIMIT si besoin
   *
   * @return string
   */
  public function getLimit() {
    $limit = "";
    if (isset($this->_limit)) {
      $limit = " LIMIT " . $this->_limit;
    }
    return $limit;
  }

  /**
   * Récupère l'instruction OFFSET si besoin
   *
   * @return string
   */
  public function getOffset() {
    $offset = "";
    if (isset($this->_offset) && isset($this->_limit)) {
      $offset = " OFFSET " . $this->_offset;
    }
    return $offset;
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
    return $this->_convertToSql($this->_fields[$key], isset($rKey) ?  : $this->_getReverseKey($key));
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
    $this->_fields[$key] = $this->_convertFromSql($value, isset($rKey) ?  : $this->_getReverseKey($key));
  }

  /**
   * Appel l'initialisation
   *
   * @see \ORM\Core\Drivers\DriverMapping::init()
   */
  public function init() {
    if (isset($this->_mapping['CollectionName'])) {
      $this->_tableName = $this->_mapping['CollectionName'];
    }
  }

  /**
   * Conversion d'une valeur de l'ORM en SQL
   *
   * @param mixed $value
   * @param string $mappingKey
   * @return string
   */
  protected function _convertToSql($value, $mappingKey = null) {
    if (is_array($value)) {
      if (isset($mappingKey) && $this->_isJson($mappingKey)) {
        $convertedValue = json_encode($value);
      } elseif (isset($mappingKey) && $this->_isArray($mappingKey)) {
        $convertedValue = serialize($value);
      } else {
        $convertedValue = $value;
      }
    } else if (isset($mappingKey) && $this->_isDateTime($mappingKey) || $value instanceof \DateTime) {
      $convertedValue = $value->format('r');
    } else {
      $convertedValue = $value;
    }
    return $convertedValue;
  }

  /**
   * Conversion d'une valeur SQL en valeur de l'ORM
   *
   * @param string $value
   * @param string $mappingKey
   * @return mixed
   */
  protected function _convertFromSql($value, $mappingKey) {
    if ($this->_isArray($mappingKey)) {
      $convertedValue = unserialize($value);
    } else if ($this->_isJson($mappingKey)) {
      $convertedValue = json_decode($value, true);
    } else if ($this->_isDateTime($mappingKey)) {
      $convertedValue = new \DateTime($value);
    } else {
      $convertedValue = $value;
    }
    return $convertedValue;
  }
}