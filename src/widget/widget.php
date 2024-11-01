<?php

add_action('widgets_init', function () {
	register_widget('TraveledMap_Trip_Widget');
});

class TraveledMap_Trip_Widget extends WP_Widget
{
	const MARGIN_DEFAULT = 20;
	const MAP_HEIGHT_DEFAULT = "300px";
	const ID_FINDER_BASE = 'traveledMap_trip_widget_id_finder-';

	public function __construct()
	{
		// with elementor, the widget doesn't have the id given by wordpress
		// therefore we need to define a custom property and give the id to an element manually
		$this->idFinder = self::ID_FINDER_BASE . uniqid();
		$widget_ops = array(
			'classname' => 'traveledMap_trip_widget',
			'description' => 'A widget to embed a trip on your blog posts',
		);
		parent::__construct('traveledMap_trip_widget', 'TraveledMap Trip', $widget_ops);
	}

	// output the widget content on the front-end
	public function widget($args, $instance)
	{
		$post = get_queried_object();
		if (!$post && !is_front_page()) {
		    echo '<img src="' . plugins_url( '../../assets/no-preview.jpg', __FILE__ ) . '" alt="No preview"/>';
            return;
        }

		if (!$post && is_front_page()) {
			echo '<p class="traveledmap-is-hidden">Queried object wasnt found</p>';
            return;
        }

		$isDisabled = get_post_meta($post->ID, 'traveledmap_disable_widget', true);

		if ($isDisabled === "1") {
			echo '<p class="traveledmap-is-hidden">Widget is disabled for this post</p>';
			return;
		}

		$userId = get_post_meta($post->ID, 'traveledmap_user_id', true);
		$tripId = get_post_meta($post->ID, 'traveledmap_trip_id', true);
		$showMarkersCustomization = get_post_meta($post->ID, 'traveledmap_show_markers_customization', true);

		if (!$userId || !$tripId || strlen($userId) === 0 || strlen($tripId) === 0) {
			echo '<p class="traveledmap-is-hidden">Couldnt find the user id or trip id for post id ' . $post->ID . '</p>';
			return;
		}

		$widgetId = $this->idFinder;

		$extendedTop = self::instanceVarToBool($instance, 'extended_top');
		$extendedRight = self::instanceVarToBool($instance, 'extended_right');
		$extendedBottom = self::instanceVarToBool($instance, 'extended_bottom');
		$extendedLeft = self::instanceVarToBool($instance, 'extended_left');

		$marginTop = isset($instance['margin_top']) ? esc_js($instance['margin_top']) : NULL;
		$marginRight = isset($instance['margin_right']) ? esc_js($instance['margin_right']) : NULL;
		$marginBottom = isset($instance['margin_bottom']) ? esc_js($instance['margin_bottom']) : NULL;
		$marginLeft = isset($instance['margin_left']) ? esc_js($instance['margin_left']) : NULL;

		$extendedShowSteps = self::instanceVarToBool($instance, 'extended_show_steps');
		$extendedShowPictures = self::instanceVarToBool($instance, 'extended_show_pictures');

		$mapHeight = isset($instance['map_height']) ? esc_attr($instance['map_height']) : NULL;

		$isSticky = self::instanceVarToBool($instance, 'is_sticky');
		$notExtendedShowSteps = self::instanceVarToBool($instance, 'not_extended_show_steps');
		$extendable = self::instanceVarToBool($instance, 'extandable');

		$showOnPhones = self::instanceVarToBool($instance, 'show_on_phones');
		$showOnTablets = self::instanceVarToBool($instance, 'show_on_tablets');
		$showOnLargeScreens = self::instanceVarToBool($instance, 'show_on_large_screens');

		echo $args['before_widget'];
		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}

		$elementId = 'traveledmap-extendable-map-' . $widgetId;

        wp_enqueue_script(
            "trip-embedder-js",
            strtr('https://cdn.jsdelivr.net/gh/traveledmap/trip-embedder-js@$version/dist/traveledmap-trip.min.js', array('$version' => SDK_VERSION))
        );

