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

/*
 * Définition d'un objet
 *
 * array(
 *  'ObjectType' => '<Class PHP de l'objet>',
 *  'Driver' => '<Nom du driver configuré>',
 *  'CollectionName' => '<Collection ou table SQL>',
 *  'primaryKeys' => array(<Tableau des clés primaires de l'objet>),
 *  'fields' => array( // Liste des champs définis dans l'objet et mappés vers la base de données
 *    <Nom du champ> => array(
 *      'name' => <Nom du champ dans la base de données>,
 *      'type' => <Type de données dans la base de données>,
 *      'size' => <[Optionnel] Longueur maximum du champ dans la bdd>,
 *      'default' => <[Optionnel] Valeur par défaut>,
 *      'ObjectType' => <[Optionnel] Se réfaire un autre ObjectType défini dans le fichier>,
 *    ),
 *  ),
 *  'methods' => array( // Liste des méthodes accessibles depuis l'objet, les méthodes sont mappées vers les méthodes du driver (created, read, update, delete)
 *    <Nom de la méthode> => array(
 *      'name' => <Nom de la méthode à appeler dans le driver>,
 *      'return' => <Type de donnée à retourner par la méthode>,
 *      'results' => <[Optionnel] "combined" si les résultats doivent être combinés entre tous les drivers (cas d'un objet sur plusieurs drivers)>,
 *      'operator' => <[Optionnel] operateur de concaténation dans le cas de résultats combinés>,
 *      'mapData' => <[Optionnel] Les données doivent directement être mappées sur l'objet>,
 *      'var' => <[Optionnel] Le résultat de la méthode doivent être conservé en variable dans l'objet>,
 *      'arguments' => array( // [Optionnel] Liste des arguments de la méthodes, l'ordre est important !
 *        <Nom du champ utilisés, actuellement : 'listFields', 'filter', 'operators', 'orderBy', 'asc', 'limit', 'offset', 'unsensitiveFields',>
 *      ),
 *      'data' => array( // [Optionnel] Force une valeur par défaut, utilise les mêmes champs que pour 'arguments'
 *        <Nom du champ utilisés> => <valeur par défaut>,
 *      ),
 *    ),
 *  ),
 * )
 */

