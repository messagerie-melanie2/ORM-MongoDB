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
namespace ORM\Core\DB\PDO;

/**
 * Classe de mapping pour le driver PDO
 */
class PDOMapping extends \ORM\Core\DB\DriverMapping {
  /**
   * Nom de la table courante
   * @var string
   */
  protected $_tableName;

  /**
   * Mapping des opérateurs
   * @var array
   */
  private static $_operatorsMapping = array(
          \ORM\Core\Mapping\Operators::eq => '=',
          \ORM\Core\Mapping\Operators::and_ => 'AND',
          \ORM\Core\Mapping\Operators::or_ => 'OR',
          \ORM\Core\Mapping\Operators::gt => '>',
          \ORM\Core\Mapping\Operators::gte => '>=',
          \ORM\Core\Mapping\Operators::in => 'IN',
          \ORM\Core\Mapping\Operators::not_in => 'NOT IN',
          \ORM\Core\Mapping\Operators::like => 'LIKE',
          \ORM\Core\Mapping\Operators::lt => '<',
          \ORM\Core\Mapping\Operators::lte => '<=',
          \ORM\Core\Mapping\Operators::neq => '<>',
          \ORM\Core\Mapping\Operators::not => 'NOT',
  );

  /**
   * Getter pour le nom de la table
   * @return string
   */
  public function getTableName() {
    return $this->_tableName;
  }
  /**
   * Setter pour le nom de la table
   * @param string $tableName
   */
  public function setTableName($tableName) {
    $this->_tableName = $tableName;
  }

  /**
   * Récupération des champs mappés pour l'insertion dans la base de données
   * @return array
   */
  public function getMappingFields() {
    // TODO améliorer le mapping (DateTime, serialize, ...)
    return $this->_fields;
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
    foreach ($mappingFields as $key => $mappingField) {
      // TODO améliorer le mapping (DateTime, serialize, ...)
      $this->_fields[$key] = $mappingField;
    }
  }

  /**
   * Retourne la liste des champs de recherche avec les valeurs
   * TODO: Associer plus d'informations via les operations etc
   * Voir le getList de l'ORM
   * @param boolean $usePrimaryKeys [Optionnel] Utiliser les clés primaires pour la recherche
   * @param array $fieldsForSearch [Optionnel] Liste des champs à utiliser pour la recherche
   * @return array|string
   */
  public function getSearchFields() {
    $searchFields = "";
    $searchValues = array();

    if (isset($this->_filter)) {
      $result = $this->_filtersToString($this->_filter);
      $searchFields = $result['string'];
      $searchValues = $result['values'];
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
      // Parcours les champs pour retourner la recherche
      foreach ($this->_fieldsForSearch as $key => $use) {
        if ($use) {
          if ($searchFields != "") {
            $searchFields = " AND ";
          }
          if (isset($this->_operators[$key])) {
            $searchFields = "$key " . self::$_operatorsMapping[$this->_operators[$key]] . " :$key";
          }
          else {
            $searchFields = "$key = :$key";
          }
          $searchValues[$key] = $this->_fields[$key];

        }
      }
    }

    return array(
            'searchFields' => $searchFields,
            'searchValues' => $searchValues,
    );
  }

  /**
   * Génère un filtre SQL en fonction du filtre passé en tableau
   * @param array $filters
   * @param string $operators
   * @return array
   */
  private function _filtersToString($filters, $operators = null) {
    $string = "";
    $values = array();

    foreach ($filters as $op => $filter) {
      // Ajoute l'operateur de transition
      if ($string != "") {
        $string .= " " . self::$_operatorsMapping[$operator] . " ";
      }
      if (is_array($filter)) {
        // C'est un tableau, donc un nouveau filtre, on fait un appel recursif
        $result = $this->_filtersToString($filter, $op);
        if ($string == "") {
          $string .= $result['string'];
        }
        else {
          $string .= " (".$result['string'].")";
        }
        $values = array_merge($values, $result['values']);
      }
      else {
        // On génère le filtre
        if (isset($this->_operators[$filter])) {
          $string .= "$filter " . self::$_operatorsMapping[$this->_operators[$filter]] . " :$filter";
        }
        else {
          $string .= "$filter = :$filter";
        }
      }
    }

    return array("string" => $string, "values" => $values);
  }

  /**
   * Liste les champs à insérer
   * @return array
   */
  public function getCreateFields() {
    $insertRequest = "";
    $insertFields = "";
    $insertValues = array();
    // Parcours les champs pour retourner la recherche
    foreach ($this->_hasChanged as $key => $haschanged) {
      if ($haschanged) {
        if ($insertRequest != "") {
          $insertRequest = ", ";
          $insertFields = ", ";
        }
        $insertFields .= "$key";
        $insertRequest .= ":$key";
        $insertValues[$key] = $this->_fields[$key];
      }
    }
    return array(
            "insertRequest" => $insertRequest,
            "insertFields" => $insertFields,
            "insertValues" => $insertValues);
  }

  /**
   * Liste les champs à mettre à jour
   * @return array
   */
  public function getUpdateFields() {
    $updateFields = "";
    $insertValues = "";
    // Parcours les champs pour retourner la recherche
    foreach ($this->_hasChanged as $key => $haschanged) {
      if ($haschanged) {
        if ($updateFields != "") {
          $updateFields = ", ";
          $insertValues = ", ";
        }
        $updateFields .= ":$key";
      }
    }
    return $updateFields;
  }

  /**
   * Donne la liste des champs à selectionner dans la requête
   * @return string
   */
  public function getSelectFields() {
    $selectFields = "";
    if ($this->isCount()) {
      // On recherche un count
      $selectFields = "count(*)";
    }
    elseif (isset($this->_fields)) {
      // On recherche une liste de champs spécifiques
      foreach ($this->_fields as $field) {
        if ($selectFields != "") {
          $selectFields .= ", ";
        }
        $selectFields .= $field;
      }
    }
    else {
      // On recherche tous les champs
      $selectFields = "*";
    }
    return $selectFields;
  }

  /**
   * Récupère l'instruction ORDER BY si besoin
   * @return string
   */
  public function getOrderBy() {
    $orderby = "";
    if (isset($this->_orderBy)) {
      $orderby = " ORDER BY " . $this->_orderBy;
    }
    if (isset($this->_asc)
        && $this->_asc) {
      $orderby .= " ASC";
    }
    else {
      $orderby .= " DESC";
    }
    return $orderby;
  }

  /**
   * Récupère l'instruction LIMIT si besoin
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
   * @return string
   */
  public function getOffset() {
    $offset = "";
    if (isset($this->_offset)
        && isset($this->_limit)) {
      $offset = " OFFSET " . $this->_offset;
    }
    return $offset;
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
    $this->_tableName = $this->_mapping['CollectionName'];
  }
}