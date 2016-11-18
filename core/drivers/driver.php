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
namespace ORM\Core\Drivers;

/**
 * Driver générique
 * Tous les drivers de l'ORM doivent l'implémenter
 * Gestion du CRUD
 */
abstract class Driver {
  /**
   * Instances
   * @var Driver[]
   */
  private static $instances = array();

  /**
   * Connexion courante vers le driver
   * @var resource
   */
  protected $connexion;
  /**
   * Configuration global de la connexion
   * @var array
   */
  protected $config;

  /**
   * Récupèration de l'instance lié au serveur
   * @param string $server Nom du serveur, l'instance sera liée à ce nom qui correspond à la configuration du serveur
   * @return Driver
   */
  public static function get_instance($server = 'default') {
    if (!isset(self::$instances[$server])) {
      // Récupération de la configuration
      $config = \ORM\Core\Config\Config::get("db.$server");
      // Si la configuration n'existe pas
      if (!isset($config)) {
        return false;
      }
      // Instancie le driver
      $driver = "\\ORM\\Core\\Drivers\\".$config['driver']."\\".$config['driver'];
      self::$instances[$server] = new $driver($config);
    }
    // Return l'instance du driver
    return self::$instances[$server];
  }

  /**
   * Constucteur par défaut du driver
   * Instancie la configuration
   * @param array $config
   */
  public function __construct(&$config) {
    $this->config = $config;
    $this->connect();
  }

  /**
   * Destructeur par défaut : appel à disconnect
   */
  public function __destruct() {
    $this->disconnect();
  }

  /**
   * Connexion aux données
   *
   * @return bool
   */
  abstract public function connect();
  /**
   * Déconnexion
   *
   * @return bool
   */
  abstract public function disconnect();

  /**
   * Création d'un objet
   * @param DriverMapping $args
   */
  abstract public function create(DriverMapping $args);
  /**
   * Récupération d'un objet
   * @param DriverMapping $args
   */
  abstract public function read(DriverMapping $args);
  /**
   * Mise à jour d'un objet
   * @param DriverMapping $args
   */
  abstract public function update(DriverMapping $args);
  /**
   * Suppression d'un objet
   * @param DriverMapping $args
   */
  abstract public function delete(DriverMapping $args);
}