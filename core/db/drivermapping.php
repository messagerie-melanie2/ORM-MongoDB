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
namespace ORM\Core\DB;

/**
 * Gestion du mapping par driver
 * Tous les drivers mapping de l'ORM doivent l'implémenter
 *
 */
abstract class DriverMapping {
  /**
   * Liste des instances des Drivers Mapping
   * @var array
   */
  private static $instances = array();

  /**
   * Identifiant de l'instance courante
   * @var string
   */
  private $_instance_id;
  /**
   * Driver de connexion à la base de données
   * @var resource
   */
  private $_driver;
  /**
   * Configuration du mapping
   * @var array
   */
  protected $_mapping;
  /**
   * Défini si les propriété ont changé pour les requêtes SQL
   * @var array
   */
  protected $_hasChanged;
  /**
   * Liste des champs
   * @var array
   */
  protected $_fields;
  /**
   * Est-ce que le résultat doit être une liste
   * @var boolean
   */
  protected $_isList;
  /**
   * Est-ce que la requête demande un count
   * @var boolean
   */
  protected $_isCount;
  /**
   * Liste des champs à lister pour une requête de type search
   * @var array
   */
  protected $_listFields;
  /**
   * Liste des operateurs utilisés dans le cas d'une requête de type search
   * @var array
   */
  protected $_operators;
  /**
   * Filtre demandé pour une requête de type search
   * @var string
   */
  protected $_filter;
  /**
   * Nom du ou des champs utilisé pour le tri pour une requête de type search
   * @var string|array
   */
  protected $_orderBy;
  /**
   * Le tri doit il être croissant pour une requête de type search
   * @var boolean
   */
  protected $_asc;
  /**
   * Nombre d'objets retournés (utile pour la pagination) pour une requête de type search
   * @var integer
   */
  protected $_limit;
  /**
   * Offset de début pour les résultats (utile pour la pagination) pour une requête de type search
   * @var integer
   */
  protected $_offset;
  /**
   * Liste des champs non sensibles à la casse pour une requête de type search
   * @var array
   */
  protected $_unsensitiveFields;
  /**
   * Est-ce que la requête de recherche doit utiliser les clés primaires
   * @var boolean
   */
  protected $_usePrimaryKeys;
  /**
   * Liste des champs à utiliser pour la recherche (si pas de clé primaire)
   * @var array
   */
  protected $_fieldsForSearch;

  /**
   * Récupèration de l'instance liée à une collection et un mapping
   * @param string $mapping Configuration de mapping pour l'instance
   * @return DriverMapping
   */
  public static function get_instance(&$mapping = null, $instance_id = null) {
    // Génération de l'identifiant
    if (!isset($instance_id)) {
      $instance_id = uniqid();
    }
    // Instancie l'instance
    if (!isset(self::$instances[$instance_id])) {
      $driver = $mapping['Driver'];
      $driverType = \ORM\Core\Config\Config::get("db.$driver.driver");
      $class = "\\ORM\\Core\\DB\\".$driverType."\\".$driverType."Mapping";
      self::$instances[$instance_id] = new $class($mapping, $instance_id);
    }
    // Retourne l'instance du driver mapping
    return self::$instances[$instance_id];
  }

  /**
   * Constructeur par défaut du driver mapping
   * Doit être appelé par tous les drivers mapping
   * @param $mapping Pointeur vers la configuration du mapping
   * @param $instance_id Identifiant de l'instance du driver mapping
   */
  public function __construct(&$mapping, $instance_id) {
    $this->_mapping = $mapping;
    // Génération de l'identifiant de l'instance
    $this->_instance_id = $instance_id;

    // Inverse le mapping pour faciliter le traitement
    $this->_reverseMapping();
    // Initialisation
    $this->_hasChanged = array();
    $this->_fields = array();

    // Si des méthodes sont configurés on récupère le driver associé
    if (isset($mapping['methods'])) {
      // Initialisation du driver
      $this->_driver = Driver::get_instance($this->_mapping['Driver']);
    }

    // Initialise les arguments
    $this->_init_arguments();

    // Appel l'initialisation
    $this->init();
  }

