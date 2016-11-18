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

/***** NE PAS MODIFIER CETTE PARTIE *****/
/* Définition des environnements disponibles */
define('IDA', 'ida');

/* Définition des environnements pour les configurations externes */
/**
 * Configuration externe vers un répertoire
 */
define('TYPE_EXTERNAL', 'external');
/**
 * Configuration dans le répertoire config/ de l'ORM
 */
define('TYPE_INTERNAL', 'internal');

/**
 * Configuration externe pour une seule application
 */
define('MODE_SIMPLE', 'simple');
/**
 * Configuration externe pour plusieurs applications
 */
define('MODE_MULTIPLE', 'multiple');


/****** PARTIE CONFIGURATION A MODIFIER SI BESOIN ****/
/**
 * Configuration externe ou interne
 * La configuration TYPE_INTERNAL va lire les données dans le répertoire /config de l'ORM
 * Dans ce cas la configuration chargée sera fonction du ENVIRONNEMENT_LIBORM
 * La configuration TYPE_EXTERNAL va les lire les données dans un répertoire configuré dans CONFIGURATION_PATH_LIBORM
 */
define('CONFIGURATION_TYPE_LIBORM', TYPE_EXTERNAL);


/****** CONFIGURATION INTERNE ******/
/**
 * Choix de l'environnement à configurer, si utilisation de la configuration interne
 */
define('ENVIRONNEMENT_LIBORM', '');


/***** CONFIGURATION EXTERNE *******/
/**
 * Chemin vers la configuration externe
 */
// Linux config files
//define('CONFIGURATION_PATH_LIBORM', '/home/thomas/git/github/ORM-MongoDB/config/ida_mongodb/');
//define('CONFIGURATION_PATH_LIBORM', '/home/thomas/git/github/ORM-MongoDB/config/ida_pgsql/');
//define('CONFIGURATION_PATH_LIBORM', '/home/thomas/git/github/ORM-MongoDB/config/ida_mysql/');
define('CONFIGURATION_PATH_LIBORM', '/home/thomas/git/github/ORM-MongoDB/config/ida_melanie/');

// Mac OSX config files
//define('CONFIGURATION_PATH_LIBORM', '/Users/thomas/Development/git/ORM-MongoDB/config/ida_mongodb/');
//define('CONFIGURATION_PATH_LIBORM', '/Users/thomas/Development/git/ORM-MongoDB/config/ida_mysql/');


/**
 * MODE_SIMPLE ou MODE_MULTIPLE pour la configuration TYPE_EXTERNAL
 * Le MODE_SIMPLE va lire les données directement dans le CONFIGURATION_PATH
 * Le MODE_MULTIPLE permet de gérer plusieurs configuration dans le CONFIGURATION_PATH_LIBORM
 * Dans ce cas la configuration va être lu dans le répertoire correspondant au CONFIGURATION_APP_LIBORM
 * qui doit être configuré dans l'application
 */
define('CONFIGURATION_MODE_LIBORM', MODE_SIMPLE);
