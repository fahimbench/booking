<?php
session_start();
header("Content-Type: application/json");
require_once('../../../wp-load.php');
if (empty($_SESSION["user_id"]) || empty($_GET["req"])) {
    header("location:http://" . $_SERVER['HTTP_HOST']);
}
$wp_token;
$api_token;
$context;

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
    $response = file_get_contents("https://slack.com/api/users.list?token={$wp_token}", false, $context);
    return $response;
}

function useAPI($url, $api_token, $data = null)
{
    $data = ($data != null) ? json_encode(http_build_query($data)) : null;

    $context = stream_context_create(array(
        "http" => array(
            "method" => "POST",
            "header" => "Content-Type: application/json;charset=utf-8\r\n"
                . "Authorization: Bearer {$api_token}\r\n",
            "content" => $data
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
        echo useAPI('http://vps595572.ovh.net/api/room/?getall', $api_token);
        break;
    case "getAllBookings":
        echo useAPI('http://vps595572.ovh.net/api/booking/?getall', $api_token);
        break;
    case "delete":
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $str = (isset($_GET['type']) && !empty($_GET['type'])) ? $_GET['type'] : null;
            $remplace = [
                'Réservation' => 'booking',
                'Salle' => 'room'
            ];
            if($str !== null && array_key_exists($str, $remplace)){
                $data = [
                    $remplace[$str]."_id" => $_GET['id']
                ];
                echo useAPI('http://vps595572.ovh.net/api/'.$remplace[$str].'/?delete', $api_token, $data);
            } 
        }
        case "add":
            var_dump($_GET);
            exit;
            echo useAPI('http://vps595572.ovh.net/api/booking/?add', $api_token, $data);
        break;
    default:
}
