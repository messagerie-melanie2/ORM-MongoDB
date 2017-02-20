<?php
/**
 * Ce fichier est développé pour la gestion de la librairie ORM
 * Cette Librairie permet d'accèder aux données sans avoir à implémenter de couche SQL
 * Des objets génériques vont permettre d'accèder et de mettre à jour les données
 *
 * ORM Copyright © 2017  PNE Annuaire et Messagerie/MEDDE
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
 * Driver PDO
 */
class PDO extends \ORM\Core\Drivers\Driver {
  /**
   * Requête SQL INSERT
   * @var string
   */
  const INSERT = "INSERT INTO {table_name}{insert_columns} VALUES ({insert_values});";
  /**
   * Requête SQL UPDATE
   * @var string
   */
  const UPDATE = "UPDATE {table_name} SET {update_fields}{where_clause};";
  /**
   * Requête SQL DELETE
   * @var string
   */
  const DELETE = "DELETE FROM {table_name} {where_clause};";
  /**
   * Requête SQL SELECT
   * @var string
   */
  const SELECT = "SELECT {select_fields} FROM {table_name}{where_clause}{order_by}{limit}{offset};";

  /**
   * Connexion courante vers le driver
   * @var \PDO
   */
  private $_pdo;

  /**
   * Connexion au serveur de base de données
   *
   * @return boolean true si connexion OK
   */
  public function connect() {
    // Configuration de la connexion au serveur
    $options = isset($this->config['options']) ? $this->config['options'] : array();
    $username = isset($this->config['username']) ? $this->config['username'] : null;
    $password = isset($this->config['password']) ? $this->config['password'] : null;

    try {
      // Connexion au serveur de base de données
      $this->_pdo = new \PDO($this->config['dsn'], $username, $password, $options);
    }
    catch (\PDOException $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->connect() PDOException : " . $ex);
      return false;
    }
    catch (\Exception $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->connect() Exception : " . $ex);
      return false;
    }
    return true;
  }
  /**
   * Déconnexion de la base de données
   * @return boolean true si ok, false sinon
   */
  public function disconnect() {
    return true;
  }

  /**
   * Création d'un objet
   * @param \ORM\Core\Drivers\DriverMapping $args
   * @return boolean true si ok, false sinon
   */
  public function create(\ORM\Core\Drivers\DriverMapping $args) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->create()");
    $ret = false;
    // Génération de la requête
    $query = self::INSERT;
    $insert = $args->getCreateFields();
    // Remplacement des champs
    $query = str_replace("{table_name}", $args->getTableName(), $query);
    $query = str_replace("{insert_columns}", " (".$insert["insertFields"].")", $query);
    $query = str_replace("{insert_values}", $insert["insertRequest"], $query);

    try {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->create() query: " . $query);
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->create() params: " . var_export($insert["insertValues"], true));
      // Excution de la requête avec les paramètres
      $stmt = $this->_pdo->prepare($query);
      $ret = $stmt->execute($insert["insertValues"]);
    }
    catch (\PDOException $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->create() PDOException : " . $ex);
    }
    catch (\Exception $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->create() Exception : " . $ex);
    }

