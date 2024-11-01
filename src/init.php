<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

require_once('metabox/metabox.php'); // the metabox is the right column on the backoffice
require_once('shared/utils/utils.php'); // utils
require_once('shared/front-renderer.php'); // utils that helps rendering the map in an iframe
require_once('shared/constants/env.php'); // constants for the environment
require_once('widget/widget.php'); // All the functions that allows the widget to work (save data and so on)
require_once('widget/shortcode.php'); // The shortcode to display the map without block
require_once('categories/traveledmap.php'); // The category that contains all the blocks

function traveledmap_trip_block_assets()
{
	// Embedded trip block ---------------------------------------
	wp_register_style(
		'traveledmap-trip-style-css', // Handle.
		plugins_url('src/blocks/dist/blocks.style.build.css', dirname(__FILE__)),
		array('wp-editor'),
		null
	);

	// Register block editor script for backend.
	wp_register_script(
		'traveledmap-trip-block-js', // Handle.
		plugins_url('/src/blocks/dist/blocks.build.js', dirname(__FILE__)), // Block.build.js: We register the block here. Built with Webpack.
		array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	wp_register_style(
		'traveledmap-trip-block-editor-css', // Handle.
		plugins_url('src/blocks/dist/blocks.editor.build.css', dirname(__FILE__)), // Block editor CSS.
		array('wp-edit-blocks'), // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	// Other libs -----------------------------------------------
	wp_register_script(
		'traveledmap_map-sticky-js',
		plugins_url('/dist/sticky.min.js', dirname(__FILE__)),
		array(),
		null,
		true
	);

    // TODO: Introduced in 5.8.0: add_filter('block_categories_all', 'add_new_traveledmap_category', 10, 2);
    // but wait to introduce it for older versions
	add_filter('block_categories', 'add_new_traveledmap_category', 10, 2);

	register_block_type(
		'traveledmap/embedded-trip-block', array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style' => 'traveledmap-trip-style-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'traveledmap-trip-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style' => 'traveledmap-trip-block-editor-css',
			// Server side rendering
			'render_callback' => 'traveledmap_render_callback',
			'attributes' => array(
				'tripId' => array('type' => 'string'),
				'traveledmap_user_id' => array('type' => 'string'),
				'traveledmap_trip_base_url' => array('type' => 'string'),
				'baseUrl' => array('type' => 'string'),
				'traveledmap_trip_steps' => array('type' => 'string'),
				'traveledmap_disable_widget' => array('type' => 'boolean', 'default' => false),
				'traveledmap_show_markers_customization' => array('type' => 'boolean', 'default' => false),
				'showPopup' => array('type' => 'boolean', 'default' => false),
				'showPictures' => array('type' => 'boolean', 'default' => true),
				'showPicturesAtStart' => array('type' => 'boolean', 'default' => false),
				'isSticky' => array('type' => 'boolean', 'default' => true),
				'showOnPhones' => array('type' => 'boolean', 'default' => true),
				'showOnTablets' => array('type' => 'boolean', 'default' => true),
				'showOnLargeScreens' => array('type' => 'boolean', 'default' => true),
				'mapHeight' => array('type' => 'string', 'default' => '50%'),
				'standardMapHeight' => array('type' => 'string', 'default' => '30%'),
				'extendedMapHeight' => array('type' => 'string', 'default' => '60%'),
				'marginTop' => array('type' => 'number', 'default' => 0),
			)
		)
	);
}

add_action('init', 'traveledmap_trip_block_assets');
