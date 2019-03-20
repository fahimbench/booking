<?php 
session_start();

$client_id;
$client_secret;
$token;
$api_token;
$url = "http://".$_SERVER['HTTP_HOST']."/wp-content/themes/vuehtml5blank/";
$query = new WP_Query([
    "post_type" => "slack"
]);

//Récupére nos données depuis le back wp
if ($query->have_posts()) {
    $query->the_post();
    $client_id = get_field("client_id");
    $client_secret = get_field("client_secret");
    $token = get_field("token");
    $api_token = get_field("private_api_token");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Booking - IOT team</title>
    <script src="https://unpkg.com/rxjs/bundles/rxjs.umd.js"></script>
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script id="data" type="application/json">{"url": "<?php echo $url ?>"}</script>
    <?php wp_head(); ?>
</head>

<body>
    <div class="container" id="app">
        <!-- PARTIE A MODIFIER -- LOGIN -->
        <?php if (empty($_SESSION["user_id"])) { ?>
        <div id="login-slack">
            <div class="left">
                <div class="logo-iot"></div>
                <div class="login-desc">
                    <div class="desc-title">Bienvenue sur l'espace de gestion du système de booking !</div>
                    <div class="desc-subtitle">Surveillez les réservations des salles de réunions de votre écosystème depuis cette espace de gestion. Connectez vous d'un simple clic !</div>
                </div>
                <div class="logo-nanolike"></div>
            </div>
            <div class="right">
                
                <a href="https://slack.com/oauth/authorize?scope=identity.basic,identity.email,identity.avatar&client_id=<?php echo $client_id; ?>&redirect_uri=http://<?php echo $_SERVER['HTTP_HOST']; ?>/connection">
                    <img src="https://api.slack.com/img/sign_in_with_slack.png" />
                </a>
            </div>
        </div>
        <!-- FIN PARTIE A MODIFIER -- LOGIN -->
        <?php 
    } else { ?>

        <!-- PARTIE A MODIFIER -- APP -->

        <input type="text" v-model="search" placeholder="Search title.." />
        <router-link to="/users">users</router-link>
        <router-link to="/rooms">rooms</router-link>
        <router-link to="/booking">booking</router-link>
        <router-view></router-view>
        <?php 
    } ?>
    </div>

    <!-- FIN DE PARTIE A MODIFIER -- APP -->

    <?php 
    //Si une session existe afficher les scripts depuis la fonction wp_footer
    //nous sommes obligés de le sortir du conteneur
    if (!empty($_SESSION["user_id"])) {
        wp_footer();
    }
    ?>


</body>

</html> 