    return $ret;
  }

  /**
   * Récupération d'un objet
   * @param \ORM\Core\Drivers\DriverMapping $args
   * @return mixed array of array dans le cas d'une liste, array dans le cas d'un résultat simple, integer dans le cas d'un count
   */
  public function read(\ORM\Core\Drivers\DriverMapping $args) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->read()");
    $ret = null;

    // Génération de la requête
    $query = self::SELECT;
    $search = $args->getSearchFields();
    $where_clause = isset($search['searchFields']) ? (" WHERE ". $search['searchFields']) : "";
    // Remplacement des champs
    $query = str_replace("{table_name}", $args->getTableName(), $query);
    $query = str_replace("{select_fields}", $args->getSelectFields(), $query);
    $query = str_replace("{where_clause}", $where_clause, $query);
    $query = str_replace("{order_by}", $args->getOrderBy(), $query);
    $query = str_replace("{limit}", $args->getLimit(), $query);
    $query = str_replace("{offset}", $args->getOffset(), $query);

    try {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->read() query: " . $query);
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->read() params: " . var_export($search['searchValues'], true));
      // Excution de la requête avec les paramètres
      $stmt = $this->_pdo->prepare($query);
      if ($stmt->execute($search['searchValues'])) {
        if ($args->isList()) {
          // Si c'est une liste on fetch tous les résultats
          $ret = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        else {
          // Sinon on ne fetch que le premier resultat
          $ret = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
      }
    }
    catch (\PDOException $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->read() PDOException : " . $ex);
    }
    catch (\Exception $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->read() Exception : " . $ex);
    }

    return $ret;
  }

  /**
   * Mise à jour d'un objet
   * @param \ORM\Core\Drivers\DriverMapping $args
   * @return boolean true si ok, false sinon
   */
  public function update(\ORM\Core\Drivers\DriverMapping $args) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->update()");
    $ret = false;

    // Génération de la requête
    $query = self::UPDATE;
    $update = $args->getUpdateFields();
    if (empty($update['updateFields'])) {
      return false;
    }
    $search = $args->getSearchFields();
    $where_clause = isset($search['searchFields']) ? (" WHERE ". $search['searchFields']) : "";
    $params = isset($search['searchValues']) ? array_merge($update['updateValues'], $search['searchValues']) : $update['updateValues'];
    // Remplacement des champs
    $query = str_replace("{table_name}", $args->getTableName(), $query);
    $query = str_replace("{update_fields}", $update['updateFields'], $query);
    $query = str_replace("{where_clause}", $where_clause, $query);

    try {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->update() query: " . $query);
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->update() params: " . var_export($params, true));
      // Excution de la requête avec les paramètres
      $pdoStatment = $this->_pdo->prepare($query);
      $ret = $pdoStatment->execute($params);
    }
    catch (\PDOException $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->update() PDOException : " . $ex);
    }
    catch (\Exception $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->update() Exception : " . $ex);
    }

    return $ret;
  }

  /**
   * Suppression d'un objet
   * @param \ORM\Core\Drivers\DriverMapping $args
   * @return boolean true si ok, false sinon
   */
  public function delete(\ORM\Core\Drivers\DriverMapping $args) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->delete()");
    $ret = false;

    // Génération de la requête
    $query = self::DELETE;
    $search = $args->getSearchFields();
    $where_clause = isset($search['searchFields']) ? ("WHERE ". $search['searchFields']) : "";
    $params = isset($search['searchValues']) ? $search['searchValues'] : null;
    // Remplacement des champs
    $query = str_replace("{table_name}", $args->getTableName(), $query);
    $query = str_replace("{where_clause}", $where_clause, $query);

    try {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->delete() query: " . $query);
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->delete() params: " . var_export($params, true));
      // Excution de la requête avec les paramètres
      $pdoStatment = $this->_pdo->prepare($query);
      $ret = $pdoStatment->execute($params);
    }
    catch (\PDOException $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->delete() PDOException : " . $ex);
    }
    catch (\Exception $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->delete() Exception : " . $ex);
    }

    return $ret;
  }
  /**
   * Execution de la requête pré-définie
   * @see \ORM\Core\Drivers\Driver::query()
   * @param \ORM\Core\Drivers\DriverMapping $args
   * @return mixed array of array dans le cas d'une liste, array dans le cas d'un résultat simple, integer dans le cas d'un count, boolean pour un insert/update/delete
   */
  public function execute(\ORM\Core\Drivers\DriverMapping $args) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->execute()");
    $ret = false;

    // Génération de la requête
    $query = $args->query();
    $params = $args->getQueryFields();

    try {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->execute() query: " . $query);
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->execute() params: " . var_export($params, true));

      if ($args->isBoolean()) {
        // Excution de la requête avec les paramètres
        $pdoStatment = $this->_pdo->prepare($query);
        $ret = $pdoStatment->execute($params);
      }
      else {
        // Excution de la requête avec les paramètres
        $stmt = $this->_pdo->prepare($query);
        if ($stmt->execute($params)) {
          if ($args->isList()) {
            // Si c'est une liste on fetch tous les résultats
            $ret = $stmt->fetchAll(\PDO::FETCH_ASSOC);
          }
          else {
            // Sinon on ne fetch que le premier resultat
            $ret = $stmt->fetch(\PDO::FETCH_ASSOC);
          }
          $stmt->closeCursor();
        }
      }

    }
    catch (\PDOException $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->execute() PDOException : " . $ex);
    }
    catch (\Exception $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:PDO]->execute() Exception : " . $ex);
    }

    return $ret;
  }
}