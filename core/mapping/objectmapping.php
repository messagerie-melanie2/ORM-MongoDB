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
namespace ORM\Core\Mapping;

/**
 * Gestion du mapping par objet
 * Tous les objets doivent l'implémenter
 */
abstract class ObjectMapping {
  /**
   * Type de l'objet courant (utilisé pour le mapping)
   * @var string
   */
  private $_objectType;
  /**
   * Liste des instances
   * @var array[]
   */
  private $_instances;
  /**
   * Driver Mapping associé à le ou les objets courants
   * @var \ORM\Core\DB\DriverMapping[]
   */
  private $_driverMappingInstances;

  /**
   * Constructeur par défaut de l'object mapping
   * Doit être appelé par tous les objets
   */
  public function __construct($driverMappingInstances = null) {
    // Récupération du type de l'objet
    $this->_objectType = str_replace('ORM\\API\\', '', get_called_class());
    // Récupération de la configuration du mapping
    $mapping = \ORM\Core\Config\Config::get('mapping');

    // Initialisation des array
    $this->_instances = array();
    $this->_driverMappingInstances = array();
    $this->_childrenObjects = array();

    if (isset($driverMappingInstances)) {
      $this->setDriverMappingInstances($driverMappingInstances);
    }
    else {
      // Parcours les mappings disponibles
      foreach ($mapping as $map) {
        if ($map['ObjectType'] == $this->_objectType) {
          // Initialisation de l'instance
          $instance = \ORM\Core\DB\DriverMapping::get_instance($map);
          $this->_driverMappingInstances[$instance->instanceId()] = $instance;
          $this->_instances[] = $instance->instanceId();
        }
      }
    }

    // Appel l'initialisation de l'objet
    $this->init();
  }

  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  abstract protected function init();

