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

function getWithPrivateAPI($url, $api_token)
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

switch ($_GET["req"]) {
    case "getAllUsers":
        echo getUsers($wp_token);
        break;
    case "getAllRooms":
        echo getWithPrivateAPI("http://vps595572.ovh.net/api/room/?getall", $api_token);
        break;
    case "getAllBookings":
        echo getWithPrivateAPI("http://vps595572.ovh.net/api/booking/?getall", $api_token);
        break;
    default:
}
