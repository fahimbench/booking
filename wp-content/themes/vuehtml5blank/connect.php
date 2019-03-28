<?php 
session_start();
require_once('../../../wp-load.php');
if (!empty($_SESSION["user_id"])) {
    header("location: http://" . $_SERVER['HTTP_HOST']);
}


$client_id;
$client_secret;

$query = new WP_Query([
    "post_type" => "slack"
]);

if ($query->have_posts()) {
    $query->the_post();
    $client_id = get_field("client_id");
    $client_secret = get_field("client_secret");
}

$code = $_GET['code'];

//Premiere réponse pour l'obtention de son token, user and team
$response = file_get_contents("https://slack.com/api/oauth.access?client_id={$client_id}&client_secret={$client_secret}&code={$code}");
//on decode
$response = json_decode($response);

//et la une requete test pour récupéré sur l'api users.identity nos propres infos
//TOKEN
$token = $response->access_token;

//On fout le token dans un tableau
$data = [
    "token" => $token
];
//on encode
$data = json_encode($data);
//on crée le context avec un content type application/x-www-form-urlencoded pour un envoie avec l'url
$context = stream_context_create(array(
    "http" => array(
        "method" => "POST",
        "header" => "Content-Type: application/x-www-form-urlencoded;\r\n"
    )
));

// Envoie la requête
$response = file_get_contents("https://slack.com/api/users.identity?token={$token}", false, $context);
//on decode la reponse
$response = json_decode($response);
//on affiche la reponse
//var_dump($response);

if (email_exists($response->user->email)) {
    $user = get_user_by("email", $response->user->email);
    $_SESSION["user_id"] = $response->user->id;
    $_SESSION["user_pic"] = $response->user->image_72;
    $_SESSION["user_name"] = $response->user->name;

    // var_dump($_SESSION["user_pic"]);
    // exit;
    header("location: http://" . $_SERVER['HTTP_HOST']);
} else {
    echo "Le système ne vous reconnait pas, veuillez contacter un administrateur";
}
 