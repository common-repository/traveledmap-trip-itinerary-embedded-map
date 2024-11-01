## Develop
The main thing is to change the variables in the `src/contants/env.php` file
Then `cd src/blocks` and `yarn start`

## Deploy
 - `cd src/blocks && yarn build`
 - `git add .`
 - `git push`
 - Go to the cloned svn repository (see instructions below if the svn repository isn't cloned yet)
 - Go inside the svn/trunk folder
 - `svn add .` and `svn revert trunk/.git --depth infinity`
 - `svn commit`
 - `svn copy https://plugins.svn.wordpress.org/traveledmap-trip-itinerary-embedded-map/trunk https://plugins.svn.wordpress.org/traveledmap-trip-itinerary-embedded-map/tags/1.1.0 -m "message...."` (creates the tag)

If the svn repository has to be installed:
 - `svn checkout https://plugins.svn.wordpress.org/traveledmap-trip-itinerary-embedded-map`
 - `cd traveledmap-trip-itinerary-embedded-map && rm -rf trunk`
 - `git clone git@gitlab.com:qlerebours-bs/wp-traveledmap-trip.git trunk` (will clone in the trunk folder)
 - Be careful don't to version .git under svn (you can svn add . --force and then svn revert trunk/.git)
## Description
The plugin has multiple parts

### Metabox
The metabox is the box in the right column of the post / page editor that allows to save
 - `traveledmap_user_id`: The user id of the map to be displayed
 - `traveledmap_trip_id`: The trip id to be displayed
 - `traveledmap_disable_widget`: If the widget is disabled on this page / post
 - `traveledmap_trip_base_url` (deprecated): The base URL of the map, containing the marker customization param. It shouldn't be used anymore
 - `traveledmap_show_markers_customization`: A param to say if the widget or trip block should use markers customization instead of the step numbers
 - `traveledmap_trip_steps`: The trip steps saved as an JSON string, representing an object { "hash": "name" }

### Trip block
It shows the block that embeds the trip map.
It saves:
 - `mapUrl` (string): Deprecated - A string containing the base map url, using function `getMapLinkFromBaseUrl`
 - `showPopup` (boolean): if the popup should be visible (whatever the status, extended, sticky or normal)
 - `showPictures` (boolean): if the pictures should be displayed (whatever the status, extended, sticky or normal)
 - `showPicturesAtStart` (boolean): if the picture panel should be opened at start
 - `isSticky` (boolean): if the map has to be sticky
 - `showOnPhones` (boolean): if the map should be visible on phones
 - `showOnTablets` (boolean): if the map should be visible on tablets
 - `showOnLargeScreens` (boolean): if the map should be visible on large screens
 - `mapHeight`: The height of the map when it's not sticky, not extended. Height can be in percent or in px
 - `standardMapHeight`: The height of the map when it's sticky. Height can be in percent or in px
 - `extendedMapHeight`: The height of the map when it's extended (stretched). Height can be in percent or in px
 - `marginTop`: The margin top when the map is sticky

#### Mapping between SDK options and data stored
If you need to understand the purpose of an option, or a database field, just check the SDK documentation.

| SDK option | Database field | Comment |
| ------ | ------ | ------ |
| userId | traveledmap_user_id | - |
| tripId | traveledmap_trip_id | - |
| isDisabled | false | - |
| isSticky | isSticky | - |
| isExtendable | false | Only for widget |
| isStretchable | true | Only for block |
| mapHeight | mapHeight | - |
| stickyMapHeight | standardMapHeight | - |
| stretchedMapHeight | extendedMapHeight | - |
| stickyMarginTop | marginTop | - |
| shouldExtendToTop | - | Not applicable |
| shouldExtendToRight | - | Not applicable |
| shouldExtendToBottom | - | Not applicable |
| shouldExtendToLeft | - | Not applicable |
| marginTop | - | Not applicable |
| marginRight | - | Not applicable |
| marginBottom | - | Not applicable |
| marginLeft | - | Not applicable |
| shouldShowMarkersCustomization | traveledmap_show_markers_customization | - |
| shouldShowPictures | showPictures | - |
| shouldShowPicturesAtStart | showPicturesAtStart | - |
| shouldShowOnPhones | showOnPhones | - |
| shouldShowOnTablets | showOnTablets | - |
| shouldShowOnLargeScreens | showOnLargeScreens | - |
| shouldShowSteps | showPopup | - |
| shouldShowStepsWhenExtended | - | Not applicable |
| shouldShowStepsWhenStretched | showPopup | - TODO later, add this option |
| shouldShowPicturesWhenExtended | - | Not applicable |
| shouldShowPicturesWhenStretched | showPictures | - TODO later, add this option |
| isOverContent | isSticky | - |
| shouldScrollToStepContentWhenClicked | true | - |

### Trip widget
The widget can be shown in a column and can be extended. It saves:
 - `title` (boolean): The title of the widget
 - `margin_top` (boolean): The top margin between the map and the window when it's extended
 - `margin_right` (boolean): The right margin between the map and the window when it's extended
 - `margin_bottom` (boolean): The bottom margin between the map and the window when it's extended
 - `margin_left` (boolean): The left margin between the map and the window when it's extended
 - `map_height` (boolean): The map's height when it's not extended
 - `extandable` (boolean): If the map can be extended or not
 - `extended_top` (boolean): If it will extend to top
 - `extended_right` (boolean): If it will extend to right
 - `extended_bottom` (boolean): If it will extend to botom
 - `extended_left` (boolean): If it will extend to left
 - `is_sticky` (boolean): If it's sticky
 - `show_on_phones` (boolean): If it will be displayed on phones
 - `show_on_tablets` (boolean): If it will be displayed on tablets
 - `show_on_large_screens` (boolean): If it will be displayed on large screens
 - `not_extended_show_steps` (boolean): If the steps should be displayed when the map isn't extended
 - `extended_show_steps` (boolean): If the steps should be visible when the map is extended
 - `extended_show_pictures` (boolean): If the pictures should be visible when the map is extended

#### Mapping between SDK options and data stored
If you need to understand the purpose of an option, or a database field, just check the SDK documentation.

| SDK option | Database field | Comment |
| ------ | ------ | ------ |
| userId | traveledmap_user_id | - |
| tripId | traveledmap_trip_id | - |
| isDisabled | traveledmap_disable_widget | - |
| isSticky | is_sticky | - |
| isExtendable | extandable | Only for widget |
| isStretchable | false | Only for block |
| mapHeight | map_height | - |
| stickyMapHeight | map_height | - TODO add the option |
| stretchedMapHeight | - | Not applicable |
| stickyMarginTop | - | Not applicable |
| shouldExtendToTop | extended_top | - |
| shouldExtendToRight | extended_right | - |
| shouldExtendToBottom | extended_bottom | - |
| shouldExtendToLeft | extended_left | - |
| marginTop | margin_top | - |
| marginRight | margin_right | - |
| marginBottom | margin_bottom | - |
| marginLeft | margin_left | - |
| shouldShowMarkersCustomization | traveledmap_show_markers_customization | - |
| shouldShowPictures | false | - |
| shouldShowPicturesAtStart | false | - |
| shouldShowOnPhones | show_on_phones | - |
| shouldShowOnTablets | show_on_tablets | - |
| shouldShowOnLargeScreens | show_on_large_screens | - |
| shouldShowSteps | not_extended_show_steps | - |
| shouldShowStepsWhenExtended | extended_show_steps | - |
| shouldShowStepsWhenStretched | - | Not applicable |
| shouldShowPicturesWhenExtended | extended_show_pictures | - |
| shouldShowPicturesWhenStretched | - | Not applicable |
| isOverContent | false | - |
| shouldScrollToStepContentWhenClicked | true | - |


## Files
 - `plugin.php` contains the plugin data, like the version number
 - `readme.txt` contains the public readme for this plugin
 - `README.md` is the developer's readme
 - `src`
   - `blocks` contains the blocks code within it's sub src folder.
   - `categories` contains the code to create the block category named TraveledMap (to sort them from the editor's block selector)
   - `metabox` contains all the code necessary to display and save data from the metabox
   - `shared` contains some code shared between the blocks, the metabox and the widget
   - `widget` contains the code used to display the widget and save it's data
   - Initialization is done in `init.php`. It includes all the files

