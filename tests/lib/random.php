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
namespace ORM\Tests\Lib;

use Faker;
// Inclusions
set_include_path(__DIR__.'/../..');
include_once 'tests/Faker-1.5.0/src/autoload.php';

/**
 * Génération de données aléatoire
 *
 * @author thomas
 *
 */
class Random {
  /**
   *
   * @var Faker\Generator
   */
  public static $faker;

  const NB_CALENDARS = 100;
  const CALENDAR_NAME = "thomas.test";

  /**
   * Génération aléatoire d'un identifiant de calendrier
   * @return string
   */
  public static function Calendar() {
    return self::CALENDAR_NAME . rand(1, self::NB_CALENDARS);
  }

  /**
   * Génération aléatoire d'un propriétaire de calendrier
   * @return string
   */
  public static function Owner() {
    return self::CALENDAR_NAME . rand(1, self::NB_CALENDARS);
  }

  /**
   * Génération aléatoire d'un titre d'événement
   * @return string
   */
  public static function Summary() {
    return self::$faker->realText(rand(10, 200));
  }

  /**
   * Génération aléatoire d'une description d'événement
   * @return string
   */
  public static function Description() {
    return self::$faker->realText(rand(50, 2000));
  }

  public static function Start() {
    return self::$faker->dateTimeThisDecade;
  }

  public static function bool() {
    return self::$faker->randomElement(array(true, false));
  }
}

Random::$faker = Faker\Factory::create('fr_FR');