  /**
   * Fonction d'initialisation
   * Appelé par le constructeur de la classe abstraite
   */
  abstract function init();
  /**
   * Récupération des champs mappés pour l'insertion dans la base de données
   * @return array
   */
  abstract function getMappingFields();
  /**
   * Défini les champs mappés suite à une lecture dans la base de données
   * @recursive
   * @param array $mappingFields
   * @param string clé parente
   */
  abstract function setMappingFields($mappingFields, $mKey = null);
  /**
   * Retourne la liste des champs de recherche avec les valeurs
   * TODO: Associer plus d'informations via les operations etc
   * Voir le getList de l'ORM
   * @return array
   */
  abstract function getSearchFields();
  /**
   * Liste les champs à insérer
   * @return array
   */
  abstract function getCreateFields();
  /**
   * Liste les champs à mettre à jour
   * @return array
   */
  abstract function getUpdateFields();
  /**
   * Récupération des options pour la requête
   * @return array
   */
  abstract function getOptions();

  /**
   * Inverse le mapping de la configuration
   * Permet de retrouver plus facilement les champs
   */
  private function _reverseMapping() {
    if (!isset($this->_mapping['reverse'])) {
      $this->_mapping['reverse'] = array();
      foreach ($this->_mapping['fields'] as $key => $field) {
        if (is_array($field)) {
          if (!isset($field['name'])) {
            continue;
          }
          $name = $field['name'];
        }
        else {
          $name = $field;
        }
        $this->_mapping['reverse'][$name] = $key;
        // Elements enfants
        if (isset($field['elements'])) {
          if (!is_array($field['elements'])) {
            $field['elements'] = array($field['elements']);
          }
          foreach ($field['elements'] as $element) {
            $this->_mapping['reverse'][$element] = $key;
          }
        }
      }
    }
  }

  /**
   * Retourne le nom mappé dans la base de données
   * @param string $name
   * @return string
   */
  protected function _getMapFieldName($name) {
    $mapName = $name;
    if (isset($this->_mapping['fields'])
        && isset($this->_mapping['fields'][$mapName])) {
      if (is_array($this->_mapping['fields'][$mapName])
          && isset($this->_mapping['fields'][$mapName]['name'])) {
        $mapName = $this->_mapping['fields'][$mapName]['name'];
      }
      else {
        $mapName = $this->_mapping['fields'][$mapName];
      }
    }
    return $mapName;
  }

  /**
   * Initialisation des arguments de recherche
   */
  public function _init_arguments() {
    $this->_isList = false;
    $this->_isCount = false;
    $this->_listFields = array();
    $this->_operators = array();
    $this->_filter = null;
    $this->_orderBy = null;
    $this->_asc = false;
    $this->_limit = null;
    $this->_offset = null;
    $this->_unsensitiveFields = array();
    $this->_usePrimaryKeys = false;
    $this->_fieldsForSearch = null;
  }

  /**
   * Getter/setter pour l'instance id
   * @param string $instanceId
   * @return string
   */
  public function instanceId($instanceId = null) {
    if (isset($instanceId)) {
      $this->_instance_id = $instanceId;
    }
    else {
      return $this->_instance_id;
    }
  }

  /**
   * Permet de copier par référence les fields
   * @param string $fields
   */
  public function fields($fields = null) {
    if (isset($fields)) {
      $this->_fields = $fields;
    }
    else {
      return $this->_fields;
    }
  }
  /**
   * Permet de copier par référence le hasChanged
   * @param string $hasChanged
   */
  public function hasChanged($hasChanged = null) {
    if (isset($hasChanged)) {
      $this->_hasChanged = $hasChanged;
    }
    else {
      return $this->_hasChanged;
    }
  }

