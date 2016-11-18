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
 * 'ObjectType' => '<Class PHP de l'objet>',
 * 'Driver' => '<Nom du driver configuré>',
 * 'CollectionName' => '<Collection ou table SQL>',
 * 'primaryKeys' => array(<Tableau des clés primaires de l'objet>),
 * 'fields' => array( // Liste des champs définis dans l'objet et mappés vers la base de données
 * <Nom du champ> => array(
 * 'name' => <Nom du champ dans la base de données>,
 * 'type' => <Type de données dans la base de données>,
 * 'size' => <[Optionnel] Longueur maximum du champ dans la bdd>,
 * 'default' => <[Optionnel] Valeur par défaut>,
 * 'ObjectType' => <[Optionnel] Se réfaire un autre ObjectType défini dans le fichier>,
 * ),
 * ),
 * 'methods' => array( // Liste des méthodes accessibles depuis l'objet, les méthodes sont mappées vers les méthodes du driver (created, read, update, delete)
 * <Nom de la méthode> => array(
 * 'name' => <Nom de la méthode à appeler dans le driver>,
 * 'return' => <Type de donnée à retourner par la méthode>,
 * 'results' => <[Optionnel] "combined" si les résultats doivent être combinés entre tous les drivers (cas d'un objet sur plusieurs drivers)>,
 * 'operator' => <[Optionnel] operateur de concaténation dans le cas de résultats combinés>,
 * 'mapData' => <[Optionnel] Les données doivent directement être mappées sur l'objet>,
 * 'var' => <[Optionnel] Le résultat de la méthode doivent être conservé en variable dans l'objet>,
 * 'arguments' => array( // [Optionnel] Liste des arguments de la méthodes, l'ordre est important !
 * <Nom du champ utilisés, actuellement : 'listFields', 'filter', 'operators', 'orderBy', 'asc', 'limit', 'offset', 'unsensitiveFields',>
 * ),
 * 'data' => array( // [Optionnel] Force une valeur par défaut, utilise les mêmes champs que pour 'arguments'
 * <Nom du champ utilisés> => <valeur par défaut>,
 * ),
 * ),
 * ),
 * )
 */

/**
 * **** Mapping *******
 */
$config['mapping'] = array(
        // Object user
        array(
                'ObjectType' => 'PHP\\User',
                'Driver' => 'default',
                'CollectionName' => 'users',
                'primaryKeys' => array(
                        'uid' => true
                ),
                'fields' => array(
                        'uid' => array(
                                'name' => 'uid',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'created' => array(
                                'name' => 'created',
                                'type' => 'datetime'
                        ),
                        'preferences' => array(
                                'name' => 'preferences',
                                'ObjectType' => 'PHP\\Preferences',
                                'json' => true
                        )
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
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'exists' => array(
                                'name' => 'read',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'operator' => 'and',
                                'var' => true,
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'insert' => array(
                                'name' => 'create',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'operator' => 'and'
                        ),
                        'update' => array(
                                'name' => 'update',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'operator' => 'and',
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'save' => array(
                                'method' => array(
                                        'exists' => array(
                                                false => 'insert',
                                                true => 'update'
                                        )
                                ),
                                'return' => 'boolean',
                                'results' => 'combined',
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'delete' => array(
                                'name' => 'delete',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        )
                )
        ),
        // Object property
        array(
                'ObjectType' => 'PHP\\Preferences',
                'Driver' => 'default',
                'fields' => array(
                        'default_calendar' => array(
                                'name' => 'default_calendar',
                                'type' => 'string'
                        ),
                        'default_addressbook' => array(
                                'name' => 'default_addressbook',
                                'type' => 'string'
                        ),
                        'default_tasklist' => array(
                                'name' => 'default_tasklist',
                                'type' => 'string'
                        )
                )
        ),
        // Object calendar
        array(
                'ObjectType' => 'PHP\\Calendar',
                'Driver' => 'default',
                'CollectionName' => 'calendars',
                'primaryKeys' => array(
                        'uid' => true
                ),
                'fields' => array(
                        'uid' => array(
                                'name' => 'uid',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'name' => array(
                                'name' => 'name',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'owner' => array(
                                'name' => 'owner',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'created' => array(
                                'name' => 'created',
                                'type' => 'datetime'
                        ),
                        'ctag' => array(
                                'name' => 'ctag',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'properties' => array(
                                'name' => 'properties',
                                'ObjectType' => 'PHP\\Property',
                                'json' => true,
                                'list' => true
                        )
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
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'exists' => array(
                                'name' => 'read',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'operator' => 'and',
                                'var' => true,
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'insert' => array(
                                'name' => 'create',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'operator' => 'and'
                        ),
                        'update' => array(
                                'name' => 'update',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'operator' => 'and',
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'save' => array(
                                'method' => array(
                                        'exists' => array(
                                                false => 'insert',
                                                true => 'update'
                                        )
                                ),
                                'return' => 'boolean',
                                'results' => 'combined',
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'delete' => array(
                                'name' => 'delete',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        )
                )
        ),
        // Object event
        array(
                'ObjectType' => 'PHP\\Event',
                'Driver' => 'default',
                'CollectionName' => 'events',
                'primaryKeys' => array(
                        'uid' => true,
                        'calendar' => true
                ),
                'fields' => array(
                        'uid' => array(
                                'name' => 'uid',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'calendar' => array(
                                'name' => 'calendar_id',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'creator' => array(
                                'name' => 'creator_id',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'last_modifier' => array(
                                'name' => 'last_modifier_id',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'created' => array(
                                'name' => 'created',
                                'type' => 'timestamp'
                        ),
                        'modified' => array(
                                'name' => 'modified',
                                'type' => 'timestamp'
                        ),
                        'history' => array(
                                'name' => 'history',
                                'ObjectType' => 'PHP\\History',
                                'list' => true,
                                'json' => true
                        ),
                        'summary' => array(
                                'name' => 'summary',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'description' => array(
                                'name' => 'description',
                                'type' => 'string'
                        ),
                        'location' => array(
                                'name' => 'location',
                                'type' => 'string'
                        ),
                        'geographical' => array(
                                'name' => 'geographical',
                                'type' => 'string'
                        ),
                        'classification' => array(
                                'name' => 'classification',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'comment' => array(
                                'name' => 'comment',
                                'type' => 'string'
                        ),
                        'status' => array(
                                'name' => 'status',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'transparency' => array(
                                'name' => 'transparency',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'sequence' => array(
                                'name' => 'sequence',
                                'type' => 'integer'
                        ),
                        'priority' => array(
                                'name' => 'priority',
                                'type' => 'integer'
                        ),
                        'start' => array(
                                'name' => 'start',
                                'type' => 'datetime'
                        ),
                        'end' => array(
                                'name' => 'end',
                                'type' => 'datetime'
                        ),
                        'timezone' => array(
                                'name' => 'timezone',
                                'type' => 'string'
                        ),
                        'duration' => array(
                                'name' => 'duration',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'is_deleted' => array(
                                'name' => 'is_deleted',
                                'type' => 'boolean'
                        ),
                        'is_recurrence' => array(
                                'name' => 'is_recurrence',
                                'type' => 'boolean'
                        ),
                        'organizer' => array(
                                'name' => 'organizer_ics',
                                'ObjectType' => 'PHP\\Organizer'
                        ),
                        'attendees' => array(
                                'name' => 'attendees_ics',
                                'ObjectType' => 'PHP\\Attendee',
                                'list' => true,
                                'json' => true
                        ),
                        'alarm' => array(
                                'name' => 'alarm_ics',
                                'ObjectType' => 'PHP\\Alarm'
                        ),
                        'categories' => array(
                                'name' => 'categories',
                                'type' => 'string',
                                'list' => true,
                                'json' => true
                        ),
                        'recurrence' => array(
                                'name' => 'recurrence_ics',
                                'ObjectType' => 'PHP\\Recurrence'
                        ),
                        'recurrence_enddate' => array(
                                'name' => 'recurrence_enddate',
                                'type' => 'datetime'
                        ),
                        'exceptions' => array(
                                'name' => 'exceptions_ics',
                                'ObjectType' => 'PHP\\Exception',
                                'list' => true,
                                'json' => true
                        ),
                        'attachments' => array(
                                'name' => 'attachments_ics',
                                'ObjectType' => 'PHP\\Attachment',
                                'list' => true,
                                'json' => true
                        ),
                        'properties' => array(
                                'name' => 'properties_ics',
                                'ObjectType' => 'PHP\\Property',
                                'list' => true,
                                'json' => true
                        )
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
                                        'fieldsForSearch' => array(
                                                'uid' => true,
                                                'calendar' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'exists' => array(
                                'name' => 'read',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'operator' => 'and',
                                'var' => true,
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true,
                                                'calendar' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'insert' => array(
                                'name' => 'create',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'operator' => 'and'
                        ),
                        'update' => array(
                                'name' => 'update',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'operator' => 'and',
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true,
                                                'calendar' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'save' => array(
                                'method' => array(
                                        'exists' => array(
                                                false => 'insert',
                                                true => 'update'
                                        )
                                ),
                                'return' => 'boolean',
                                'results' => 'combined',
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true,
                                                'calendar' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
                        ),
                        'delete' => array(
                                'name' => 'delete',
                                'return' => 'boolean',
                                'results' => 'combined',
                                'data' => array(
                                        'fieldsForSearch' => array(
                                                'uid' => true,
                                                'calendar' => true
                                        ),
                                        'usePrimaryKeys' => false
                                )
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
                                        'unsensitiveFields'
                                )
                        )
                )
        ),
        // Object organizer
        array(
                'ObjectType' => 'PHP\\Organizer',
                'Driver' => 'default',
                'fields' => array(
                        'name' => array(
                                'name' => 'name',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'email' => array(
                                'name' => 'email',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'role' => array(
                                'name' => 'role',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'partstat' => array(
                                'name' => 'partstat',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'sent_by' => array(
                                'name' => 'sent_by',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'directory' => array(
                                'name' => 'directory',
                                'type' => 'string'
                        )
                )
        ),
        // Object attendee
        array(
                'ObjectType' => 'PHP\\Attendee',
                'Driver' => 'default',
                'fields' => array(
                        'name' => array(
                                'name' => 'name',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'email' => array(
                                'name' => 'email',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'role' => array(
                                'name' => 'role',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'partstat' => array(
                                'name' => 'partstat',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'sent_by' => array(
                                'name' => 'sent_by',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'delegated_from' => array(
                                'name' => 'delegated_from',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'directory' => array(
                                'name' => 'directory',
                                'type' => 'string'
                        )
                )
        ),
        // Object alarm
        array(
                'ObjectType' => 'PHP\\Alarm',
                'Driver' => 'default',
                'fields' => array(
                        'trigger' => array(
                                'name' => 'trigger',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'action' => array(
                                'name' => 'action',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'repeat' => array(
                                'name' => 'repeat',
                                'type' => 'integer'
                        ),
                        'duration' => array(
                                'name' => 'duration',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'attachment' => array(
                                'name' => 'attachment',
                                'ObjectType' => 'PHP\\Attachment'
                        )
                )

        ),
        // Object property
        array(
                'ObjectType' => 'PHP\\Property',
                'Driver' => 'default',
                'fields' => array(
                        'name' => array(
                                'name' => 'name',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'value' => array(
                                'name' => 'value',
                                'type' => 'string'
                        ),
                        'params' => array(
                                'name' => 'params',
                                'type' => 'string',
                                'list' => true
                        )
                )
        ),
        // Object attachment
        array(
                'ObjectType' => 'PHP\\Attachment',
                'Driver' => 'default',
                'fields' => array(
                        'name' => array(
                                'name' => 'name',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'type' => array(
                                'name' => 'type',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'mimetype' => array(
                                'name' => 'mimetype',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'size' => array(
                                'name' => 'size',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'uri' => array(
                                'name' => 'uri',
                                'type' => 'string'
                        )
                )
        ),
        // Object recurrence
        array(
                'ObjectType' => 'PHP\\Recurrence',
                'Driver' => 'default',
                'fields' => array(
                        'frequence' => array(
                                'name' => 'frequence',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'count' => array(
                                'name' => 'count',
                                'type' => 'integer'
                        ),
                        'interval' => array(
                                'name' => 'interval',
                                'type' => 'integer'
                        ),
                        'byday' => array(
                                'name' => 'byday',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'bymonth' => array(
                                'name' => 'bymonth',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'bymonthday' => array(
                                'name' => 'bymonthday',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'byyearday' => array(
                                'name' => 'byyearday',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'until' => array(
                                'name' => 'until',
                                'type' => 'datetime'
                        ),
                        'wkst' => array(
                                'name' => 'wkst',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'dates' => array(
                                'name' => 'dates',
                                'type' => 'datetime',
                                'list' => true
                        )
                )
        ),
        // Object exception
        array(
                'ObjectType' => 'PHP\\Exception',
                'Driver' => 'default',
                'fields' => array(
                        'uid' => array(
                                'name' => 'uid',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'created' => array(
                                'name' => 'created',
                                'type' => 'timestamp'
                        ),
                        'modified' => array(
                                'name' => 'modified',
                                'type' => 'timestamp'
                        ),
                        'summary' => array(
                                'name' => 'summary',
                                'type' => 'string',
                                'size' => 255
                        ),
                        'description' => array(
                                'name' => 'description',
                                'type' => 'string'
                        ),
                        'location' => array(
                                'name' => 'location',
                                'type' => 'string'
                        ),
                        'geographical' => array(
                                'name' => 'location',
                                'ObjectType' => 'PHP\\Geographical'
                        ),
                        'classification' => array(
                                'name' => 'classification',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'comment' => array(
                                'name' => 'comment',
                                'type' => 'string'
                        ),
                        'status' => array(
                                'name' => 'status',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'transparency' => array(
                                'name' => 'transparency',
                                'type' => 'string',
                                'size' => 120
                        ),
                        'sequence' => array(
                                'name' => 'sequence',
                                'type' => 'integer'
                        ),
                        'priority' => array(
                                'name' => 'priority',
                                'type' => 'integer'
                        ),
                        'start' => array(
                                'name' => 'start',
                                'type' => 'datetime'
                        ),
                        'end' => array(
                                'name' => 'end',
                                'type' => 'datetime'
                        ),
                        'timezone' => array(
                                'name' => 'timezone',
                                'type' => 'string'
                        ),
                        'duration' => array(
                                'name' => 'duration_ics',
                                'ObjectType' => 'PHP\\Duration'
                        ),
                        'is_deleted' => array(
                                'name' => 'is_deleted',
                                'type' => 'boolean'
                        ),
                        'organizer' => array(
                                'name' => 'organizer_ics',
                                'ObjectType' => 'PHP\\Organizer'
                        ),
                        'attendees' => array(
                                'name' => 'attendees_ics',
                                'ObjectType' => 'PHP\\Attendee',
                                'list' => true
                        ),
                        'alarm' => array(
                                'name' => 'alarm_ics',
                                'ObjectType' => 'PHP\\Alarm'
                        ),
                        'categories' => array(
                                'name' => 'categories',
                                'type' => 'list'
                        ),
                        'attachments' => array(
                                'name' => 'attachments_ics',
                                'ObjectType' => 'PHP\\Attachment',
                                'list' => true
                        ),
                        'properties' => array(
                                'name' => 'properties_ics',
                                'ObjectType' => 'PHP\\Property',
                                'list' => true
                        )
                )
        )
);
