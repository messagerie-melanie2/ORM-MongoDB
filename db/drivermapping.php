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
namespace ORM\DB;

/**
 * Gestion du mapping par driver
 * Tous les drivers mapping de l'ORM doivent l'implémenter
 *
 * @property string $instance_id Identifiant de l'instance courante
 * @property string $collection_name Nom de la collection courante
 * @property array $fields Liste des champs
 * @property array $search_fields Liste des champs recherchés
 * @property array $filter_fields Liste des champs à filtre avec leurs valeurs
 * @property array $update_fields Liste des champs à mettre à jour avec leur valeurs
 * @property array $create_fields Liste des champs à créer avec leurs valeurs
 */
abstract class DriverMapping {
  /**
   * Liste des instances des Drivers Mapping
   * @var array
   */
  private static $instances = [];

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
   * Récupèration de l'instance liée à une collection et un mapping
   * @param string $mapping Configuration de mapping pour l'instance
   * @return DriverMapping
   */
  public static function get_instance($mapping = null, $instance_id = null) {
    // Génération de l'identifiant
    if (!isset($instance_id)) {
      $instance_id = uniqid();
    }
    // Instancie l'instance
    if (!isset(self::$instances[$instance_id])) {
      self::$instances[$instance_id] = new self($mapping);
    }
    // Retourne l'instance du driver mapping
    return self::$instances[$instance_id];
  }

  /**
   * Fonction d'initialisation
   * Appelé par le constructeur de la classe abstraite
   */
  abstract function init();

  /**
   * Constructeur par défaut du driver mapping
   * Doit être appelé par tous les drivers mapping
   */
  public function __construct($mapping) {
    $this->_mapping = $mapping;
    // Initialisation
    $this->_hasChanged = array();
    $this->_fields = array();

    // Initialisation du driver
    $this->_driver = Driver::get_instance($this->_mapping['Driver']);

    // Appel l'initialisation
    $this->init();
  }

  /**
   * Getter/Setter pour savoir s'il s'agit d'une liste
   * @param boolean $isList
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
   * PHP magic to set an instance variable
   *
   * @param string $name Nom de la propriété
   * @param mixed $value Valeur de la propriété
   * @access public
   * @return
   * @ignore
   */
  public function __set($name, $value) {
    $this->_fields[$name] = $value;
    $this->_hasChanged[$name] = true;
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
    return $this->_driver->$name($arguments);
  }

}