  /**
   * Getter/setter pour le mapping
   * @param array $mapping
   * @return array
   */
  public function mapping($mapping = null) {
    if (isset($mapping)) {
      $this->_mapping = $mapping;
    }
    else {
      return $this->_mapping;
    }
  }
  /**
   * Getter/Setter pour savoir s'il s'agit d'une liste
   * @param boolean $isList
   * @return boolean
   */
  public function isList($isList = null) {
    if (isset($isList)) {
      $this->_isList = $isList;
    }
    else {
      return $this->_isList;
    }
  }
  /**
   * Getter/Setter pour savoir s'il faut faire un count
   * @param boolean $isCount
   * @return boolean
   */
  public function isCount($isCount = null) {
    if (isset($isCount)) {
      $this->_isCount = $isCount;
    }
    else {
      return $this->_isCount;
    }
  }
  /**
   * Getter/Setter de la liste des champs à lister pour une requête de type search
   * @param array $listFields
   * @return array
   */
  public function listFields($listFields = null) {
    if (isset($listFields)) {
      // Mapping des noms de champs
      $this->_listFields = array_map(array($this, '_getMapFieldName'), $listFields);
    }
    else {
      return $this->_listFields;
    }
  }
  /**
   * Getter/Setter de la liste des operateurs utilisés dans le cas d'une requête de type search
   * @param string $operators
   * @return string
   */
  public function operators($operators = null) {
    if  (isset($operators)) {
      $mapOperators = array();
      foreach ($operators as $key => $value) {
        $mapOperators[$this->_getMapFieldName($key)] = $value;
      }
      unset($operators);
      $this->_operators = $mapOperators;
    }
    else {
      return $this->_operators;
    }
  }
  /**
   * Getter/Setter du filtre demandé pour une requête de type search
   * @param string $filter
   * @return string
   */
  public function filter($filter = null) {
    if (isset($filter)) {
      // Mapping des noms de champs (appel recursif)
      array_walk_recursive($filter, function ($item, $key) {
        $item = $this->_getMapFieldName($item);
      });
      $this->_filter = $filter;
    }
    else {
      return $this->_filter;
    }
  }
  /**
   * Getter/Setter du nom du ou des champs utilisé pour le tri pour une requête de type search
   * @param string|array $orderBy
   * @return string|array
   */
  public function orderBy($orderBy = null) {
    if (isset($orderBy)) {
      if (is_array($orderBy)) {
        // Mapping des noms de champs
        $this->_orderBy = array_map(array($this, '_getMapFieldName'), $orderBy);
      }
      else {
        $this->_orderBy = $this->_getMapFieldName($orderBy);
      }
    }
    else {
      return $this->_orderBy;
    }
  }
  /**
   * Getter/Setter du tri doit il être croissant pour une requête de type search
   * @param boolean $asc
   * @return boolean
   */
  public function asc($asc = null) {
    if (isset($asc)) {
      $this->_asc = $asc;
    }
    else {
      return $this->_asc;
    }
  }
  /**
   * Getter/Setter du nombre d'objets retournés (utile pour la pagination) pour une requête de type search
   * @param number $limit
   * @return number
   */
  public function limit($limit = null) {
    if (isset($limit)) {
      $this->_limit = $limit;
    }
    else {
      return $this->_limit;
    }
  }
  /**
   * Getter/Setter de l'offset de début pour les résultats (utile pour la pagination) pour une requête de type search
   * @param number $offset
   * @return number
   */
  public function offset($offset = null) {
    if (isset($offset)) {
      $this->_offset = $offset;
    }
    else {
      return $this->_offset;
    }
  }
  /**
   * Getter/Setter de la liste des champs non sensibles à la casse pour une requête de type search
   * @param array $unsensitiveFields
   * @return array
   */
  public function unsensitiveFields($unsensitiveFields = null) {
    if (isset($unsensitiveFields)) {
      // Mapping des noms de champs
      $this->_unsensitiveFields = array_map(array($this, '_getMapFieldName'), $unsensitiveFields);
    }
    else {
      return $this->_unsensitiveFields;
    }
  }
  /**
   * Getter/Setter si on utiliser les clés primaires pour la recherche ou non
   * @param boolean $usePrimaryKeys
   * @return boolean
   */
  public function usePrimaryKeys($usePrimaryKeys = null) {
    if (isset($usePrimaryKeys)) {
      $this->_usePrimaryKeys = $usePrimaryKeys;
    }
    else {
      return $this->_usePrimaryKeys;
    }
  }
  /**
   * Getter/Setter pour la liste des champs à utiliser pour la requête de recherche
   * @param array $fieldsForSearch
   * @return array
   */
  public function fieldsForSearch($fieldsForSearch = null) {
    if (isset($fieldsForSearch)) {
      // Mapping des noms de champs
      $mapfieldsForSearch = array();
      foreach ($fieldsForSearch as $key => $value) {
        $mapfieldsForSearch[$this->_getMapFieldName($key)] = $value;
      }
      unset($fieldsForSearch);
      $this->_fieldsForSearch = $mapfieldsForSearch;
    }
    else {
      return $this->_fieldsForSearch;
    }
  }

