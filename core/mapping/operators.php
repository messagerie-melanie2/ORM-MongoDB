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
 * Liste des opérateurs utilisables pour une requête de recherche
 */
class Operators {
  /**
   * Greater than
   */
  const gt = "gt";
  /**
   * Greater than and equal
   */
  const gte = "gte";
  /**
   * Lower than
   */
  const lt = "lt";
  /**
   * Lower than and equal
   */
  const lte = "lte";
  /**
   * Not equal
   */
  const neq = "neq";
  /**
   * Like
   */
  const like = "like";
  /**
   * Equal
   */
  const eq = "eq";
  /**
   * In
   */
  const in = "in";
  /**
   * Not In
   */
  const not_in = "nin";
  /**
   * Or
   */
  const or_0 = "or0";
  const or_1 = "or1";
  const or_2 = "or2";
  const or_3 = "or3";
  const or_4 = "or4";
  const or_5 = "or5";
  const or_6 = "or6";
  const or_7 = "or7";
  const or_8 = "or8";
  const or_9 = "or9";
  /**
   * And
   */
  const and_0 = "and0";
  const and_1 = "and1";
  const and_2 = "and2";
  const and_3 = "and3";
  const and_4 = "and4";
  const and_5 = "and5";
  const and_6 = "and6";
  const and_7 = "and7";
  const and_8 = "and8";
  const and_9 = "and9";
  /**
   * Not
   */
  const not = "not";
  /**
   * Nor
   */
  const nor = "nor";
}