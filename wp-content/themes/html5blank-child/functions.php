<?php

// PERMET AU TEMPLATE CHILD D'AVOIR LA PRIORITE SUR LE PARENT
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles', 11 );

// PERMET D'IMPORTER SON CSS PROPREMENT SUR WORDPRESS
// IL VERIFIE SI IL A BIEN CHARGE LE CSS
// CAUSE DES PROBLEMES SINON
function add_script_and_style_to_head() {
    echo "<script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\"></script>";
    echo "<script src=\"https://cdn.jsdelivr.net/npm/vue/dist/vue.js\"></script>";

}
add_action( 'wp_head', 'add_script_and_style_to_head' );

function add_script_to_footer() {
    echo "<script src=\"" . get_stylesheet_directory_uri() . "/scripts/app.js\"></script>";
}
add_action( 'wp_head', 'add_script_to_footer' );

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