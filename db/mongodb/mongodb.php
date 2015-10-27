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
    $ret = null;
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      $ret = $collection->insert($args->getMappingFields(), $args->getOptions());
    }
    catch (\MongoCursorException  $mongoCursorEx) {

    }
    catch (\MongoCursorTimeoutException $mongoCursorTimeoutEx) {

    }
    catch (\MongoException $mongoEx) {

    }
    catch (\Exception $ex) {

    }
    return $ret;
  }

  /**
   * Récupération d'un objet
   * @param MongoDBMapping $args
   */
  public function read(MongoDBMapping $args) {
    $ret = null;
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      if ($args->isList()) {
        $cursor = $collection->find($args->getSearchFields(), $args->getListFields());
        // Récupération des paramètres du curseur
        $limit = $args->limit();
        if (isset($limit)
            && is_numeric($limit)) {
          // Limit sur le curseur mongo
          $cursor->limit($limit);
        }
        $offset = $args->offset();
        if (isset($offset)
            && is_numeric($offset)) {
          // skip sur le curseur mongo
          $cursor->skip($offset);
        }
        $orderBy = $args->orderBy();
        $asc = $args->asc();
        if (isset($orderBy)) {
          $sort = array();
          if (is_array($orderBy)) {
            foreach ($orderBy as $field) {
              $sort[$field] = $asc ? 1 : -1;
            }
          }
          else {
            $sort[$orderBy] = $asc ? 1 : -1;
          }
          // Sort sur le curseur mongo
          $cursor->sort($sort);
        }
        $mongoMaps = array();
        // Traitement des résultats
        while ($cursor->hasNext()) {
          $data = $cursor->next();
          $mongoMap = new MongoDBMapping($args->mapping());
          $mongoMap->setMappingFields($data);
          $mongoMaps[] = $mongoMap;
        }
        $ret = $mongoMaps;
      }
      else {
        $result = $collection->findOne($args->getSearchFields(), $args->getListFields());
        // Traitement du résultat
        if (isset($result)) {
          $args->setMappingFields($result);
          $ret = true;
        }
        else {
          $ret = false;
        }
      }
    }
    catch (\MongoCursorException  $mongoCursorEx) {

    }
    catch (\MongoCursorTimeoutException $mongoCursorTimeoutEx) {

    }
    catch (\MongoException $mongoEx) {

    }
    catch (\Exception $ex) {

    }
    return $ret;
  }

  /**
   * Mise à jour d'un objet
   * @param MongoDBMapping $args
   */
  public function update(MongoDBMapping $args) {
    $ret = null;
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      $ret = $collection->update($args->getSearchFields(true), $args->getUpdateFields(), $args->getOptions());
    }
    catch (\MongoCursorException  $mongoCursorEx) {

    }
    catch (\MongoCursorTimeoutException $mongoCursorTimeoutEx) {

    }
    catch (\MongoException $mongoEx) {

    }
    catch (\Exception $ex) {

    }
    return $ret;
  }

  /**
   * Suppression d'un objet
   * @param MongoDBMapping $args
   */
  public function delete(MongoDBMapping $args) {
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      $result = $collection->remove($args->getSearchFields(true), $args->getOptions());
    }
    catch (\MongoCursorException  $mongoCursorEx) {

    }
    catch (\MongoCursorTimeoutException $mongoCursorTimeoutEx) {

    }
    catch (\MongoException $mongoEx) {

    }
    catch (\Exception $ex) {

    }
  }
}