<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'bookingihm');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'fahim');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'arigato@@..');

/** Adresse de l’hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ' :v2L3|N0JwsH?|R&]a4|eq#tPP#5&5jfTST3#dKRaPC.[>gG%KD$=/Tve7U1mg[');
define('SECURE_AUTH_KEY',  ':JQ0cvW:S:3vRwUvLquhlj~PQBKQduNt(h~q?M}L~LD1M]X@`(T(Vewc} Vux2ax');
define('LOGGED_IN_KEY',    'K^9]?$!&;GFrYII3tdj-}WIDx7bk[Nd:8ZOi`c1GY87M:v+>$DkcDFy}``,&o>Xr');
define('NONCE_KEY',        '3xlUY-NgW;SGe]JD]$K(iu@Kp Q;WY)!jG7)[13 (wKA102WO>(KJ|5d;RE^KIz]');
define('AUTH_SALT',        'n2O5R%ku-@&=~U){L|4y56ZPn8A|1d^HGH]yAA|vna#YQ}}$#9[zCDRWzg{U:0>M');
define('SECURE_AUTH_SALT', 'a>~zqUHAm2_(?@I>1D_vl8R`B5Bi<65t1~QLQt0/%a0>Gw3jW~bwTYKl`/]/)27m');
define('LOGGED_IN_SALT',   '3`-tH1*(R#d?7I&O6Jc6sM4qWKzvGr?jw(Ce*-yie~82b,@OP8@_O6lulBom|2.u');
define('NONCE_SALT',       '$)cLhs6F=jbv+YRIRx6>C@bV$Oy{-:s[;>2FK[LUl?El5jg/)udy-ftq!M^%Lu=:');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix  = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');