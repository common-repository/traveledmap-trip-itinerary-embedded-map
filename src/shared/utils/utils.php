<?php

class TraveledMap_Utils {
	public static function varToBool($var)
	{
		return (bool) $var ? 1 : 0;
	}

	public static function isPixelsValueValid($val, $maxVal = 10000) {
		$intVal = intval(str_replace("px", "", $val));
		return strpos($val, "px") !== false && is_int($intVal) && $intVal < $maxVal;
	}

	public static function isPercentValueValid($val) {
		$intVal = intval(str_replace("%", "", $val));
		return strpos($val, "%") !== false && is_int($intVal) && $intVal <= 100;
	}

	public static function isHeightValueValid($val, $maxVal = 10000) {
		return TraveledMap_Utils::isPixelsValueValid($val, $maxVal) || TraveledMap_Utils::isPercentValueValid($val) || is_numeric($val);
	}

	public static function convertHeightFromPercentToVH($height) {
    	$height = trim($height);
    	return str_replace("%", "VH", $height);
    }

    public static function getStepBlockRetrocompatibilityJavascript() {
        return '
          // handle trip step retrocompatibility
          Array.from(document.getElementsByClassName("traveledmap-trip-anchor"))
            .forEach((el) => {
              console.log(el);
              if (!el.classList.contains("traveledmap-trip-step")) {
                el.classList.add("traveledmap-trip-step");
              }
              if (!el.getAttribute("data-step")) {
                el.setAttribute("data-step", el.getAttribute("id"))
              }
            });
        ';
    }
}