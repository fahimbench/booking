<?php
session_start();
header("Content-Type: application/json");
require_once('../../../wp-load.php');
if (empty($_SESSION["user_id"]) || empty($_GET["req"])) {
    header("location:http://" . $_SERVER['HTTP_HOST']);
}
$wp_token;
$api_token;

$query = new WP_Query([
    "post_type" => "slack"
]);

if ($query->have_posts()) {
    $query->the_post();
    $wp_token = get_field("access_token");
    $api_token = get_field("private_api_token");
}

function getUsers($wp_token)
{
    $response = file_get_contents("https://slack.com/api/users.list?token={$wp_token}", false);
    return $response;
}
function actionAPI($url, $api_token, $data)
{

    $data = http_build_query($data);
    $context = stream_context_create(array(
        "http" => array(
            "method" => "POST",
            "header" => "Content-Type: application/json;charset=utf-8\r\n"
                . "Authorization: Bearer {$api_token}\r\n",
            "content" => json_encode($data)
        )
    ));


    $response = file_get_contents($url, false, $context);
    return $response;
}
function callAPI($url, $api_token)
{

    $context = stream_context_create(array(
        "http" => array(
            "method" => "POST",
            "header" => "Content-Type: application/json;charset=utf-8\r\n"
                . "Authorization: Bearer {$api_token}\r\n",
            "content" => null
        )
    ));


    $response = file_get_contents($url, false, $context);
    return $response;
}


switch ($_GET['req']) {
    case "getAllUsers":
        echo getUsers($wp_token);
        break;
    case "getAllRooms":
        echo callAPI('http://vps595572.ovh.net/api/room/?getall', $api_token);
        break;
    case "getAllBookings":
        echo callAPI('http://vps595572.ovh.net/api/booking/?getall', $api_token);
        break;
    case "delete":
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $str = (isset($_GET['type']) && !empty($_GET['type'])) ? $_GET['type'] : null;
            $remplace = [
                'Réservation' => 'booking',
                'Salle' => 'room'
            ];
            if ($str !== null && array_key_exists($str, $remplace)) {
                $data = [
                    $remplace[$str] . "_id" => $_GET['id']
                ];
                echo actionAPI('http://vps595572.ovh.net/api/' . $remplace[$str] . '/?delete', $api_token, $data);
            }
        }
        break;
    case "add":
        foreach ($_GET as $key => $value) {
            if (empty($value)) {
                return;
            }
        }
        $days = [
            "Dimanche" => 0,
            "Lundi" => 1,
            "Mardi" => 2,
            "Mercredi" => 3,
            "Jeudi" => 4,
            "Vendredi" => 5,
            "Samedi" => 6
        ];
        $data = [
            "room_name" => $_GET['name'],
            "room_day_start" => $days[$_GET['dayStart']],
            "room_day_end" => $days[$_GET['dayEnd']],
            "room_hr_start" => "1990-01-01 20:00:00",
            "room_hr_end" => "1990-01-01 23:00:00",
            "room_building" => $_GET['building'] + 1
        ];
        echo actionAPI('http://vps595572.ovh.net/api/room/?add', $api_token, $data);
        break;
    default:
}