  /**
   * PHP magic to set an instance variable
   *
   * @param string $name Nom de la propriété
   * @param mixed $value Valeur de la propriété
   * @access public
   * @return
   * @ignore
   */
  public function __set($name, $value) {
    if (!isset($this->_fields[$name]) || $this->_fields[$name] !=  $value) {
      $this->_fields[$name] = $value;
      $this->_hasChanged[$name] = true;
    }
  }
  /**
   * PHP magic to get an instance variable
   *
   * @param string $name Nom de la propriété
   * @access public
   * @return
   * @ignore
   */
  public function __get($name) {
    if (isset($this->_fields[$name])) {
      return $this->_fields[$name];
    }
    else {
      return null;
    }
  }
  /**
   * PHP magic to check if an instance variable is set
   *
   * @param string $name Nom de la propriété
   * @access public
   * @return
   * @ignore
   */
  public function __isset($name) {
    return isset($this->_fields[$name]);
  }
  /**
   * PHP magic to remove an instance variable
   *
   * @param string $name Nom de la propriété
   * @access public
   * @return
   * @ignore
   */
  public function __unset($name) {
    if (isset($this->_fields[$name])) {
      unset($this->_fields[$name]);
      $this->_hasChanged[$name] = true;
    }
  }
  /**
   * PHP magic to implement any getter, setter, has and delete operations
   * on an instance variable.
   * Methods like e.g. "SetVariableName($x)" and "GetVariableName()" are supported
   *
   * @param string $name Nom de la methode
   * @param array $arguments Arguments de la methode
   * @access public
   * @return mixed
   * @ignore
   */
  public function __call($name, $arguments) {
    // Récupération du mapping de la méthode
    $methods_mapping = $arguments[0];
    // Appel la méthode
    $result = $this->_driver->$name($this);
    if ($this->isList()) {
      if (isset($methods_mapping['mapData'])
          && $methods_mapping['mapData']) {
        $data = array();
        foreach ($result as $res) {
          $object = self::get_instance($this->mapping());
          $object->setMappingFields($res);
          $data[] = $object;
        }
        $result = $data;
      }
    }
    elseif (isset($methods_mapping['mapData'])
        && $methods_mapping['mapData']) {
      if (isset($result)) {
        $this->setMappingFields($result);
      }
    }
    // Gestion des resultats
    if (isset($methods_mapping['return'])) {
      switch ($methods_mapping['return']) {
        case 'boolean':
          if (!is_bool($result)) {
            $result = isset($result);
          }
          break;
        case 'array':
          if (!is_array($result)) {
            $result = array($result);
          }
          break;
        case 'integer':
          if (!is_int($result)) {
            $result = intval($result);
          }
          break;
      }
    }
    // Réinitialise les arguments
    $this->_init_arguments();
    return $result;
  }

  /**
   * Retourne la clé inversé pour retrouver le mapping
   * @param string $key
   * @return string
   */
  protected function _getReverseKey($key) {
    $rKey = $key;
    if (isset($this->_mapping['reverse'][$rKey])) {
      $rKey = $this->_mapping['reverse'][$rKey];
    }
    return $rKey;
  }
  /**
   * Retourne si la clé est un Object Type
   * @param string $key
   * @return boolean
   */
  protected function _isObjectType($key) {
    return isset($this->_mapping['fields'])
      && isset($this->_mapping['fields'][$key])
      && isset($this->_mapping['fields'][$key]['ObjectType']);
  }
  /**
   * Retourne si la clé est un type Liste d'un Object Type
   * @param string $key
   * @return boolean
   */
  protected function _isObjectList($key) {
    return isset($this->_mapping['fields'][$key]['type'])
      && $this->_mapping['fields'][$key]['type'] == 'list';
  }
  /**
   * Retourne si la clé est une DateTime
   * @param string $key
   * @return boolean
   */
  protected function _isDateTime($key) {
    return isset($this->_mapping['fields'])
      && isset($this->_mapping['fields'][$key])
      && isset($this->_mapping['fields'][$key]['type'])
      && $this->_mapping['fields'][$key]['type'] == 'datetime';
  }
  /**
   * Retourne si la clé est un tableau
   * @param string $key
   * @return boolean
   */
  protected function _isArray($key) {
    return isset($this->_mapping['fields'])
      && isset($this->_mapping['fields'][$key])
      && isset($this->_mapping['fields'][$key]['type'])
      && $this->_mapping['fields'][$key]['type'] == 'array';
  }
}