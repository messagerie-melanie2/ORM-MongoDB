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
namespace ORM\Core\Drivers\Ldap;

/**
 * Driver LDAP
 */
class Ldap extends \ORM\Core\Drivers\Driver {
  /**
   * Connexion courante vers le driver LDAP
   * @var resource
   */
  private $_ldap;
  /**
   * Permet de savoir si on est en connexion anonyme
   * @var bool
   */
  private $_is_anonymous = false;
  /**
   * Permet de savoir si on est en connexion authentifiée
   * @var bool
   */
  private $_is_authenticate = false;

  /**
   * Connexion au serveur LDAP
   *
   * @return boolean true si connexion OK
   */
  public function connect() {
    // Configuration de la connexion au serveur
    $options = isset($this->config['options']) ? $this->config['options'] : array();
    $hostname = isset($this->config['hostname']) ? $this->config['hostname'] : 'localhost';
    $port = isset($this->config['port']) ? $this->config['port'] : '389';

    try {
      // Initialisation de la connexion vers le serveur
      $this->_ldap = @ldap_connect($hostname, $port);

      // Gestion des options
      foreach ($options as $option_name => $option_value) {
        @ldap_set_option($this->_ldap, $option_name, $option_value);
      }
      // Réinitialise les variables
      $this->_is_anonymous = false;
      $this->_is_authenticate = false;
    }
    catch (Exception $ex) {
      \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_ERROR, "[Driver:Ldap]->connect() Exception : " . $ex);
      return false;
    }
    return !is_null($this->_ldap);
  }

  /**
   * Deconnection du serveur LDAP
   *
   * @return boolean true si déconnexion OK
   */
  public function disconnect() {
    $ret = @ldap_unbind($this->_ldap);

    // Réinitialise les variables
    $this->_is_anonymous = false;
    $this->_is_authenticate = false;
    return $ret;
  }

  /**
   * Se connecte en faisant une authentification sur le serveur LDAP
   *
   * @param string $dn
   * @param string $password
   * @return boolean
   */
  public function authenticate($dn, $password) {
    if (is_null($this->_ldap)) {
      $this->connect();
    }
    // Utilisation d'une connexion TLS ?
    if (isset($this->config['tls']) && $this->config['tls']) {
      @ldap_start_tls($this->_ldap);
    }
    // Authentification sur le seveur LDAP
    $this->_is_authenticate = @ldap_bind($this->_ldap, $dn, $password);
    $this->_is_anonymous = false;
    return $this->_is_authenticate;
  }

  /**
   * Se connecte en faisant un bind anonyme vers le serveur LDAP
   *
   * @param boolean $force Forcer la connexion anonyme, même si une authentification existe
   * @return boolean
   */
  public function anonymous($force = false) {
    if (is_null($this->_ldap)) {
      $this->connect();
    }
    // On ne force pas la connexion anonyme, la connexion est-elle déjà authentifiée ?
    if (!$force && $this->_is_authenticate) {
      return $this->_is_authenticate;
    }
    // Une connexion anonyme est déjà établie ?
    if ($this->_is_anonymous) {
      return $this->_is_anonymous;
    }
    // Utilisation d'une connexion TLS ?
    if (isset($this->config['tls']) && $this->config['tls']) {
      @ldap_start_tls($this->_ldap);
    }
    // Bind anonyme sur le seveur LDAP
    $this->_is_anonymous = @ldap_bind($this->connection);
    $this->_is_authenticate = false;
    return $this->_is_anonymous;
  }

  /**
   * Création d'un objet
   *
   * @param \ORM\Core\Drivers\DriverMapping $args
   * @return boolean true si ok, false sinon
   */
  public function create(\ORM\Core\Drivers\DriverMapping $args) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->create()");
    $ret = false;

    return $ret;
  }

  /**
   * Récupération d'un objet
   *
   * @param \ORM\Core\Drivers\DriverMapping $args
   * @return mixed array of array dans le cas d'une liste, array dans le cas d'un résultat simple, integer dans le cas d'un count
   */
  public function read(\ORM\Core\Drivers\DriverMapping $args) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->read()");
    $ret = null;

    return $ret;
  }

  /**
   * Mise à jour d'un objet
   *
   * @param \ORM\Core\Drivers\DriverMapping $args
   * @return boolean true si ok, false sinon
   */
  public function update(\ORM\Core\Drivers\DriverMapping $args) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->update()");
    $ret = false;

    return $ret;
  }

  /**
   * Suppression d'un objet
   *
   * @param \ORM\Core\Drivers\DriverMapping $args
   * @return boolean true si ok, false sinon
   */
  public function delete(\ORM\Core\Drivers\DriverMapping $args) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:PDO]->delete()");
    $ret = false;

    return $ret;
  }

  /**
   * Recherche dans le LDAP
   * Effectue une recherche avec le filtre filter dans le dossier base_dn avec le paramétrage LDAP_SCOPE_SUBTREE.
   * C'est l'équivalent d'une recherche dans le dossier.
   *
   * @param string $base_dn Base DN de recherche
   * @param string $filter Filtre de recherche
   * @param array $attributes Attributs à rechercher
   * @param int $attrsonly Doit être défini à 1 si seuls les types des attributs sont demandés. S'il est défini à 0, les types et les valeurs des attributs sont récupérés, ce qui correspond au comportement par défaut.
   * @param int $sizelimit Vous permet de limiter le nombre d'entrées à récupérer. Le fait de définir ce paramètre à 0 signifie qu'il n'y aura aucune limite.
   * @return resource a search result identifier or false on error.
   */
  public function search($base_dn, $filter, $attributes = null, $attrsonly = 0, $sizelimit = 0) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->search($base_dn, $filter)");

    return @ldap_search($this->_ldap, $base_dn, $filter, $attributes, $attrsonly, $sizelimit);
  }
  /**
   * Recherche dans le LDAP
   * Effectue une recherche avec le filtre filter dans le dossier base_dn avec la configuration LDAP_SCOPE_BASE.
   * C'est équivalent à lire une entrée dans un dossier.
   *
   * @param string $base_dn Base DN de recherche
   * @param string $filter Filtre de recherche
   * @param array $attributes Attributs à rechercher
   * @param int $attrsonly Doit être défini à 1 si seuls les types des attributs sont demandés. S'il est défini à 0, les types et les valeurs des attributs sont récupérés, ce qui correspond au comportement par défaut.
   * @param int $sizelimit Vous permet de limiter le nombre d'entrées à récupérer. Le fait de définir ce paramètre à 0 signifie qu'il n'y aura aucune limite.
   * @return resource a search result identifier or false on error.
   */
  public function read($base_dn, $filter, $attributes = null, $attrsonly = 0, $sizelimit = 0) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->read($base_dn, $filter)");

    return @ldap_read($this->_ldap, $base_dn, $filter, $attributes, $attrsonly, $sizelimit);
  }
  /**
   * Recherche dans le LDAP
   * Effectue une recherche avec le filtre filter dans le dossier base_dn avec l'option LDAP_SCOPE_ONELEVEL.
   * LDAP_SCOPE_ONELEVEL signifie que la recherche ne peut retourner des entrées que dans le niveau qui est immédiatement sous le niveau base_dn
   * (c'est l'équivalent de la commande ls, pour obtenir la liste des fichiers et dossiers du dossier courant).
   *
   * @param string $base_dn Base DN de recherche
   * @param string $filter Filtre de recherche
   * @param array $attributes Attributs à rechercher
   * @param int $attrsonly Doit être défini à 1 si seuls les types des attributs sont demandés. S'il est défini à 0, les types et les valeurs des attributs sont récupérés, ce qui correspond au comportement par défaut.
   * @param int $sizelimit Vous permet de limiter le nombre d'entrées à récupérer. Le fait de définir ce paramètre à 0 signifie qu'il n'y aura aucune limite.
   * @return resource a search result identifier or false on error.
   */
  public function ldap_list($base_dn, $filter, $attributes = null, $attrsonly = 0, $sizelimit = 0) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->ldap_list($base_dn, $filter)");

    return @ldap_list($this->_ldap, $base_dn, $filter, $attributes, $attrsonly, $sizelimit);
  }
  /**
   * Retourne les entrées trouvées via le Ldap search
   *
   * @param resource $search Resource retournée par le search
   * @return array a complete result information in a multi-dimensional array on success and false on error.
   */
  public function get_entries($search) {
    return @ldap_get_entries($this->_ldap, $search);
  }
  /**
   * Retourne le nombre d'entrées trouvé via le Ldap search
   *
   * @param resource $search Resource retournée par le search
   * @return int number of entries in the result or false on error.
   */
  public function count_entries($search) {
    return @ldap_count_entries($this->_ldap, $search);
  }
  /**
   * Retourne la premiere entrée trouvée
   *
   * @param resource $search Resource retournée par le search
   * @return resource the result entry identifier for the first entry on success and false on error.
   */
  public function first_entry($search) {
    return @ldap_first_entry($this->_ldap, $search);
  }
  /**
   * Retourne les entrées suivantes de la recherche
   *
   * @param resource $search Resource retournée par le search
   * @return resource entry identifier for the next entry in the result whose entries are being read starting with ldap_first_entry. If there are no more entries in the result then it returns false.
   */
  public function next_entry($search) {
    return @ldap_next_entry($this->_ldap, $search);
  }
  /**
   * Retourne le dn associé à une entrée de l'annuaire
   *
   * @param resource $entry l'entrée dans laquelle on récupère les infos
   * @return string the DN of the result entry and false on error.
   */
  public function get_dn($entry) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->get_dn()");

    return @ldap_get_dn($this->_ldap, $entry);
  }
  /**
   * Ajoute l'attribut entry à l'entrée dn.
   * Elle effectue la modification au niveau attribut, par opposition au niveau objet.
   * Les additions au niveau objet sont réalisées par ldap_add().
   *
   * @param string $dn Le nom DN de l'entrée LDAP.
   * @param array $entry Entrée à remplacer dans l'annuaire
   * @return bool Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient.
   */
  public function mod_add($dn , $entry) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->mod_add($dn)");

    return @ldap_mod_add($this->_ldap, $dn, $entry);
  }
  /**
   * Remplace l'attribut entry de l'entrée dn.
   * Elle effectue le remplacement au niveau attribut, par opposition au niveau objet.
   * Les additions au niveau objet sont réalisées par ldap_modify().
   *
   * @param string $dn Le nom DN de l'entrée LDAP.
   * @param array $entry Entrée à remplacer dans l'annuaire
   * @return bool Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient.
   */
  public function mod_replace($dn , $entry) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->mod_replace($dn)");

    return @ldap_mod_replace($this->_ldap, $dn, $entry);
  }
  /**
   * Efface l'attribut entry de l'entrée dn.
   * Elle effectue la modification au niveau attribut, par opposition au niveau objet.
   * Les additions au niveau objet sont réalisées par ldap_delete().
   *
   * @param string $dn Le nom DN de l'entrée LDAP.
   * @param array $entry Entrée à remplacer dans l'annuaire
   * @return bool Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient.
   */
  public function mod_del($dn , $entry) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->mod_del($dn)");

    return @ldap_mod_del($this->_ldap, $dn, $entry);
  }
  /**
   * Ajoute une entrée dans un dossier LDAP.
   *
   * @param string $dn Le nom DN de l'entrée LDAP.
   * @param array $entry Entrée à remplacer dans l'annuaire
   * @return bool Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient.
   */
  public function add($dn, $entry) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->add($dn)");

    return @ldap_add($this->_ldap, $dn, $entry);
  }
  /**
   * Modifie l'entrée identifiée par dn, avec les valeurs fournies dans entry.
   * La structure de entry est la même que détaillée dans ldap_add().
   *
   * @param string $dn Le nom DN de l'entrée LDAP.
   * @param array $entry Entrée à remplacer dans l'annuaire
   * @return bool Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient.
   */
  public function modify($dn, $entry) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->modify($dn)");

    return @ldap_modify($this->_ldap, $dn, $entry);
  }
  /**
   * Efface une entrée spécifique d'un dossier LDAP.
   *
   * @param string $dn Le nom DN de l'entrée LDAP.
   * @return bool Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient.
   */
  public function delete($dn) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->delete($dn)");

    return @ldap_delete($this->_ldap, $dn);
  }
  /**
   * Renomme une entrée pour déplacer l'objet dans l'annuaire
   *
   * @param string $dn Le nom DN de l'entrée LDAP.
   * @param string $newrdn The new RDN.
   * @param string $newparent The new parent/superior entry.
   * @param bool $deleteoldrdn If TRUE the old RDN value(s) is removed, else the old RDN value(s) is retained as non-distinguished values of the entry.
   * @return bool Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient.
   */
  public function rename($dn , $newrdn , $newparent , $deleteoldrdn) {
    \ORM\Core\Log\ORMLog::Log(\ORM\Core\Log\ORMLog::LEVEL_DEBUG, "[Driver:Ldap]->rename($dn)");

    return @ldap_rename($this->_ldap, $dn, $newrdn, $newparent, $deleteoldrdn);
  }
  /**
   * Retourne la précédente erreur pour la commande LDAP
   *
   * @return array('Errno' => int, 'Errmsg' => string)
   */
  public function getError() {
    $errno = ldap_errno($this->_ldap);
    return array('Errno' => $errno, 'Errmsg' => ldap_err2str($errno));
  }
}