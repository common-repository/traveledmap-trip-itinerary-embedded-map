<?php

function traveledmap_render_callback($attributes)
{
    $post = get_queried_object();
    if (!$post) {
        return '';
    }

    $userId = get_post_meta($post->ID, 'traveledmap_user_id', true);
    $tripId = get_post_meta($post->ID, 'traveledmap_trip_id', true);

	if (!$userId || !$tripId) {
		return 'Missing user id or trip id userId=' . $userId . ' tripId=' . $tripId;
	}

	$marginTop = isset($attributes['marginTop']) ? esc_js((int) $attributes['marginTop']) : 0;
	$mapHeight = TraveledMap_Utils::convertHeightFromPercentToVH(esc_html($attributes['mapHeight']));
	$standardMapHeight = TraveledMap_Utils::convertHeightFromPercentToVH(esc_html($attributes['standardMapHeight']));
	$stretchedMapHeight = TraveledMap_Utils::convertHeightFromPercentToVH(esc_html($attributes['extendedMapHeight']));

	$shouldShowPictures = (bool) esc_html($attributes['showPictures']);
	$shouldShowPicturesAtStart = (bool) esc_html($attributes['showPicturesAtStart']);
	$shouldShowPopup = (bool) esc_html($attributes['showPopup']);
	$showOnPhones = (bool) esc_html($attributes['showOnPhones']);
	$showOnTablets = (bool) esc_html($attributes['showOnTablets']);
	$showOnLargeScreens = (bool) esc_html($attributes['showOnLargeScreens']);
	$isSticky = (bool) $attributes['isSticky'];

	$elementId = "traveledmap-stretchable-map-" . uniqid();

    wp_enqueue_script(
        "trip-embedder-js",
        strtr('https://cdn.jsdelivr.net/gh/traveledmap/trip-embedder-js@$version/dist/traveledmap-trip.min.js', array('$version' => SDK_VERSION))
    );

    wp_enqueue_style(
        "trip-embedder-css",
        strtr('https://cdn.jsdelivr.net/gh/traveledmap/trip-embedder-js@$version/dist/traveledmap-trip.min.css', array('$version' => SDK_VERSION))
    );

	return '
		<div>
			<div id="' . $elementId . '"></div>

			<script type="text/javascript">
              ' . TraveledMap_Utils::getStepBlockRetrocompatibilityJavascript() . '

              const handleTraveledMapTripBlockLoad = function () {
                new TraveledMapTrip("' . $elementId . '", {
                  userId: "' . $userId . '",
                  tripId: "' . $tripId . '",
                  isDisabled: false,
                  isSticky: ' . TraveledMap_Utils::varToBool($isSticky) . ',
                  isExtendable: false,
                  isStretchable: true,
                  mapHeight: "' . $mapHeight . '",
                  stickyMapHeight: "' . $standardMapHeight . '",
                  stretchedMapHeight: "' . $stretchedMapHeight . '",
                  stickyMarginTop: ' . $marginTop . ',
                  shouldExtendToTop: false, // not applicable
                  shouldExtendToRight: false, // not applicable
                  shouldExtendToBottom: false, // not applicable
                  shouldExtendToLeft: false, // not applicable
                  marginTop: null, // not applicable
                  marginRight: null, // not applicable
                  marginBottom: null, // not applicable
                  marginLeft: null, // not applicable
                  shouldShowMarkersCustomization: false,
                  shouldShowPictures: ' . TraveledMap_Utils::varToBool($shouldShowPictures) . ',
                  shouldShowPicturesAtStart: ' . TraveledMap_Utils::varToBool($shouldShowPicturesAtStart) . ',
                  shouldShowOnPhones: ' . TraveledMap_Utils::varToBool($showOnPhones) . ',
                  shouldShowOnTablets: ' . TraveledMap_Utils::varToBool($showOnTablets) . ',
                  shouldShowOnLargeScreens: ' . TraveledMap_Utils::varToBool($showOnLargeScreens) . ',
                  shouldShowSteps: ' . TraveledMap_Utils::varToBool($shouldShowPopup) . ',
                  shouldShowStepsWhenExtended: null, // not applicable
                  shouldShowStepsWhenStretched: ' . TraveledMap_Utils::varToBool($shouldShowPopup) . ',
                  shouldShowPicturesWhenExtended: null, // not applicable
                  shouldShowPicturesWhenStretched: ' . TraveledMap_Utils::varToBool($shouldShowPictures) . ',
                  isOverContent: ' . TraveledMap_Utils::varToBool($isSticky) . ',
                  shouldScrollToStepContentWhenClicked: true,
                })
              }

              if (window.addEventListener) {
                window.addEventListener("load", handleTraveledMapTripBlockLoad, false);
              } else if (window.attachEvent) { // IE DOM
                window.attachEvent("onload", handleTraveledMapTripBlockLoad);
              } else {
                handleTraveledMapTripBlockLoad();
              }
            </script>
		</div>
	';
}
