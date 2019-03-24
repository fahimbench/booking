<?php 
session_start();

$client_id;
$client_secret;
$token;
$api_token;
$url = "http://" . $_SERVER['HTTP_HOST'] . "/wp-content/themes/vuehtml5blank/";
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="http://momentjs.com/downloads/moment.js"></script>
    <script id="data" type="application/json">
        {
            "url": "<?php echo $url ?>"
        }
    </script>
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
        <div id="main-container">
            <div class="topbar">
                <div class="search-icon-space">
                    <div class="search-icon"></div>
                </div>
                <div class="search">
                    <input type="text" v-model="search" placeholder="Chercher ..." />
                </div>
                <div class="profil">
                    <div class="picture image--cover" style="background: url('<?php echo $_SESSION['user_pic']; ?>') no-repeat center center;  background-size: 45px 45px;"></div>
                    <div class="profil-hidden">
                        <a class="disconnect" href="/disconnect">Se déconnecter</a>
                    </div>
                </div>
            </div>
            <div class="view">
                <nav>
                    <router-link to="/users" class="menu-icon users"></router-link>
                    <router-link to="/rooms" class="menu-icon rooms"></router-link>
                    <router-link to="/booking" class="menu-icon booking"></router-link>
                    <router-link to="/" class="menu-icon home"></router-link>
                </nav>
                <div class="result">
                    <router-view></router-view>
                </div>
            </div>
        </div>
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
    <script>
        let profil = document.querySelector('.profil');

        document.addEventListener('click', function(e) {

            if (!e.target.classList.contains('picture') && profil.classList.contains('active')) {
                profil.classList.toggle('active');
            }

        });

        profil.addEventListener('click', function() {
            this.classList.toggle('active');
        });

        let modify = function(room) {
            swal({
                title: "Modification d'une salle",
                content: buildForm(room)
            }).then((e) => {

            });
        }

        let buildForm = function(room) {
            let buildings = ["IOT1", "IOT2", "IOT3"];
            let days = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
            let hours = [];
            for (let i = 0; i < 24; i++) {
                for (let j = 0; j < 2; j++) {
                    i2 = (i <= 9) ? "0" + i : i;
                    hours.push(i2 + ":" + (j === 0 ? "00" : 30));
                }
            }
            //creation du conteneur
            let div = document.createElement("div");
            //creation de l'input nom de la salle
            let inputRoomName = document.createElement("input");
            inputRoomName.id = "inputRoomName";
            inputRoomName.type = "text";
            inputRoomName.placeholder = "Nom de la Salle";
            inputRoomName.value = room.name;
            div.appendChild(inputRoomName);

            div.appendChild(selectBuilder("selectDayStart", days, room.dayStart, "days"));
            div.appendChild(selectBuilder("selectDayEnd", days, room.dayEnd, "days"));
            div.appendChild(selectBuilder("selectHrStart", hours, room.hrStart, "hours"));
            div.appendChild(selectBuilder("selectHrEnd", hours, room.hrEnd, "hours"));
            div.appendChild(selectBuilder("selectBuilding", buildings, room.building, "building"));
            return div;
        }

        let selectBuilder = function(id, array, selected, type) {
            let select = document.createElement("select");
            select.id = id;
            for (let i = 0; i < array.length; i++) {
                let option = document.createElement("option");
                option.value = array[i];
                option.text = array[i];
                switch (type) {
                    case "hours":
                        date = moment(selected).locale("fr").format("HH:mm");
                        option.selected = (date == array[i]) ? true : false;
                        break;
                    case "days":
                        option.selected = (selected == i) ? true : false;
                        break;
                    case "building":
                        option.selected = (selected == i + 1) ? true : false;
                        break;
                }
                select.appendChild(option);
            }
            return select;
        }

        let createRow = function() {
            let name = document.querySelector("#inputRoomName").value;
            let dayStart = document.querySelector("#selectDayStart").value;
            let dayEnd = document.querySelector("#selectDayEnd").value;
            let hrStart = document.querySelector("#selectHrStart").value;
            let hrEnd = document.querySelector("#selectHrEnd").value;
            let building = document.querySelector("#selectBuilding").value;

            
        }
    </script>

</body>

</html> 