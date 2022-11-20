<?php
include_once 'util/ArcgisUtil.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('HTTP/1.1 200 OK');

$dest = $_REQUEST['dest'];
switch ($dest) {
    case 'coordinate':
        if (isset($_REQUEST['distance']) && ! empty($_REQUEST['distance']) && isset($_REQUEST['angle']) && ! empty($_REQUEST['angle'])) {
            echo json_encode(ArcgisUtil::destinationPoint($_REQUEST['longitude'], $_REQUEST['latitude'], $_REQUEST['distance'], $_REQUEST['angle']));
        } else {
            echo json_encode(array(
                $_REQUEST['longitude'],
                $_REQUEST['latitude']
            ));
        }
        break;
    case 'json':
        $old_json = ArcgisUtil::createPointJsonData(array(
            $_REQUEST['longitude'],
            $_REQUEST['latitude']
        ));
        $result = array(
            $old_json
        );
        if (isset($_REQUEST['distance']) && ! empty($_REQUEST['distance']) && isset($_REQUEST['angle']) && ! empty($_REQUEST['angle'])) {
            $newPoint = ArcgisUtil::destinationPoint($_REQUEST['longitude'], $_REQUEST['latitude'], $_REQUEST['distance'], $_REQUEST['angle']);
            $new_json = ArcgisUtil::createPointJsonData($newPoint, ArcgisUtil::hexToRGB("#00ff00"));
            array_push($result, $new_json);
        }

        echo json_encode($result);
        break;
    default:
        header('HTTP/1.1 404 Not Found');
}