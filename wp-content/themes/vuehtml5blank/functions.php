<?php 
// PERMET AU TEMPLATE CHILD D'AVOIR LA PRIORITE SUR LE PARENT
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', "http://".$_SERVER['HTTP_HOST']."/wp-content/themes/vuehtml5blank/style.css" );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles', 11 );


function add_script_to_head(){
    echo "";
}
add_action( 'wp_head', 'add_script_to_head');

function add_script_to_footer(){
    echo "<script src=\"http://".$_SERVER['HTTP_HOST']."/wp-content/themes/vuehtml5blank/app.js\"></script>";
}
add_action( 'wp_footer', 'add_script_to_footer');

// PERMET DE SUPPRIMER LES MENUS DANS LE PANEL ADMIN D'UN USER
// ICI TOUS LES USERS N'AURONT PAS LES MENUS SUIVANT
// IL N'Y A QUE L'ADMIN QUI POURRA LES VOIR
function remove_menus(){
	$user = wp_get_current_user();
    if( $user && isset($user->user_login) && 'admin' != $user->user_login ) {
        remove_menu_page('edit.php');	
        remove_menu_page('upload.php');	
        remove_menu_page('edit-comments.php');	
        remove_menu_page('edit.php?post_type=html5-blank');
		remove_menu_page('tools.php');	
	}
                 
}
add_action( 'admin_menu', 'remove_menus' );
