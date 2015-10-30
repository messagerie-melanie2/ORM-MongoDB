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
    $options = isset($this->config['options']) ? $this->config['options'] : array();
    $driver_options = isset($this->config['driver_options']) ? $this->config['driver_options'] : null;
    // Initialisation de la liste des collections
    $this->_collections = array();
    // Connexion au serveur MongoDB
    $this->_connexion = new \MongoClient($this->config['dsn'], $options, $driver_options);
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
   * @return boolean True si ok, false sinon
   */
  public function disconnect() {
    // Pas de déconnexion en MongoDB
    $this->_connexion->close();
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
   * @param \ORM\DB\DriverMapping $args
   * @return boolean True si ok, false sinon
   */
  public function create(\ORM\DB\DriverMapping $args) {
    \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_DEBUG, "[Driver:MongoDB]->create()");
    $ret = null;
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      $ret = $collection->insert($args->getMappingFields(), $args->getOptions());
    }
    catch (\MongoCursorException  $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->create() MongoCursorException : " . $ex->getTraceAsString());
    }
    catch (\MongoCursorTimeoutException $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->create() MongoCursorTimeoutException : " . $ex->getTraceAsString());
    }
    catch (\MongoException $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->create() MongoException : " . $ex->getTraceAsString());
    }
    catch (\Exception $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->create() Exception : " . $ex->getTraceAsString());
    }
    return $ret;
  }

  /**
   * Récupération d'un objet
   * @param \ORM\DB\DriverMapping $args
   * @return mixed array of array dans le cas d'une liste, array dans le cas d'un résultat simple, integer dans le cas d'un count
   */
  public function read(\ORM\DB\DriverMapping $args) {
    \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_DEBUG, "[Driver:MongoDB]->read()");
    $ret = null;
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      if ($args->isList()) {
        // Est-ce qu'on souhaite faire un count
        if ($args->isCount()) {
          $ret = $cursor->count($args->getSearchFields(), $args->getOptions());
        }
        else {
          $cursor = $collection->find($args->getSearchFields(), $args->listFields());
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
          $data = array();
          // Traitement des résultats
          while ($cursor->hasNext()) {
            $data[] = $cursor->next();
          }
          $ret = $data;
        }
      }
      else {
        $ret = $collection->findOne($args->getSearchFields(), $args->listFields());
      }
    }
    catch (\MongoCursorException  $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->read() MongoCursorException : " . $ex->getTraceAsString());
    }
    catch (\MongoCursorTimeoutException $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->read() MongoCursorTimeoutException : " . $ex->getTraceAsString());
    }
    catch (\MongoException $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->read() MongoException : " . $ex->getTraceAsString());
    }
    catch (\Exception $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->read() Exception : " . $ex->getTraceAsString());
    }
    return $ret;
  }

  /**
   * Mise à jour d'un objet
   * @param \ORM\DB\DriverMapping $args
   * @return boolean True si ok, false sinon
   */
  public function update(\ORM\DB\DriverMapping $args) {
    \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_DEBUG, "[Driver:MongoDB]->update()");
    $ret = null;
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      $ret = $collection->update($args->getSearchFields(true), $args->getUpdateFields(), $args->getOptions());
    }
    catch (\MongoCursorException  $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->update() Exception : " . $ex->getTraceAsString());
    }
    catch (\MongoCursorTimeoutException $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->update() Exception : " . $ex->getTraceAsString());
    }
    catch (\MongoException $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->update() Exception : " . $ex->getTraceAsString());
    }
    catch (\Exception $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->update() Exception : " . $ex->getTraceAsString());
    }
    return $ret;
  }

  /**
   * Suppression d'un objet
   * @param \ORM\DB\DriverMapping $args
   * @return boolean True si ok, false sinon
   */
  public function delete(\ORM\DB\DriverMapping $args) {
    \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_DEBUG, "[Driver:MongoDB]->delete()");
    $ret = null;
    try {
      $collection = $this->_getCollection($args->getCollectionName());
      $ret = $collection->remove($args->getSearchFields(true), $args->getOptions());
    }
    catch (\MongoCursorException  $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->delete() Exception : " . $ex->getTraceAsString());
    }
    catch (\MongoCursorTimeoutException $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->delete() Exception : " . $ex->getTraceAsString());
    }
    catch (\MongoException $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->delete() Exception : " . $ex->getTraceAsString());
    }
    catch (\Exception $ex) {
      \ORM\Log\ORMLog::Log(\ORM\Log\ORMLog::LEVEL_ERROR, "[Driver:MongoDB]->delete() Exception : " . $ex->getTraceAsString());
    }
    return $ret;
  }
}