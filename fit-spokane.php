<?php

/**
 * Plugin Name: Fit Spokane
 * Plugin URI: https://fitspokane.com
 * Description: Custom Plugin for Fit Spokane
 * Author: Tony DeStefano
 * Author URI: https://www.facebook.com/TonyDeStefanoWebcom
 * Version: 1.0.0
 * Text Domain: fit-spokane
 *
 * Copyright 2016 Tony DeStefano
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

require_once ( 'classes/FitSpokane/Controller.php' );
require_once ( 'classes/FitSpokane/Program.php' );
require_once ( 'classes/FitSpokane/Payment.php' );
require_once ( 'classes/Stripe/init.php' );

/* controller object */
$fit_spokane_controller = new \FitSpokane\Controller;

/* activate */
register_activation_hook( __FILE__, array( $fit_spokane_controller, 'activate' ) );

/* enqueue js and css */
add_action( 'init', array( $fit_spokane_controller, 'init' ) );

/* Create custom post type */
add_action( 'init', array( $fit_spokane_controller, 'create_post_type' ) );

/* capture form post */
add_action ( 'init', array( $fit_spokane_controller, 'form_capture' ) );

/* register shortcode */
add_shortcode ( 'fit_spokane', array( $fit_spokane_controller, 'short_code' ) );

/* admin stuff */
if (is_admin() )
{
	/* Add main menu and sub-menus */
	add_action( 'admin_menu', array( $fit_spokane_controller, 'admin_menus') );

	/* register settings */
	add_action( 'admin_init', array( $fit_spokane_controller, 'register_settings' ) );

	/* admin scripts */
	add_action( 'admin_init', array( $fit_spokane_controller, 'admin_scripts' ) );

	/* create custom attributes for post type */
	add_action( 'add_meta_boxes_' . \FitSpokane\Program::POST_TYPE , array( $fit_spokane_controller, 'extra_program_meta' ) );

	/* remove the "view" action from the custom post type */
	add_filter( 'post_row_actions', array( $fit_spokane_controller, 'remove_row_actions' ), 10, 1 );
	add_filter('post_updated_messages', array( $fit_spokane_controller, 'custom_post_type_messages' ) );
	
	/* custom columns */
	add_filter( 'manage_' . \FitSpokane\Program::POST_TYPE . '_posts_columns', array( $fit_spokane_controller, 'add_new_program_columns' ) );
	add_action( 'manage_' . \FitSpokane\Program::POST_TYPE . '_posts_custom_column' , array( $fit_spokane_controller, 'custom_program_columns' ) );
	add_filter( 'manage_' . \FitSpokane\Payment::POST_TYPE . '_posts_columns', array( $fit_spokane_controller, 'add_new_payment_columns' ) );
	add_action( 'manage_' . \FitSpokane\Payment::POST_TYPE . '_posts_custom_column' , array( $fit_spokane_controller, 'custom_payment_columns' ) );

	/* Save meta */
	add_action( 'save_post', array( $fit_spokane_controller, 'save_program_meta' ), 10, 2 );

	/* add the instructions page link */
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $fit_spokane_controller, 'instructions_link' ) );

	/* add the instructions page */
	add_action( 'admin_menu', array( $fit_spokane_controller, 'instructions_page' ) );
}
