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
 * Driver MongoDB
 */
class MongoDB extends \ORM\DB\Driver {
  /**
   * Connexion courante vers le driver
   * @var \MongoClient
   */
  private $_connexion;
  /**
   * Base de données MongoDB courante
   * @var \MongoDB
   */
  private $_db;

  /**
   * Liste des collections MongoDB
   * @var \MongoCollection[]
   */
  private $_collections;

  /**
   * Connexion au serveur MongoDB
   *
   * @return boolean True si connexion OK
   */
  public function connect() {
    $options = isset($this->config['options']) ? $this->config['options'] : null;
    $driver_options = isset($this->config['driver_options']) ? $this->config['driver_options'] : null;
    // Initialisation de la liste des collections
    $this->_collections = array();
    // Connexion au serveur MongoDB
    $this->_connexion = new \MongoClient($this->dsn, $options, $driver_options);
    if ($this->_connexion->connect()) {
      $this->_db = $this->_connexion->selectDB($this->config['database']);
      return true;
    }
    else {
      return false;
    }
  }
  /**
   * Déconnexion de la base MongoDB
   */
  public function disconnect() {
    // Pas de déconnexion en MongoDB
    //$this->connexion->close();
    return true;
  }

  /**
   * Retourne la collection
   * @param string $collectionName
   * @return \MongoCollection
   */
  private function _getCollection($collectionName) {
    if (!isset($this->_collections[$collectionName])) {
      $this->_collections[$collectionName] = $this->_db->$collectionName;
    }
    return $this->_collections[$collectionName];
  }

  /**
   * Création d'un objet
   * @param MongoDBMapping $args
   */
  public function create(MongoDBMapping $args) {
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      $ret = $collection->insert($args->getMappingFields());
    }
    catch (\MongoException $mongoEx) {

    }
    catch (\Exception $ex) {

    }
  }

  /**
   * Récupération d'un objet
   * @param MongoDBMapping $args
   */
  public function read(MongoDBMapping $args) {
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      if ($args->isList()) {
        $cursor = $collection->find($args->getSearchFields());
      }
      else {
        $result = $collection->findOne($args->getSearchFields());
      }

    }
    catch (\MongoException $mongoEx) {

    }
    catch (\Exception $ex) {

    }
  }

  /**
   * Mise à jour d'un objet
   * @param MongoDBMapping $args
   */
  public function update(MongoDBMapping $args) {
    try {
      $collection = $this->_getCollection($args->getCollectionName());
    }
    catch (\MongoException $mongoEx) {

    }
    catch (\Exception $ex) {

    }
  }

  /**
   * Suppression d'un objet
   * @param MongoDBMapping $args
   */
  public function delete(MongoDBMapping $args) {
    try {
      $collection = $this->_getCollection($args->getCollectionName());
    }
    catch (\MongoException $mongoEx) {

    }
    catch (\Exception $ex) {

    }
  }
}