/****** Mapping ********/
$config['mapping'] = array(
     // Objet event
     array(
        'ObjectType' => 'PHP\\Event',
        'Driver' => 'default',
        'CollectionName' => 'events',
        'primaryKeys' => array('uid' => true, 'calendar' => true),
        'fields' => array(
                'uid' => array('name' => 'uid', 'type' => 'string', 'size' => 255),
                'calendar' => array('name' => 'calendar', 'type' => 'string', 'size' => 255),
                'owner' => array('name' => 'owner', 'type' => 'string', 'size' => 255),
                'etag' => array('name' => 'etag', 'type' => 'string', 'size' => 255),
                'deleted' => array('name' => 'deleted', 'type' => 'boolean', 'defaut' => false),
                'title' => array('name' => 'summary', 'type' => 'string'),
                'description' => array('name' => 'description', 'type' => 'string'),
                'start' => array('name' => 'dtstart', 'type' => 'datetime'),
                'end' => array('name' => 'dtend', 'type' => 'datetime'),
                'created' => array('name' => 'created', 'type' => 'timestamp'),
                'modified' => array('name' => 'modified', 'type' => 'timestamp'),
                'class' => array('name' => 'class', 'type' => 'string', 'size' => 120),
                'status' => array('name' => 'status', 'type' => 'string', 'size' => 120),
                'categories' => array('name' => 'categories', 'type' => 'array'),
                'organizer' => array('ObjectType' => 'PHP\\Organizer', 'name' => 'organizer', 'elements' => array('organizer_name', 'organizer_email')),
                'attendees' => array('ObjectType' => 'PHP\\Attendee', 'type' => 'list', 'name' => 'attendees'),
                'alarm' => array('ObjectType' => 'PHP\\Alarm', 'name' => 'valarm', 'elements' => array('valarm_trigger', 'valarm_action')),
                'recurrence' => array('ObjectType' => 'PHP\\Recurrence', 'name' => 'recurrence', 'elements' => array('recurrence_freq', 'recurrence_count', 'recurrence_interval', 'recurrence_byday', 'recurrence_bymonth', 'recurrence_bymonthday', 'recurrence_byyearday', 'recurrence_until', 'recurrence_wkst')),
                'attachments' => array('ObjectType' => 'PHP\\Attachment', 'type' => 'list', 'name' => 'attachments'),
                'exceptions' => array('ObjectType' => 'PHP\\Exception', 'type' => 'list', 'name' => 'exceptions'),
        ),
        'methods' => array(
                'load' => array(
                        'name' => 'read',
                        'return' => 'boolean',
                        'results' => 'combined',
                        'operator' => 'and',
                        'var' => true,
                        'mapData' => true,
                        'data' => array(
                                'fieldsForSearch' => array('uid' => true, 'calendar' => true),
                                'usePrimaryKeys' => false,
                        ),
                ),
                'exists' => array(
                        'name' => 'read',
                        'return' => 'boolean',
                        'results' => 'combined',
                        'operator' => 'and',
                        'var' => true,
                        'data' => array(
                                'fieldsForSearch' => array('uid' => true, 'calendar' => true),
                                'usePrimaryKeys' => false,
                        ),
                ),
                'insert' => array(
                        'name' => 'create',
                        'return' => 'boolean',
                        'results' => 'combined',
                        'operator' => 'and',
                ),
                'update' => array(
                        'name' => 'update',
                        'return' => 'boolean',
                        'results' => 'combined',
                        'operator' => 'and',
                        'data' => array(
                                'fieldsForSearch' => array('uid' => true, 'calendar' => true),
                                'usePrimaryKeys' => false,
                        ),
                ),
                'save' => array(
                        'method' => array('exists' => array(false => 'insert', true => 'update')),
                        'return' => 'boolean',
                        'results' => 'combined',
                        'data' => array(
                                'fieldsForSearch' => array('uid' => true, 'calendar' => true),
                                'usePrimaryKeys' => false,
                        ),
                ),
                'delete' => array(
                        'name' => 'delete',
                        'return' => 'boolean',
                        'results' => 'combined',
                        'data' => array(
                                'fieldsForSearch' => array('uid' => true, 'calendar' => true),
                                'usePrimaryKeys' => false,
                        ),
                ),
                'list' => array(
                        'name' => 'read',
                        'return' => 'list',
                        'results' => 'combined',
                        'mapData' => true,
                        'arguments' => array(
                                'listFields',
                                'filter',
                                'operators',
                                'orderBy',
                                'asc',
                                'limit',
                                'offset',
                                'unsensitiveFields',
                        ),
                ),
        ),
    ),
    // Objet organizer
    array(
          'ObjectType' => 'PHP\\Organizer',
          'Driver' => 'default',
          'CollectionName' => 'event',
          'fields' => array(
                  'name' => array('name' => 'organizer_name', 'type' => 'string', 'size' => 255),
                  'email' => array('name' => 'organizer_email', 'type' => 'string', 'size' => 255),
          ),
    ),
    // Objet attendee
    array(
          'ObjectType' => 'PHP\\Attendee',
          'Driver' => 'default',
          'CollectionName' => 'event',
          'fields' => array(
                  'name' => array('name' => 'name', 'type' => 'string', 'size' => 255),
                  'email' => array('name' => 'email', 'type' => 'string', 'size' => 255),
                  'role' => array('name' => 'role', 'type' => 'string', 'size' => 120),
                  'response' => array('name' => 'partstat', 'type' => 'string', 'size' => 120),
          ),
    ),
    // Objet alarm
    array(
            'ObjectType' => 'PHP\\Alarm',
            'Driver' => 'default',
            'CollectionName' => 'event',
            'fields' => array(
                    'trigger' => array('name' => 'valarm_trigger', 'type' => 'string', 'size' => 120),
                    'action' => array('name' => 'valarm_action', 'type' => 'string', 'size' => 120),
            ),
    ),
    // Objet attachment
    array(
            'ObjectType' => 'PHP\\Attachment',
            'Driver' => 'default',
            'CollectionName' => 'event',
            'fields' => array(
                    'name' => array('name' => 'name', 'type' => 'string', 'size' => 255),
                    'type' => array('name' => 'type', 'type' => 'string', 'size' => 120),
                    'contentType' => array('name' => 'contentType', 'type' => 'string', 'size' => 255),
                    'owner' => array('name' => 'owner', 'type' => 'string', 'size' => 255),
                    'encoding' => array('name' => 'encoding', 'type' => 'string', 'size' => 120),
                    'modified' => array('name' => 'modified', 'type' => 'timestamp'),
                    'data' => array('name' => 'data', 'type' => 'string'),
            ),
    ),
    // Objet recurrence
    array(
            'ObjectType' => 'PHP\\Recurrence',
            'Driver' => 'default',
            'CollectionName' => 'event',
            'fields' => array(
                    'freq' => array('name' => 'recurrence_freq', 'type' => 'string', 'size' => 120),
                    'count' => array('name' => 'recurrence_count', 'type' => 'integer'),
                    'interval' => array('name' => 'recurrence_interval', 'type' => 'integer'),
                    'byday' => array('name' => 'recurrence_byday', 'type' => 'string', 'size' => 120),
                    'bymonth' => array('name' => 'recurrence_bymonth', 'type' => 'string', 'size' => 120),
                    'bymonthday' => array('name' => 'recurrence_bymonthday', 'type' => 'string', 'size' => 120),
                    'byyearday' => array('name' => 'recurrence_byyearday', 'type' => 'string', 'size' => 120),
                    'until' => array('name' => 'recurrence_until', 'type' => 'datetime'),
                    'wkst' => array('name' => 'recurrence_wkst', 'type' => 'string', 'size' => 120),
            ),
    ),
    // Objet exception
    array(
            'ObjectType' => 'PHP\\Exception',
            'Driver' => 'default',
            'CollectionName' => 'event',
            'fields' => array(
                    'uid' => array('name' => 'uid', 'type' => 'string'),
                    'deleted' => array('name' => 'owner', 'type' => 'boolean', 'defaut' => false),
                    'title' => array('name' => 'summary', 'type' => 'string'),
                    'description' => array('name' => 'description', 'type' => 'string'),
                    'start' => array('name' => 'dtstart', 'type' => 'datetime'),
                    'end' => array('name' => 'dtend', 'type' => 'datetime'),
                    'created' => array('name' => 'created', 'type' => 'timestamp'),
                    'modified' => array('name' => 'modified', 'type' => 'timestamp'),
                    'class' => array('name' => 'class', 'type' => 'string'),
                    'status' => array('name' => 'status', 'type' => 'string'),
                    'categories' => array('name' => 'categories', 'type' => 'array'),
                    'organizer' => array('ObjectType' => 'PHP\\Organizer', 'name' => 'organizer'),
                    'attendees' => array('ObjectType' => 'PHP\\Attendee', 'type' => 'list', 'name' => 'attendees'),
                    'alarm' => array('ObjectType' => 'PHP\\Alarm', 'name' => 'valarm'),
                    'attachments' => array('ObjectType' => 'PHP\\Attachment', 'type' => 'list', 'name' => 'attachments'),
            ),
    ),
);
