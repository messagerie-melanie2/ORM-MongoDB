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


/**
 * Queries
 */
$config['queries'] = array(
        /**
         * Lister les calendriers de l'utilisateur
         */
        'listUserCalendars' => 'SELECT *, \'30\' as share_value FROM calendars WHERE owner = :user_uid;',
        /**
         * Lister les calendriers auquel a accès l'utilisateur
         */
        'listUserSharedCalendars' => 'SELECT cal.uid as uid, cal.name as name, cal.owner as owner, cal.created as created, cal.ctag as ctag, cal.properties as properties FROM calendars cal INNER JOIN calendar_shares sha ON cal.uid = sha.calendar_id WHERE user_uid = :user_uid;',
);