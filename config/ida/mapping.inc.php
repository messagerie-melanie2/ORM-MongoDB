<?php
/**
 * Ce fichier est développé pour la gestion de la librairie Mélanie2
 * Cette Librairie permet d'accèder aux données sans avoir à implémenter de couche SQL
 * Des objets génériques vont permettre d'accèder et de mettre à jour les données
 *
 * ORM M2 Copyright (C) 2015  PNE Annuaire et Messagerie/MEDDE
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

/****** Mapping ********/
$config['mapping'] = array(
     array(
        'ObjectType' => 'Roundcube\\Event',
        'Driver' => 'default',
        'CollectionName' => 'event',
        'innerjoin' => array(),
        'leftjoin' => array(),
        'rightjoin' => array(),
        'fields' => array(
                'uid' => array('name' => 'vcalendar.vevent.uid', 'type' => 'string', 'id' => true),
                'calendar' => array('name' => 'calendar', 'type' => 'string', 'id' => true),
                'title' => array('name' => 'vcalendar.vevent.summary', 'type' => 'string'),
                'description' => array('name' => 'vcalendar.vevent.description', 'type' => 'string'),
                'start' => array('name' => 'vcalendar.vevent.dtstart', 'type' => 'datetime'),
                'end' => array('name' => 'vcalendar.vevent.dtend', 'type' => 'datetime'),
        ),
        'methods' => array(
                'load' => array('name' => 'read', 'return' => 'boolean', 'results' => 'combined', 'operator' => 'and'),
                'save' => array('name' => array('insert', 'update'), 'return' => 'boolean', 'results' => 'combined'),
                'delete' => array('name' => 'delete', 'return' => 'boolean', 'results' => 'combined'),
                'getList' => array('name' => 'read', 'return' => 'list', 'results' => 'combined'),
        ),
    ),
);
