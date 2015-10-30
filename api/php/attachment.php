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
namespace ORM\API\PHP;

/**
 * Objet Attachment pour les API Event
 *
 * @property string $name Nom de la pièce jointe
 * @property Attachment::TYPE_* Type de pièce jointe (url ou binaire)
 * @property string $contentType Contentype de la pièce jointe (binaire)
 * @property string $owner Propriétaire de la pièce jointe (binaire)
 * @property Attachment::ENCODING_* $encoding Type d'encodage de la pièce jointe (binaire)
 * @property int $modified Date de modification de la pièce jointe (binaire)
 * @property string $data Données de pièce jointe
 */
class Attachment extends \ORM\Mapping\ObjectMapping {
  /**
   * CONSTANTES
   */
  // TYPE
  const TYPE_BINARY = 'BINARY';
  const TYPE_URL = 'URL';
  // ENCODING
  const ENCODING_BASE64 = 'BASE64';

  /**
   * Méthode d'initialisation de l'objet
   * Appelé dans le constructeur de l'ObjectMapping
   */
  protected function init() {}
}