<?php
/**
 * Description: Collecte le contenu d'un fichier au format GeoJSON d'échange de données
 *
 * @param {string} $_REQUEST['url'] l'url du flux à analyser
 * @param {string} $_REQUEST['nonce'] le nonce à tester
 * @return {Object[]} Le contenu du fichier GeoJSON distant
 * @since 1.8.8
 * @since 1.9.6 Gesion plus fine des erreurs 'file_get_contents'
 * @since 2.1.1 Teste de la fonctionnalité de lecture des fichiers GeoJSON
 */

namespace EACCustomWidgets\Proxy;

$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once $parse_uri[0] . 'wp-load.php';

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Config_Elements;

if ( ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'eac_file_osm_nonce_' . sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) ) ) {
	header( 'Content-Type: text/plain' );
	echo esc_html__( 'Jeton invalide. Actualiser la page courante...', 'eac-components' );
	exit;
}

if ( ! ini_get( 'allow_url_fopen' ) || ! isset( $_REQUEST['url'] ) ) {
	header( 'Content-Type: text/plain' );
	echo esc_html__( '"allow_url_fopen" est désactivé', 'eac-components' );
	exit;
}

/** @since 2.1.1 Fonctionnalité active */
/*if ( ! Eac_Config_Elements::is_feature_active( 'unfiltered-medias' ) ) {
	header( 'Content-Type: text/plain' );
	echo esc_html__( 'Activer la fonctionnalité "Télécharger les fichiers non filtrés" pour lire un flux JSON', 'eac-components' );
	exit;
}*/

$file = filter_var( urldecode( $_REQUEST['url'] ), FILTER_SANITIZE_URL );

$ctx = stream_context_create( array( 'http' => array( 'timeout' => 15 ) ) );

$file_source = file_get_contents( $file, false, $ctx );

if ( false === $file_source || empty( $file_source ) ) {
	header( 'Content-Type: text/plain' );
	$error_last = error_get_last();

	if ( preg_match( '/(404)/', $error_last['message'] ) ) {
		preg_match( '/\(([^\)]+)\)/', $error_last['message'], $match );
		echo '"' . filter_var( urldecode( $match[1] ), FILTER_SANITIZE_URL ) . '" => ' . esc_html__( "La page demandée n'existe pas.", 'eac-components' );
	} elseif ( preg_match( '/(403)/', $error_last['message'] ) ) {
		preg_match( '/\(([^\)]+)\)/', $error_last['message'], $match );
		echo '"' . filter_var( urldecode( $match[1] ), FILTER_SANITIZE_URL ) . '" => ' . esc_html__( 'Accès refusé.', 'eac-components' );
	} elseif ( preg_match( '/(401)/', $error_last['message'] ) ) {
		preg_match( '/\(([^\)]+)\)/', $error_last['message'], $match );
		echo '"' . filter_var( urldecode( $match[1] ), FILTER_SANITIZE_URL ) . '" => ' . esc_html__( 'Non autorisé.', 'eac-components' );
	} elseif ( preg_match( '/(503)/', $error_last['message'] ) ) {
		preg_match( '/\(([^\)]+)\)/', $error_last['message'], $match );
		echo '"' . filter_var( urldecode( $match[1] ), FILTER_SANITIZE_URL ) . '" => ' . esc_html__( 'Service indisponible. Réessayer plus tard.', 'eac-components' );
	} elseif ( preg_match( '/(405)/', $error_last['message'] ) ) {
		preg_match( '/\(([^\)]+)\)/', $error_last['message'], $match );
		echo '"' . filter_var( urldecode( $match[1] ), FILTER_SANITIZE_URL ) . '" => ' . esc_html__( 'Méthode non autorisée.', 'eac-components' );
	} elseif ( preg_match( '/(429)/', $error_last['message'] ) ) {
		preg_match( '/\(([^\)]+)\)/', $error_last['message'], $match );
		echo '"' . filter_var( urldecode( $match[1] ), FILTER_SANITIZE_URL ) . '" => ' . esc_html__( 'Trop de requêtes.', 'eac-components' );
	} elseif ( preg_match( '/(495)/', $error_last['message'] ) ) {
		preg_match( '/\(([^\)]+)\)/', $error_last['message'], $match );
		echo '"' . filter_var( urldecode( $match[1] ), FILTER_SANITIZE_URL ) . '" => ' . esc_html__( 'Certificat SSL invalide.', 'eac-components' );// SSL Certificate Error
	} elseif ( preg_match( '/(496)/', $error_last['message'] ) ) {
		preg_match( '/\(([^\)]+)\)/', $error_last['message'], $match );
		echo '"' . filter_var( urldecode( $match[1] ), FILTER_SANITIZE_URL ) . '" => ' . esc_html__( 'Certificat SSL requis.', 'eac-components' );// SSL Certificate Required
	} elseif ( preg_match( '/(500)/', $error_last['message'] ) ) {
		preg_match( '/\(([^\)]+)\)/', $error_last['message'], $match );
		echo '"' . filter_var( urldecode( $match[1] ), FILTER_SANITIZE_URL ) . '" => ' . esc_html__( 'Erreur Interne du Serveur.', 'eac-components' );
	} else {
		echo esc_html__( 'HTTP: La requête a échoué.', 'eac-components' );
	}

	error_clear_last();
	exit;
}

header( 'Content-Type: application/json' );
echo $file_source; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
