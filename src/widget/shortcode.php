<?php

require_once(__DIR__ . '/../shared/front-renderer.php');

function traveledmap_embedded_trip_shortcode($atts = '')
{
	$tripBaseUrl = get_post_meta(get_the_ID(), 'traveledmap_trip_base_url', true);

	$attributes = shortcode_atts(array(
		'show_popup' => '0',
		'show_pictures' => '1',
		'show_pictures_at_start' => '0',
		'is_sticky' => '1',
		'show_on_phones' => '1',
		'show_on_tablets' => '1',
		'show_on_large_screens' => '1',
		'map_height' => '50%',
		'standard_map_height' => '30%',
		'extended_map_height' => '60%',
		'margin_top' => 0,
	), $atts);

	return traveledmap_render_callback([
		'showPopup' => $attributes['show_popup'],
		'showPictures' => $attributes['show_pictures'],
		'showPicturesAtStart' => $attributes['show_pictures_at_start'],
		'isSticky' => $attributes['is_sticky'],
		'showOnPhones' => $attributes['show_on_phones'],
		'showOnTablets' => $attributes['show_on_tablets'],
		'showOnLargeScreens' => $attributes['show_on_large_screens'],
		'mapHeight' => $attributes['map_height'],
		'standardMapHeight' => $attributes['standard_map_height'],
		'extendedMapHeight' => $attributes['extended_map_height'],
		'marginTop' => $attributes['margin_top'],
	]);
}

function traveledmap_embedded_trip_step_shortcode($atts = '')
{
	$attributes = shortcode_atts(array(
		'location' => '',
	), $atts);

	return '<span class="traveledmap-trip-step" data-step="' . esc_html($attributes['location']) . '" ></span>';
}

add_shortcode('embedded_trip', 'traveledmap_embedded_trip_shortcode');
add_shortcode('embedded_trip_step', 'traveledmap_embedded_trip_step_shortcode');