        wp_enqueue_style(
            "trip-embedder-css",
            strtr('https://cdn.jsdelivr.net/gh/traveledmap/trip-embedder-js@$version/dist/traveledmap-trip.min.css', array('$version' => SDK_VERSION))
        );

		echo '
			<div>
				<div id="' . $elementId . '"></div>

				<script type="text/javascript">
                  ' . TraveledMap_Utils::getStepBlockRetrocompatibilityJavascript() . '

				  const handleTraveledMapWidgetLoad = function () {
					new TraveledMapTrip("' . $elementId . '", {
					  userId: "' . $userId . '",
					  tripId: "' . $tripId . '",
					  isDisabled: false,
					  isSticky: ' . TraveledMap_Utils::varToBool($isSticky) . ',
					  isExtendable: ' . TraveledMap_Utils::varToBool($extendable) . ',
					  isStretchable: false,
					  mapHeight: "' . $mapHeight . '",
					  stickyMapHeight: "' . $mapHeight . '",
					  stretchedMapHeight: null, // not applicable
					  stickyMarginTop: null, // not applicable
					  shouldExtendToTop: ' . TraveledMap_Utils::varToBool($extendedTop) . ',
					  shouldExtendToRight: ' . TraveledMap_Utils::varToBool($extendedRight) . ',
					  shouldExtendToBottom: ' . TraveledMap_Utils::varToBool($extendedBottom) . ',
					  shouldExtendToLeft: ' . TraveledMap_Utils::varToBool($extendedLeft) . ',
					  marginTop: ' . $marginTop . ',
					  marginRight: ' . $marginRight . ',
					  marginBottom: ' . $marginBottom . ',
					  marginLeft: ' . $marginLeft . ',
					  shouldShowMarkersCustomization: ' . TraveledMap_Utils::varToBool($showMarkersCustomization) . ',
					  shouldShowPictures: false,
					  shouldShowPicturesAtStart: false,
					  shouldShowOnPhones: ' . TraveledMap_Utils::varToBool($showOnPhones) . ',
					  shouldShowOnTablets: ' . TraveledMap_Utils::varToBool($showOnTablets) . ',
					  shouldShowOnLargeScreens: ' . TraveledMap_Utils::varToBool($showOnLargeScreens) . ',
					  shouldShowSteps: ' . TraveledMap_Utils::varToBool($notExtendedShowSteps) . ',
					  shouldShowStepsWhenExtended: ' . TraveledMap_Utils::varToBool($extendedShowSteps) .',
					  shouldShowStepsWhenStretched: null, // not applicable
					  shouldShowPicturesWhenExtended: ' . TraveledMap_Utils::varToBool($extendedShowPictures) . ',
					  shouldShowPicturesWhenStretched: false, // not applicable
					  isOverContent: false,
					  shouldScrollToStepContentWhenClicked: true,
					})
				  }

				  if (window.addEventListener) {
					window.addEventListener("load", handleTraveledMapWidgetLoad, false);
				  } else if (window.attachEvent) { // IE DOM
					window.attachEvent("onload", handleTraveledMapWidgetLoad);
				  } else {
					handleTraveledMapWidgetLoad();
				  }
				</script>
			</div>
		';

