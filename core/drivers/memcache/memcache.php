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
namespace ORM\Core\Drivers\Memcache;

/**
 * Driver Memcache
 */
class Memcache extends \ORM\Core\Drivers\Driver {
	/**
	 * Connexion courante vers le/les serveurs Memcached
	 * @var \Memcached
	 */
	private $_memcached;
	
	/**
	 * Connexion aux serveurs Memcached
	 *
	 * @return boolean true si connexion OK
	 */
	public function connect() {
		$this->_memcached = new \Memcached();
		
		$this->_memcached->setOption(\Memcached::OPT_CONNECT_TIMEOUT, 10);
		$this->_memcached->setOption(\Memcached::OPT_DISTRIBUTION, \Memcached::DISTRIBUTION_CONSISTENT);
		$this->_memcached->setOption(\Memcached::OPT_SERVER_FAILURE_LIMIT, 2);
		$this->_memcached->setOption(\Memcached::OPT_REMOVE_FAILED_SERVERS, true);
		$this->_memcached->setOption(\Memcached::OPT_RETRY_TIMEOUT, 1);
		
		return $this->_memcached->addServers($this->config['servers']);
	}
	/**
	 * Déconnexion des serveurs Memcached
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
		
	}	
	/**
	 * Récupération d'un objet
	 * @param \ORM\Core\Drivers\DriverMapping $args
	 * @return mixed array of array dans le cas d'une liste, array dans le cas d'un résultat simple, integer dans le cas d'un count
	 */
	public function read(\ORM\Core\Drivers\DriverMapping $args) {
		
	}
	/**
	 * Mise à jour d'un objet
	 * @param \ORM\Core\Drivers\DriverMapping $args
	 * @return boolean true si ok, false sinon
	 */
	public function update(\ORM\Core\Drivers\DriverMapping $args) {
		
	}
	/**
	 * Suppression d'un objet
	 * @param \ORM\Core\Drivers\DriverMapping $args
	 * @return boolean true si ok, false sinon
	 */
	public function delete(\ORM\Core\Drivers\DriverMapping $args) {
		
	}
	/**
	 * Execution de la requête pré-définie
	 * @see \ORM\Core\Drivers\Driver::query()
	 * @param \ORM\Core\Drivers\DriverMapping $args
	 * @return mixed array of array dans le cas d'une liste, array dans le cas d'un résultat simple, integer dans le cas d'un count, boolean pour un insert/update/delete
	 */
	public function execute(\ORM\Core\Drivers\DriverMapping $args) {
		
	}
}
