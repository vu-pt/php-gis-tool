<?php
define("DEFAULT_OUTLINE", array(
    "color" => array(
        255,
        255,
        255
    ),
    "width" => 0.5
));
class ArcgisUtil
{
    public static function createPointJsonData($point, $color = array(227, 139, 79), $outline_color = DEFAULT_OUTLINE)
    {
        return array(
            'type' => "point",
            'longitude' => $point[0],
            'latitude' => $point[1],
            'symbol' => array(
                'type' => 'simple-marker',
                'color' => $color,
                'outline' => $outline_color
            ),
            'popupTemplate' => array(
                'title' => 'Point',
                'content' => 'Coordinate: ({longitude}, {latitude})'
            )
        );
    }
    /**
     *
     * @param number $lng
     * @param number $lat
     * @param number $dist (m)
     * @param number $brng (degree 0..360)
     * @return number[]
     */
    public static function destinationPoint($lng, $lat, $meters, $brng)
    {
        $dist = $meters / 1000; // dist in km
        $rad = 6371; // earths mean radius
        $dist = $dist / $rad; // convert dist to angular distance in radians
        $brng = deg2rad($brng); // conver to radians
        $lat1 = deg2rad($lat);
        $lon1 = deg2rad($lng);
        
        $lat2 = asin(sin($lat1) * cos($dist) + cos($lat1) * sin($dist) * cos($brng));
        $lon2 = $lon1 + atan2(sin($brng) * sin($dist) * cos($lat1), cos($dist) - sin($lat1) * sin($lat2));
        $lon2 = fmod($lon2 + 3 * M_PI, 2 * M_PI) - M_PI; // normalise to -180..+180º
        $lat2 = rad2deg($lat2);
        $lon2 = rad2deg($lon2);
        
        return array(
            $lon2,
            $lat2
        );
    }
    
    public static function hexToRGB($hex)
    {
        list ($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return array(
            $r,
            $g,
            $b
        );
    }
}