  /**
   * Retourne les instances nécessaire au bon fonctionnement de l'objet
   * Permet de faire des enfants de l'objet parent utilisant les mêmes instances
   * @return \ORM\Core\DB\DriverMapping[]
   */
  public function getDriverMappingInstances() {
    return $this->_driverMappingInstances;
  }
  /**
   * Retourne l'instance de driver mapping en fonction du driver configuré dans le mapping
   * @param string $objectType
   * @return \ORM\Core\DB\DriverMapping
   */
  public function getDriverMappingInstanceByDriver($driver) {
    foreach ($this->_driverMappingInstances as $driverMappingInstance) {
      if ($driverMappingInstance->mapping()['Driver'] == $driver) {
        return $driverMappingInstance;
      }
    }
  }
  /**
   * Retourne l'instance de driver mapping en fonction de l'object type configuré dans le mapping
   * @param string $objectType
   * @return \ORM\Core\DB\DriverMapping
   */
  public function getDriverMappingInstanceByObjectType($objectType) {
    foreach ($this->_driverMappingInstances as $driverMappingInstance) {
      if ($driverMappingInstance->mapping()['ObjectType'] == $objectType) {
        return $driverMappingInstance;
      }
    }
  }
  /**
   * Défini les instances de drivermapping de l'objet courant
   * @param \ORM\Core\DB\DriverMapping[] $driverMappingInstances
   */
  public function setDriverMappingInstances($driverMappingInstances) {
  	$this->_driverMappingInstances = array();
    $this->_instances = array();
    foreach ($driverMappingInstances as $instance) {
      $this->_instances[] = $instance->instance_id;
      $this->_driverMappingInstances[$instance->instance_id] = $instance;
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
    foreach($this->_instances as $instance_id) {
      $mapping = $this->_driverMappingInstances[$instance_id]->mapping();
      // Gestion du mapping configuré
      if (isset($mapping['fields'])
          && isset($mapping['fields'][$name])) {
        $field_mapping = $mapping['fields'][$name];
        // Si un typage est requis
        if (isset($field_mapping['type'])) {
          \ORM\Core\Tools\Tools::convert($value, $field_mapping['type']);
        }
        // Nom de mapping
        if (isset($field_mapping['name'])) {
          $name = $field_mapping['name'];
        }
        $this->_driverMappingInstances[$instance_id]->$name = $value;
      }
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
    foreach($this->_instances as $instance_id) {
      $mapping = $this->_driverMappingInstances[$instance_id]->mapping();
      // Gestion du mapping configuré
      if (isset($mapping['fields'])
          && isset($mapping['fields'][$name])) {
        $field_mapping = $mapping['fields'][$name];

        // Nom de mapping
        if (isset($field_mapping['name'])) {
          $name = $field_mapping['name'];
        }
        $value = $this->_driverMappingInstances[$instance_id]->$name;
        // Si le champ est un objet complexe
        if (isset($field_mapping['ObjectType']) && !isset($value)) {
          if (isset($field_mapping['type'])
              && $field_mapping['type'] == 'list') {
            $value = array();
          }
          else {
            $class_name = 'ORM\\API\\'.$field_mapping['ObjectType'];
            $value = new $class_name();
            $this->_driverMappingInstances[$instance_id]->$name = $value;
          }
        }
        // Si un typage est requis
        if (isset($field_mapping['type'])) {
          \ORM\Core\Tools\Tools::convert($value, $field_mapping['type']);
        }
        return $value;
      }
      else {
        return $this->_driverMappingInstances[$instance_id]->$name;
      }
    }
    return null;
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
    foreach($this->_instances as $instance_id) {
      $mapping = $this->_driverMappingInstances[$instance_id]->mapping();
      if (isset($mapping['fields'])
          && isset($mapping['fields'][$name])) {
        $field_mapping = $mapping['fields'][$name];
        // Nom de mapping
        if (isset($field_mapping['name'])) {
          $name = $field_mapping['name'];
        }
      }
      return isset($this->_driverMappingInstances[$instance_id]->$name);
    }
    return false;
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
    foreach($this->_instances as $instance_id) {
      $mapping = $this->_driverMappingInstances[$instance_id]->mapping();
      if (isset($mapping['fields'])
          && isset($mapping['fields'][$name])) {
        $field_mapping = $mapping['fields'][$name];
        // Nom de mapping
        if (isset($field_mapping['name'])) {
          $name = $field_mapping['name'];
        }
      }
      unset($this->_driverMappingInstances[$instance_id]->$name);
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
    $ret = null;
    foreach($this->_instances as $instance_id) {
      $mapping = $this->_driverMappingInstances[$instance_id]->mapping();
      if (isset($mapping['methods'])
          && isset($mapping['methods'][$name])) {
        $methods_mapping = $mapping['methods'][$name];

        // Nom de mapping
        if (isset($methods_mapping['name'])) {
          $name = $methods_mapping['name'];
        }
        else if (isset($methods_mapping['method'])) {
          // Gestion des méthodes imbriquées
          foreach ($methods_mapping['method'] as $key => $value) {
            // Appel de la méthode
            $res = $this->__call($key, $arguments);
            $name = $value[$res];
            return $this->__call($name, $arguments);
          }
        }
        // Sagit-il d'une liste ?
        if (isset($methods_mapping['return'])
            && $methods_mapping['return'] == 'list') {
          $this->_driverMappingInstances[$instance_id]->isList(true);
        }
        else {
          $this->_driverMappingInstances[$instance_id]->isList(false);
        }
        // Mapping des data
        if (isset($methods_mapping['data'])) {
          foreach ($methods_mapping['data'] as $key => $value) {
            // Map de donnée avec le driver Mapping, via l'identifiant du tableau
            $this->_driverMappingInstances[$instance_id]->$key($value);
          }
        }
        // Mapping des paramètres
        if (isset($methods_mapping['arguments'])) {
          foreach ($methods_mapping['arguments'] as $key => $argument) {
            if (isset($arguments[$key])) {
              // Map d'argument avec le driver Mapping, via l'identifiant du tableau
              $this->_driverMappingInstances[$instance_id]->$argument($arguments[$key]);
            }
          }
        }
        // Appel de la méthode
        $result = $this->_driverMappingInstances[$instance_id]->$name($methods_mapping);

        // Combinaison des résultats
        if (!isset($methods_mapping['results'])
            || !isset($methods_mapping['results']) != 'combined') {
          if (isset($methods_mapping['return'])
              && $methods_mapping['return'] == 'list') {
            $ret = array();
            foreach ($result as $key => $value) {
              $class = get_called_class();
              $object = new $class(array($value));
              $ret[$key] = $object;
            }
          }
          else {
            $ret = $result;
          }

          break;
        }
        else {
          // Gestion des résultats
          if (!isset($ret)) {
            $ret = $result;
          }
          else {
            // Type retourné par la méthode et imbrication des résultats
            switch ($methods_mapping['return']) {
              case 'boolean':
                if (isset($methods_mapping['operator'])
                    && $methods_mapping['operator'] = 'or') {
                  $ret = $ret || $result;
                }
                else {
                  $ret = $ret && $result;
                }
                break;
              case 'integer':
                $ret = $ret + $result;
                break;
              case 'string':
                if (isset($methods_mapping['concat'])) {
                  $ret = $ret . $methods_mapping['concat'] . $result;
                }
                else {
                  $ret = $ret . $result;
                }
                break;
              case 'list':
                if (!is_array($ret)) {
                  $ret = array();
                }
                foreach ($result as $key => $value) {
                  // Initialise les résultats
                  $class = get_called_class();
                  $object = new $class(array($value));
                  $ret[$key] = $object;
                }
                break;
            }
          }
        }
      }
    }
    // Retourne les résultats
    return $ret;
  }
}