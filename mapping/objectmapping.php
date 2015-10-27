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
namespace ORM\Mapping;

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
   * Configuration du mapping pour le ou les objets courants
   * @var array[]
   */
  private $_mapping;
  /**
   * Driver Mapping associé à le ou les objets courants
   * @var \ORM\DB\DriverMapping[]
   */
  private $_driverMappingInstance;
  /**
   * Défini si l'objet courant existe ou pas
   * @var boolean
   */
  private $_isExists;

  /**
   * Constructeur par défaut de l'object mapping
   * Doit être appelé par tous les objets
   */
  public function __construct() {
    // Récupération du type de l'objet
    $this->_objectType = str_replace('ORM\\Mapping\\', '', get_class());
    // Récupération de la configuration du mapping
    $mapping = \ORM\Config\Config::get('mapping');

    // Initialisation des array
    $this->_mapping = array();
    $this->_driverMappingInstance = array();

    // Parcours les mappings disponibles
    foreach ($mapping as $map) {
      if ($map['ObjectType'] == $this->_objectType) {
        // Initialisation de l'instance
        $instance = \ORM\DB\DriverMapping::get_instance($map);
        $this->_driverMappingInstance[$instance->instance_id] = $instance;
        $this->_mapping[$instance->instance_id] = $map;
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
   * PHP magic to set an instance variable
   *
   * @param string $name Nom de la propriété
   * @param mixed $value Valeur de la propriété
   * @access public
   * @return
   * @ignore
   */
  public function __set($name, $value) {
    foreach($this->_mapping as $instance_id => $mapping) {
      // Gestion du mapping configuré
      if (isset($mapping['fields'])
          && isset($mapping['fields'][$name])) {
        $field_mapping = $mapping['fields'][$name];
        // Si un typage est requis
        if (isset($field_mapping['type'])) {
          switch (strtolower($field_mapping['type'])) {
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
                if ($value instanceof \DateTime) {
                  if (isset($field_mapping['format'])) {
                    $value = $value->format($field_mapping['format']);
                  }
                  else {
                    $value = $value->format('Y-m-d H:i:s');
                  }
                }
                else {
                  if (isset($field_mapping['format'])) {
                    $value = date($field_mapping['format'], strtotime($value));
                  }
                  else {
                    $value = date('Y-m-d H:i:s', strtotime($value));
                  }
                }
              }
              catch (Exception $ex) {
                // Une erreur s'est produite, on met une valeur par défaut pour le pas bloquer la lecture des données
                $value = "1970-01-01 00:00:00";
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
            case 'string':
            default:
              // Gérer la taille si besoin
              if (isset($field_mapping['size'])) {
                $value = substr($value, 0, $field_mapping['size']);
              }
              break;
          }
        }
        // Nom de mapping
        if (isset($field_mapping['name'])) {
          $name = $field_mapping['name'];
        }
        $this->_driverMappingInstance[$instance_id]->$name = $value;
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
    foreach($this->_mapping as $instance_id => $mapping) {
      // Gestion du mapping configuré
      if (isset($mapping['fields'])
          && isset($mapping['fields'][$name])) {
        $field_mapping = $mapping['fields'][$name];
        // Nom de mapping
        if (isset($field_mapping['name'])) {
          $name = $field_mapping['name'];
        }
      }
      return $this->_driverMappingInstance[$instance_id]->$name;
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
    foreach($this->_mapping as $instance_id => $mapping) {
      if (isset($mapping['fields'])
          && isset($mapping['fields'][$name])) {
        $field_mapping = $mapping['fields'][$name];
        // Nom de mapping
        if (isset($field_mapping['name'])) {
          $name = $field_mapping['name'];
        }
      }
      return isset($this->_driverMappingInstance[$instance_id]->$name);
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
    foreach($this->_mapping as $instance_id => $mapping) {
      if (isset($mapping['fields'])
          && isset($mapping['fields'][$name])) {
        $field_mapping = $mapping['fields'][$name];
        // Nom de mapping
        if (isset($field_mapping['name'])) {
          $name = $field_mapping['name'];
        }
      }
      unset($this->_driverMappingInstance[$instance_id]->$name);
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
    foreach($this->_mapping as $instance_id => $mapping) {
      if (isset($mapping['methods'])
          && isset($mapping['methods'][$name])) {
        $methods_mapping = $mapping['methods'][$name];
        // Nom de mapping
        if (isset($methods_mapping['name'])) {
          $name = $methods_mapping['name'];
        }
        // Sagit-il d'une liste ?
        if (isset($methods_mapping['return'])
            && $methods_mapping['return'] == 'list') {
          $this->_driverMappingInstance->isList(true);
        }
        else {
          $this->_driverMappingInstance->isList(false);
        }
        // Mapping des paramètres
        if (isset($methods_mapping['arguments'])) {
          foreach ($methods_mapping['arguments'] as $key => $argument) {
            // Map d'argument avec le driver Mapping, via l'identifiant du tableau
            $this->_driverMappingInstance->$argument($arguments[$key]);
          }
        }
        // Appel de la méthode
        $result = $this->_driverMappingInstance->$name();
        // Combinaison des résultats
        if (!isset($methods_mapping['results'])
            || !isset($methods_mapping['results']) != 'combined') {
          $ret = $result;
          break;
        }
        else {
          // Gestion des résultats
          if (!isset($ret)) {
            $ret = $result;
          }
          else {
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