		echo $args['after_widget'];
	}

	// output the option form field in admin Widgets screen
	public function form($instance)
	{
		$thisWidgetRandomId = rand(0, 10000000);
		$title = !empty($instance['title']) ? esc_attr($instance['title']) : '';
		$titleId = esc_attr($this->get_field_id('title'));
		$titleName = esc_attr($this->get_field_name('title'));
		$label = esc_attr(translate("Title", "text_domain"));

		$isStickyName = esc_attr($this->get_field_name('is_sticky'));
		$isStickyValue = esc_attr(self::instanceVarToChecked($instance, 'is_sticky'));

		$showOnPhoneName = esc_attr($this->get_field_name('show_on_phones'));
		$showOnPhonesValue = esc_attr(self::instanceVarToChecked($instance, 'show_on_phones'));
		$showOnTabletsName = esc_attr($this->get_field_name('show_on_tablets'));
		$showOnTabletsValue = esc_attr(self::instanceVarToChecked($instance, 'show_on_tablets'));
		$showOnLargeScreensName = esc_attr($this->get_field_name('show_on_large_screens'));
		$showOnLargeScreensValue = esc_attr(self::instanceVarToChecked($instance, 'show_on_large_screens'));

		$mapHeightName = esc_attr($this->get_field_name('map_height'));
		$mapHeightValue = isset($instance['map_height']) ? esc_attr($instance['map_height']) : self::MAP_HEIGHT_DEFAULT;

		$extendableName = esc_attr($this->get_field_name('extandable'));
		$extendableValue = esc_attr(self::instanceVarToChecked($instance, 'extandable'));
		$extendedTopName = esc_attr($this->get_field_name('extended_top'));
		$extendedTopValue = esc_attr(self::instanceVarToChecked($instance, 'extended_top'));
		$extendedRightName = esc_attr($this->get_field_name('extended_right'));
		$extendedRightValue = esc_attr(self::instanceVarToChecked($instance, 'extended_right'));
		$extendedBottomName = esc_attr($this->get_field_name('extended_bottom'));
		$extendedBottomValue = esc_attr(self::instanceVarToChecked($instance, 'extended_bottom'));
		$extendedLeftName = esc_attr($this->get_field_name('extended_left'));
		$extendedLeftValue = esc_attr(self::instanceVarToChecked($instance, 'extended_left'));

		$marginTopName = esc_attr($this->get_field_name('margin_top'));
		$marginTopValue = isset($instance['margin_top']) ? esc_attr($instance['margin_top']) : self::MARGIN_DEFAULT;
		$marginRightName = esc_attr($this->get_field_name('margin_right'));
		$marginRightValue = isset($instance['margin_right']) ? esc_attr($instance['margin_right']) : self::MARGIN_DEFAULT;
		$marginBottomName = esc_attr($this->get_field_name('margin_bottom'));
		$marginBottomValue = isset($instance['margin_bottom']) ? esc_attr($instance['margin_bottom']) : self::MARGIN_DEFAULT;
		$marginLeftName = esc_attr($this->get_field_name('margin_left'));
		$marginLeftValue = isset($instance['margin_left']) ? esc_attr($instance['margin_left']) : self::MARGIN_DEFAULT;

		$notExtendedShowStepsName = esc_attr($this->get_field_name('not_extended_show_steps'));
		$notExtendedShowStepsValue = esc_attr(self::instanceVarToChecked($instance, 'not_extended_show_steps'));
		$extendedShowStepsName = esc_attr($this->get_field_name('extended_show_steps'));
		$extendedShowStepsValue = esc_attr(self::instanceVarToChecked($instance, 'extended_show_steps'));
		$extendedShowPicturesName = esc_attr($this->get_field_name('extended_show_pictures'));
		$extendedShowPicturesValue = esc_attr(self::instanceVarToChecked($instance, 'extended_show_pictures'));

		$extendedWrapperClass = $extendableValue === "checked" ? '' : 'is-hidden';

		echo '
			<p>
				<h3>Widget</h3>
				<label for="' . $titleId . '"><strong>' . $label . '</strong></label>
				<input type="text" id="' . $titleId . '" name="' . $titleName . '" value="' . $title . '" class="widefat" maxlength="40">
			</p>
		';

		echo '
			<p>
				<input type="checkbox" name="' . $showOnPhoneName . '" ' . $showOnPhonesValue . '>
				<label for="' . $showOnPhoneName . '">Show on phones</label>,&nbsp;
				<input type="checkbox" name="' . $showOnTabletsName . '" ' . $showOnTabletsValue . '>
				<label for="' . $showOnTabletsName . '">Show on tablets</label>,&nbsp;
				<input type="checkbox" name="' . $showOnLargeScreensName. '" ' . $showOnLargeScreensValue . '>
				<label for="' . $showOnLargeScreensName . '">Show on larger screens</label> <br />
				<small>
					You can decides on which type of device the widget will show up.
				</small>
			</p>
			<p>
				<label>Map height (in pixels or percentage)</label>
				<input type="text" name="' . $mapHeightName . '" value="' . $mapHeightValue . '" class="widefat" maxlength="10">
				<small>
					You can specify height in pixels of percentage. i.e: 500px or 60%<br />
					Percentage are relative to screen\'s height.
				</small>
			</p>
			<p>
				<input type="checkbox" name="' . $isStickyName . '" ' . $isStickyValue . '>
				<label>Widget is sticky</label> <br />
				<small>
					Sticky means that the widget will stay fixed on the screen while user scroll,
					to allow him to see the map whereever his current scroll is on the blog post.
				</small>
			</p>
			<hr />
		';

		echo '
			<p>
				<h3>Map not extended</h3>
			  	<input type="checkbox" name="' . $notExtendedShowStepsName . '" ' . $notExtendedShowStepsValue . '>
				<label>Show steps</label>
			</p>
			<hr />
		';

		echo '
			<p>
				<h3>Map extended</h3>
				<input type="checkbox" name="' . $extendableName . '" ' . $extendableValue . ' onclick="window.toggleExtendOptions[\'' . $thisWidgetRandomId . '\']()">
				<label>Can be expanded (to a large map, using options bellow - you may need to save and refresh to make them appear)</label> <br />
			</p>
		';

		echo '
			<div id="tm-extend-options-wrapper-' . $thisWidgetRandomId . '" class="tm-extend-options-wrapper ' . $extendedWrapperClass . '">
				<p>
					<strong>Extend options</strong><br />
					<input type="checkbox" name="' . $extendedShowStepsName . '" ' . $extendedShowStepsValue . '>
					<label>Show steps</label><br />
					<input type="checkbox" name="' . $extendedShowPicturesName . '" ' . $extendedShowPicturesValue . '>
					<label>Show pictures</label><br />
				</p>

				<p>
					<strong>Top</strong><br />
					<input type="checkbox" name="' . $extendedTopName . '" ' . $extendedTopValue . '>
					<label>Extend to the top</label> <br />
					<label>Top margin (in pixels)</label>
					<input type="number" name="' . $marginTopName . '" value="' . (int)$marginTopValue . '" class="widefat">
				</p>

				<p>
					<strong>Right</strong><br />
					<input type="checkbox" name="' . $extendedRightName . '" ' . $extendedRightValue . '>
					<label>Extend to the right</label><br />
					<label>Right margin (in pixels)</label>
					<input type="number" name="' . $marginRightName . '" value="' . (int)$marginRightValue . '" class="widefat">
				</p>

				<p>
					<strong>Bottom</strong><br />
					<input type="checkbox" name="' . $extendedBottomName . '" ' . $extendedBottomValue . '>
					<label>Extend to the bottom</label><br />
					<label>Bottom margin (in pixels)</label>
					<input type="number" name="' . $marginBottomName . '" value="' . (int)$marginBottomValue . '" class="widefat">
				</p>

				<p>
					<strong>Left</strong><br />
					<input type="checkbox" name="' . $extendedLeftName . '" ' . $extendedLeftValue . '>
					<label>Extend to the left</label><br />
					<label>Left margin (in pixels)</label>
					<input type="number" name="' . $marginLeftName . '" value="' . (int)$marginLeftValue . '" class="widefat">
				</p>

				<p>
					<strong>Help</strong>
					Margins: Space between the extended map and the screen\'s border, set in pixels (px)
					<br/>
				</p>
			</div>
		';

		echo '
			<style>
				.mt-20 {
					margin-top: 20px;
				}
				.mt-30 {
					margin-top: 30px;
				}
				.mb-0 {
					margin-bottom: 0;
				}
				.mt-0 {
					margin-top: 0;
				}
				.tm-extend-options-wrapper.is-hidden {
					display: none;
				}
			</style>

			<script>
				(function() {
				    const randomId = "' . $thisWidgetRandomId . '";
					const extendOptionsWrapperEl = document.getElementById(`tm-extend-options-wrapper-${randomId}`);
					if(!window.toggleExtendOptions) {
						window.toggleExtendOptions = {};
					}
					window.toggleExtendOptions[randomId] = function() {
					  if(extendOptionsWrapperEl.classList.contains("is-hidden")) {
						extendOptionsWrapperEl.classList.remove("is-hidden");
					  } else {
						extendOptionsWrapperEl.classList.add("is-hidden");
					  }
					};
				})();
			</script>
		';
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (isset($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
		$instance['margin_top'] = (isset($new_instance['margin_top'])) ? sanitize_text_field($new_instance['margin_top']) : self::MARGIN_DEFAULT;
		$instance['margin_right'] = (isset($new_instance['margin_right'])) ? sanitize_text_field($new_instance['margin_right']) : self::MARGIN_DEFAULT;
		$instance['margin_bottom'] = (isset($new_instance['margin_bottom'])) ? sanitize_text_field($new_instance['margin_bottom']) : self::MARGIN_DEFAULT;
		$instance['margin_left'] = (isset($new_instance['margin_left'])) ? sanitize_text_field($new_instance['margin_left']) : self::MARGIN_DEFAULT;
		$instance['map_height'] = (isset($new_instance['map_height'])) ? sanitize_text_field($new_instance['map_height']) : self::MAP_HEIGHT_DEFAULT;
		$instance['extandable'] = self::instanceVarToBool($new_instance, 'extandable');
		$instance['extended_top'] = self::instanceVarToBool($new_instance, 'extended_top');
		$instance['extended_right'] = self::instanceVarToBool($new_instance, 'extended_right');
		$instance['extended_bottom'] = self::instanceVarToBool($new_instance, 'extended_bottom');
		$instance['extended_left'] = self::instanceVarToBool($new_instance, 'extended_left');
		$instance['is_sticky'] = self::instanceVarToBool($new_instance, 'is_sticky');
		$instance['show_on_phones'] = self::instanceVarToBool($new_instance, 'show_on_phones');
		$instance['show_on_tablets'] = self::instanceVarToBool($new_instance, 'show_on_tablets');
		$instance['show_on_large_screens'] = self::instanceVarToBool($new_instance, 'show_on_large_screens');
		$instance['not_extended_show_steps'] = self::instanceVarToBool($new_instance, 'not_extended_show_steps');
		$instance['extended_show_steps'] = self::instanceVarToBool($new_instance, 'extended_show_steps');
		$instance['extended_show_pictures'] = self::instanceVarToBool($new_instance, 'extended_show_pictures');

		$instance = $this->checkInputs($instance);

		return $instance;
	}

	private function checkInputs($instance)
	{
		if (strlen($instance['title']) > 40) {
			$instance['title'] = '';
		}
		if (!TraveledMap_Utils::isHeightValueValid($instance['map_height'])) {
			$instance['map_height'] = '';
		}
		$instance['margin_top'] = intval($instance['margin_top']);
		$instance['margin_right'] = intval($instance['margin_right']);
		$instance['margin_bottom'] = intval($instance['margin_bottom']);
		$instance['margin_left'] = intval($instance['margin_left']);
		return $instance;
	}

	private static function instanceVarToChecked($instance, $varName)
	{
		return self::instanceVarToBool($instance, $varName) ? 'checked' : '';
	}

	private static function instanceVarToBool($instance, $varName)
	{
		return isset($instance[$varName]) && (($instance[$varName]) || $instance[$varName] === 1);
	}
}
