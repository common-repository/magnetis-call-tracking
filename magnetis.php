<?php

/**
 * Plugin Name:     MAGNETIS Call-tracking
 * Plugin URI:      https://www.magnetis.fr/call-tracking-wordpress
 * Description:     Analysez l'origine de vos appels entrants avec le Call-tracking Magnétis. Déterminez quels canaux vous génèrent le plus d’appels et optimisez votre budget de communication. L'extension de Call-Tracking Magnétis intègre vos numéros trackés directement dans votre site Wordpress. Retrouvez ensuite vos statistiques d'attribution dans votre interface de call-tracking Magnetis.
 * Author:          Magnetis
 * Author URI:      https://www.magnetis.fr
 * Text Domain:     magnetis-call-tracking
 * Domain Path:     /languages
 * Version:         7.1
 *
 * @package         Magnetis_Call_Tracking
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

require_once plugin_dir_path(__FILE__) . '/lib/autoload.php';

// Add the plugin to WordPress
\Magnetis\MagnetisPlugin::instance();
