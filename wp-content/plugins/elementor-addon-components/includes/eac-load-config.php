<?php

/*=================================================================================================
* Class: Eac_Config_Elements
*
* Description:	Charge les listes de composants et de features
*				Vérifie et retourne le statut de chaque éléments (actif ou pas)
* 
* @since 1.9.8
=================================================================================================*/

namespace EACCustomWidgets\Includes;

// Exit if accessed directly
if(!defined('ABSPATH')) { exit; }

class Eac_Config_Elements {
	
	/**
	 * @var options_widgets_name
	 */
	private static $options_widgets_name = 'eac_options_settings';
	
	/**
	 * @var options_features_name
	 */
	private static $options_features_name = 'eac_options_features';
	
	/**
	 * @var widgets_list
	 */
	private static $widgets_list = array();
	
	/**
	 * @var widgets_advanced_name
	 */
	private static $widgets_advanced_name = 'all-advanced';
	
	/**
	 * @var widgets_common_name
	 */
	private static $widgets_common_name = 'all-components';
	
	/**
	 * @var widgets_keys
	 */
	private static $widgets_keys = array();
	
	/**
	 * @var widgets_keys_active
	 */
	private static $widgets_keys_active = array();
	
	/**
	 * @var widgets_advanced_keys_active
	 */
	private static $widgets_advanced_keys_active = array();
	
	/**
	 * @var widgets_common_keys_active
	 */
	private static $widgets_common_keys_active = array();
	
	/**
	 * @var features_list
	 */
	private static $features_list = array();
	
	/**
	 * @var features_advanced_name
	 */
	private static $features_advanced_name = 'all-features-advanced';
	
	/**
	 * @var features_common_name
	 */
	private static $features_common_name = 'all-features-common';
	
	/**
	 * @var features_keys
	 */
	private static $features_keys = array();
	
	/**
	 * @var features_keys_active
	 */
	private static $features_keys_active = array();
	
	/**
	 * @var features_advanced_keys_active
	 */
	private static $features_advanced_keys_active = array();
	
	/**
	 * @var features_common_keys_active
	 */
	private static $features_common_keys_active = array();
	
	/**
	 * Constructeur de la class
	 */
	public function __construct() {
		// Charge les liste des widgets et des fonctionnalités
		$this->set_widgets_list();
		$this->set_features_list();
		
		// Construit la liste des widgets actives par défaut
		foreach(self::$widgets_list as $key => $value) {
			self::$widgets_keys[$key] = $value['active'];
		}
		
		// Construit la liste des fonctionnalités actives par défaut
		foreach(self::$features_list as $key => $value) {
			self::$features_keys[$key] = $value['active'];
		}
		
		// Enregistre l'option des widgets si elle n'existe pas
		if(!get_option(self::$options_widgets_name, false)) {
			update_option(self::$options_widgets_name, self::$widgets_keys);
			// Affecte les widgets actives à sa variable
			self::$widgets_keys_active = self::$widgets_keys;
		} else {
			// Met à jour l'option des widgets dans la BDD 
			$this->compare_widgets_option();
		}
		
		$pos_widgets_advanced = array_search(self::$widgets_advanced_name, array_keys(self::$widgets_keys_active));
		$pos_widgets_common = array_search(self::$widgets_common_name, array_keys(self::$widgets_keys_active));
		self::$widgets_advanced_keys_active = array_slice(self::$widgets_keys_active, $pos_widgets_advanced, $pos_widgets_common);
		self::$widgets_common_keys_active = array_slice(self::$widgets_keys_active, $pos_widgets_common);
		
		//error_log("Widgets advanced".json_encode($widgets_advanced_keys_active));
		//error_log("Widgets components".json_encode($widgets_common_keys_active));
		
		// Enregistre l'option des fonctionnalités si elle n'existe pas
		if(!get_option(self::$options_features_name, false)) {
			update_option(self::$options_features_name, self::$features_keys);
			// Affecte les fonctionnalités actives à sa variable
			self::$features_keys_active = self::$features_keys;
		} else {
			// Met à jour l'option des fonctionnalités dans la BDD 
			$this->compare_features_option();
		}
		
		$pos_features_advanced = array_search(self::$features_advanced_name, array_keys(self::$features_keys_active));
		$pos_features_common = array_search(self::$features_common_name, array_keys(self::$features_keys_active));
		self::$features_advanced_keys_active = array_slice(self::$features_keys_active, $pos_features_advanced, $pos_features_common);
		self::$features_common_keys_active = array_slice(self::$features_keys_active, $pos_features_common);
		
		//error_log("Features advanced".json_encode($features_advanced_keys_active));
		//error_log("Features common".json_encode($features_common_keys_active));
	}
	
	/**
	 * compare_widgets_option
	 *
	 * Charge les options des widgets
	 * Ajoute/Supprime les widgets en comparant les options par défaut et celles de la BDD
	 *
	 * @since 1.8.4
	 */
	private function compare_widgets_option() {
		$diff_settings =  array();
		
		// Récupère les options dans la BDD
        $bdd_settings = get_option(self::$options_widgets_name);
		
		// Ajoute les widgets
		if(count(self::$widgets_keys) > count($bdd_settings)) {
			$diff_settings = array_merge($bdd_settings, array_diff_key(self::$widgets_keys, $bdd_settings));
            update_option(self::$options_widgets_name, $diff_settings);
		// Supprime les widgets
		} else if(count(self::$widgets_keys) < count($bdd_settings)) {
			$diff_settings = array_diff_key($bdd_settings, array_diff_key($bdd_settings, self::$widgets_keys));
			update_option(self::$options_widgets_name, $diff_settings);
		}
		
		// Charge les options mises à jour
		self::$widgets_keys_active = get_option(self::$options_widgets_name);
	}
	
	/**
	 * compare_features_option
	 *
	 * Ajoute/Supprime les fonctionnalités en comparant les options par défaut et celles de la BDD
	 * Charge les options des fonctionnalités
	 *
	 * @since 1.8.4
	 */
	private function compare_features_option() {
		$diff_settings =  array();
		
		// Récupère les options dans la BDD
        $bdd_settings = get_option(self::$options_features_name);
		
		// Ajoute les fonctionnalités
		if(count(self::$features_keys) > count($bdd_settings)) {
			$diff_settings = array_merge($bdd_settings, array_diff_key(self::$features_keys, $bdd_settings));
            update_option(self::$options_features_name, $diff_settings);
		// Supprime les fonctionnalités 
		} else if(count(self::$features_keys) < count($bdd_settings)) {
			$diff_settings = array_diff_key($bdd_settings, array_diff_key($bdd_settings, self::$features_keys));
			update_option(self::$options_features_name, $diff_settings);
		}
		
		// Charge les options mises à jour
		self::$features_keys_active = get_option(self::$options_features_name);
	}
	
	/**
	 * get_widgets_option_name
	 *
	 * @return String Le libellé de l'option des widgets
	 */
	public static function get_widgets_option_name() {
		return self::$options_widgets_name;
	}
	
	/**
	 * get_features_option_name
	 *
	 * @return String Le libellé de l'option des fonctionnalités
	 */
	public static function get_features_option_name() {
		return self::$options_features_name;
	}
	
	/**
	 * get_widgets_active
	 *
	 * @return Array La liste des composants et leur statut
	 */
	public static function get_widgets_active() {
		return self::$widgets_keys_active;
	}
	
	/**
	 * get_widgets_advanced_active
	 *
	 * @return Array La liste des composants et leur statut
	 */
	public static function get_widgets_advanced_active() {
		return self::$widgets_advanced_keys_active;
	}
	
	/**
	 * get_widgets_common_active
	 *
	 * @return Array La liste des composants communs et leur statut
	 */
	public static function get_widgets_common_active() {
		return self::$widgets_common_keys_active;
	}
	
	/**
	 * get_features_active
	 *
	 * @return Array La liste des fonctionnalités et leur statut
	 */
	public static function get_features_active() {
		return self::$features_keys_active;
	}
	
	/**
	 * get_features_advanced_active
	 *
	 * @return Array La liste des fonctionnalités et leur statut
	 */
	public static function get_features_advanced_active() {
		return self::$features_advanced_keys_active;
	}
	
	/**
	 * get_features_common_active
	 *
	 * @return Array La liste des fonctionnalités et leur statut
	 */
	public static function get_features_common_active() {
		return self::$features_common_keys_active;
	}
	
	/**
	 * is_widget_active
	 *
	 * @param $element (String) le composant à checker
	 * @return Bool Composant actif
	 */
	public static function is_widget_active($element) {
		$active = false;
		
		// La clé est enregistrée dans la table des options
		if(array_key_exists($element, self::$widgets_keys_active)) {
			
			// Check les class dépendantes
			if(!empty(self::$widgets_list[$element]['class_depends'])) {
				foreach(self::$widgets_list[$element]['class_depends'] as $class) {
					if(!class_exists($class)) {
						return $active;
					}
				}
				
			}
			
			// Check les fonctions dépendantes
			if(!empty(self::$widgets_list[$element]['func_depends'])) {
				foreach(self::$widgets_list[$element]['func_depends'] as $func) {
					if(!function_exists($func)) {
						return $active;
					}
				}
				
			}
			
			// Le booléen de l'élément stocké dans la table des options
			$active = self::$widgets_keys_active[$element];
		}
		
		return $active;
	}
	
	/**
	 * is_feature_active
	 *
	 * @param $element (String) la fonctionnalité à checker
	 * @return Bool Feature actif
	 */
	public static function is_feature_active($element) {
		$active = false;
		
		// La clé est enregistrée dans la table des options
		if(array_key_exists($element, self::$features_keys_active)) {
			
			// Check les class dépendantes
			if(!empty(self::$features_list[$element]['class_depends'])) {
				foreach(self::$features_list[$element]['class_depends'] as $class) {
					if(!class_exists($class)) {
						return $active;
					}
				}
				
			}
			
			// Check les fonctions dépendantes
			if(!empty(self::$features_list[$element]['func_depends'])) {
				foreach(self::$features_list[$element]['func_depends'] as $func) {
					if(!function_exists($func)) {
						return $active;
					}
				}
				
			}
			
			// Le booléen du feature stocké dans la table des options
			$active = self::$features_keys_active[$element];
		}
		
		return $active;
	}
	
	/**
	 * get_widget_path
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le chemin absolu du fichier PHP des composants
	 */
	public static function get_widget_path($element) {
		$path = false;
		
		if(array_key_exists($element, self::$widgets_keys_active)) {
			$fullPath = self::$widgets_list[$element]['file_path'];
			if(!empty($fullPath) && file_exists($fullPath)) {
				return $fullPath;
			}
		}
		return $path;
	}
	
	/**
	 * get_feature_path
	 *
	 * @param $element (String) la fonctionnnalité à checker
	 * @return String Le chemin absolu du fichier PHP des fonctionnalités
	 */
	public static function get_feature_path($element) {
		$path = false;
		
		if(array_key_exists($element, self::$features_keys_active)) {
			$fullPath = self::$features_list[$element]['file_path'];
			if(!empty($fullPath) && file_exists($fullPath)) {
				return $fullPath;
			}
		}
		return $path;
	}
	
	/**
	 * get_widget_namespace
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le NAMESPACE du composant
	 */
	public static function get_widget_namespace($element) {
		$fullClassName = '';
		
		if(array_key_exists($element, self::$widgets_keys_active)) {
			$fullClassName = self::$widgets_list[$element]['name_space'];
			if(!empty($fullClassName) && class_exists($fullClassName)) {
				return $fullClassName;
			}
		}
		return $fullClassName;
	}
	
	/**
	 * get_widget_object
	 *
	 * @param $element (String) le composant à checker
	 * @return Object Les propriétés de l'élément
	 */
	public static function get_widget_object($element) {
		$obj = null;
		
		if(array_key_exists($element, self::$widgets_keys_active)) {
			$obj = self::$widgets_list[$element];
		}
		return $obj;
	}
	
	/**
	 * get_widget_name
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le nom du composant unique
	 */
	public static function get_widget_name($element) {
		$name = '';
		
		if(array_key_exists($element, self::$widgets_keys_active)) {
			$name = self::$widgets_list[$element]['name'];
		}
		return $name;
	}
	
	/**
	 * get_widget_title
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le titre du composant traduit dans la locale
	 */
	public static function get_widget_title($element) {
		$title = '';
		
		if(array_key_exists($element, self::$widgets_keys_active)) {
			$title = sprintf('%s', esc_html__(self::$widgets_list[$element]['title'], 'eac-components'));
		}
		return $title;
	}
	
	/**
	 * get_widget_icon
	 *
	 * @param $element (String) le composant à checker
	 * @return String L'icone du widget
	 */
	public static function get_widget_icon($element) {
		$icon = '';
		
		if(array_key_exists($element, self::$widgets_keys_active)) {
			$icon = self::$widgets_list[$element]['icon'];
		}
		return $icon;
	}
	
	/**
	 * get_widget_keywords
	 *
	 * @param $element (String) le composant à checker
	 * @return Array La liste des mots-clés du widget
	 */
	public static function get_widget_keywords($element) {
		$keywords = array();
		
		if(array_key_exists($element, self::$widgets_keys_active)) {
			$keywords = self::$widgets_list[$element]['keywords'];
		}
		return $keywords;
	}
	
	/**
	 * get_widget_help_url
	 *
	 * @param $element (String) le composant à checker
	 * @return String L'URL de l'aide en ligne du widget
	 */
	public static function get_widget_help_url($element) {
		$help = '';
		
		if(array_key_exists($element, self::$widgets_keys_active)) {
			$help = self::$widgets_list[$element]['help_url'];
		}
		return esc_url($help);
	}
	
	/**
	 * get_feature_object
	 *
	 * @param $element (String) le composant à checker
	 * @return Object Les propriétés de la fonctionnalité
	 */
	public static function get_feature_object($element) {
		$obj = null;
		
		if(array_key_exists($element, self::$features_keys_active)) {
			$obj = self::$features_list[$element];
		}
		return $obj;
	}
	
	/**
	 * get_feature_title
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le titre de la fonctionnalité traduite dans la locale
	 */
	public static function get_feature_title($element) {
		$title = '';
		
		if(array_key_exists($element, self::$features_keys_active)) {
			$title = sprintf('%s', esc_html__(self::$features_list[$element]['title'], 'eac-components'));
		}
		return $title;
	}
	
	/**
	 * set_widgets_list
	 *
	 * On peut ajouter/supprimer
	 * NE JAMAIS CHANGER LE NOM DES CLÉS
	 */
	public function set_widgets_list() {
		
		self::$widgets_list = array(
			'all-advanced'	=> array(
				'active'	=> true,
				'title'		=> 'Active/Désactive tous les composants',
				'keywords'	=> array(),
				'icon'		=> '',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => '',
				'name_space' => '',
			),
			'acf-relationship'	=> array(
				'active'	=> true,
				'title'		=> 'ACF relationship',
				'keywords'	=> array(),
				'icon'		=> '',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array('acf_get_field_groups'),
				'help_url' => "https://elementor-addon-components.com/how-to-display-acf-relationship-posts-in-a-grid/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'acf-relationship.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Acf_Relationship_Widget',
			),
			'author-infobox'	=> array(
				'active'	=> true,
				'title'		=> 'Boîte auteur',
				'keywords'	=> array(),
				'icon'		=> 'eicon-person eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/author-info-box/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'author-infobox.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Author_Infobox_Widget',
			),
			'chart'	=> array(
				'active'	=> true,
				'title'		=> 'Diagrammes',
				'keywords'	=> array(),
				'icon'		=> 'eicon-dashboard eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_WIDGETS_PATH . 'chart.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Chart_Widget',
			),
			'html-sitemap'	=> array(
				'active'	=> true,
				'title'		=> 'HTML sitemap',
				'keywords'	=> array(),
				'icon'		=> 'eicon-sitemap eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/how-do-i-make-a-html-sitemap-with-elementor/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'html-sitemap.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Html_Sitemap_Widget',
			),
			'image-galerie'	=> array(
				'active'	=> true,
				'title'		=> "Galerie d'images",
				'keywords'	=> array(),
				'icon'		=> 'eicon-gallery-masonry eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/create-amazing-image-gallery-using-elementor/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'image-gallery.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Image_Galerie_Widget',
			),
			'image-hotspots'	=> array(
				'active'	=> true,
				'title'		=> 'Image réactive',
				'keywords'	=> array(),
				'icon'		=> 'eicon-hotspot eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/image-hotspots/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'image-hotspots.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Image_Hotspots_Widget',
			),
			'lottie-animations'	=> array(
				'active'	=> true,
				'title'		=> 'Lottie animation',
				'keywords'	=> array(),
				'icon'		=> 'eicon-lottie eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/lottie-animation/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'lottie-animations.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Lottie_Animations_Widget',
			),
			'modal-box'	=> array(
				'active'	=> true,
				'title'		=> 'Boîte modale',
				'keywords'	=> array(),
				'icon'		=> 'eicon-lightbox eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/elementor-modal-box-doc/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'modal-box.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Modal_Box_Widget',
			),
			'news-ticker'	=> array(
				'active'	=> true,
				'title'		=> "Fil d'actualité",
				'keywords'	=> array(),
				'icon'		=> 'eicon-posts-ticker eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/news-ticker/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'news-ticker.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'News_Ticker_Widget',
			),
			'off-canvas'	=> array(
				'active'	=> true,
				'title'		=> 'Barre latérale',
				'keywords'	=> array(),
				'icon'		=> 'eicon-sidebar eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/off-canvas-menu/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'off-canvas.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Off_Canvas_Widget',
			),
			'open-streetmap'	=> array(
				'active'	=> true,
				'title'		=> 'OpenStreetMap',
				'keywords'	=> array(),
				'icon'		=> 'eicon-google-maps eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/openstreetmap/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'open-streetmap.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Open_Streetmap_Widget',
			),
			'pdf-viewer'	=> array(
				'active'	=> true,
				'title'		=> 'Visionneuse PDF',
				'keywords'	=> array(),
				'icon'		=> 'far fa-file-pdf eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/add-a-pdf-viewer-to-elementor/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'pdf-viewer.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Simple_PDF_Viewer_Widget',
			),
			'articles-liste'	=> array(
				'active'	=> true,
				'title'		=> "Grille d'articles",
				'keywords'	=> array(),
				'icon'		=> 'eicon-posts-masonry eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/how-to-create-advanced-queries-for-the-component-post-grid/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'post-grid.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Articles_Liste_Widget',
			),
			'lecteur-rss'	=> array(
				'active'	=> true,
				'title'		=> 'Lecteur RSS',
				'keywords'	=> array(),
				'icon'		=> 'eicon-alert eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/rss-feed/#display-and-share-your-favorite-rss-feeds/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'rss-reader.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Lecteur_Rss_Widget',
			),
			'syntax-highlight'	=> array(
				'active'	=> true,
				'title'		=> 'Coloration syntaxique',
				'keywords'	=> array(),
				'icon'		=> 'eicon-code eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/syntax-highlighter/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'syntax-highlighter.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Syntax_Highlighter_Widget',
			),
			'table-content'	=> array(
				'active'	=> true,
				'title'		=> 'Table des matières',
				'keywords'	=> array(),
				'icon'		=> 'eicon-table-of-contents eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/create-and-display-the-table-of-contents-of-your-posts/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'table-of-contents.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Table_Of_Contents_Widget',
			),
			'team-members'	=> array(
				'active'	=> true,
				'title'		=> "Membres de l'équipe",
				'keywords'	=> array(),
				'icon'		=> 'eicon-person eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/team-members/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'team-members.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Team_Members_Widget',
			),
			'woo-product-grid'	=> array(
				'active'	=> false,
				'title'		=> 'WC Grille de produits',
				'keywords'	=> array('product', 'query', 'filter', 'category', 'tag'),
				'icon'		=> 'eicon-woocommerce eac-icon-elements',
				'name'		=> 'eac-addon-product-grid',
				'class_depends'	=> array('WooCommerce'),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/woocommerce-product-grid-for-elementor/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'wc-product-grid.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'WC_Product_Grid_Widget',
			),
			'all-components'	=> array(
				'active'	=> true,
				'title'		=> 'Active/Désactive tous les composants',
				'keywords'	=> array(),
				'icon'		=> '',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => '',
				'name_space' => '',
			),
			'image-diaporama'	=> array(
				'active'	=> true,
				'title'		=> "Diaporama d'arrière-plan",
				'keywords'	=> array(),
				'icon'		=> 'eicon-slideshow eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_WIDGETS_PATH . 'image-diaporama.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Image_Diaporama_Widget',
			),
			'images-comparison'	=> array(
				'active'	=> true,
				'title'		=> "Comparaison d'images",
				'keywords'	=> array(),
				'icon'		=> 'eicon-image-before-after eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_WIDGETS_PATH . 'images-comparison.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Images_Comparison_Widget',
			),
			'image-effects'	=> array(
				'active'	=> true,
				'title'		=> "Effets d'images",
				'keywords'	=> array(),
				'icon'		=> 'eicon-image-rollover eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_WIDGETS_PATH . 'image-effects.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Image_Effects_Widget',
			),
			'kenburn-slider'	=> array(
				'active'	=> true,
				'title'		=> 'Carrousel Ken Burn',
				'keywords'	=> array(),
				'icon'		=> 'eicon-media-carousel eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_WIDGETS_PATH . 'kenburn-slider.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'KenBurn_Slider_Widget',
			),
			'pinterest-rss'	=> array(
				'active'	=> true,
				'title'		=> 'Flux Pinterest',
				'keywords'	=> array(),
				'icon' => 'eicon-social-icons eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_WIDGETS_PATH . 'pinterest-rss.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Pinterest_Rss_Widget',
			),
			'image-promotion'	=> array(
				'active'	=> true,
				'title'		=> 'Promotion de produit',
				'keywords'	=> array(),
				'icon'		=> 'eicon-price-table eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_WIDGETS_PATH . 'image-promotion.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Image_Promotion_Widget',
			),
			'site-thumbnail'	=> array(
				'active'	=> true,
				'title'		=> 'Miniature de site',
				'keywords'	=> array(),
				'icon'		=> 'eicon-thumbnails-right eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/add-website-thumbnail-like-a-screenshot/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_WIDGETS_PATH . 'site-thumbnail.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Site_Thumbnails_Widget',
			),
			'lecteur-audio'	=> array(
				'active'	=> true,
				'title'		=> 'Flux webradio',
				'keywords'	=> array(),
				'icon'		=> 'eicon-headphones eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_WIDGETS_PATH . 'webradio-player.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Lecteur_Audio_Widget',
			),
			'reseaux-sociaux'	=> array(
				'active'	=> true,
				'title'		=> 'Partager un article',
				'keywords'	=> array(),
				'icon'		=> 'eicon-share eac-icon-elements',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_WIDGETS_PATH . 'share-post.php',
				'name_space' => EAC_WIDGETS_NAMESPACE . 'Reseaux_Sociaux_Widget',
			),
		);
	}
	
	/**
	 * set_features_list
	 *
	 * On peut ajouter/supprimer
	 * NE JAMAIS CHANGER LE NOM DES CLÉS
	 */
	public function set_features_list() {
		
		self::$features_list = array(
			'all-features-advanced'	=> array(
				'active'	=> true,
				'title'		=> 'Active/Désactive tous les composants',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => '',
				'name_space' => '',
			),
			'acf-dynamic-tag'	=> array(
				'active'	=> true,
				'title'		=> 'ACF balises dynamiques',
				'class_depends'	=> array(),
				'func_depends'	=> array('acf_get_field_groups'),
				'help_url' => "https://elementor-addon-components.com/how-to-integrate-and-use-acf-fields-with-elementor/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_DYNAMIC_TAGS_PATH . 'acf/eac-acf-tags.php',
				'name_space' => '',
			),
			'acf-json'	=> array(
				'active'	=> false,
				'title'		=> 'ACF JSON',
				'class_depends'	=> array(),
				'func_depends'	=> array('acf_get_field_groups'),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => EAC_ACF_INCLUDES . 'eac-acf-json.php',
				'name_space' => '',
			),
			'acf-option-page'	=> array(
				'active'	=> false,
				'title'		=> "ACF page d'options",
				'class_depends'	=> array(),
				'func_depends'	=> array('acf_get_field_groups'),
				'help_url' => "https://elementor-addon-components.com/add-options-page-for-the-free-version-of-acf/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_ACF_INCLUDES . 'options-page/eac-acf-options-page.php',
				'name_space' => '',
			),
			'alt-attribute'	=> array(
				'active'	=> true,
				'title'		=> 'ALT attribut',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/add-external-image-for-elementor/#improve-your-seo-with-the-dynamic-tag-external-image/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-image.php',
				'name_space' => '',
			),
			'custom-attribute'	=> array(
				'active'	=> true,
				'title'		=> 'Attributs personnalisés',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/add-your-custom-attributes-with-elementor/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_ELEMENTOR_INCLUDES . 'custom-attributes/eac-custom-attributes.php',
				'name_space' => '',
			),
			'custom-css'	=> array(
				'active'	=> true,
				'title'		=> 'CSS personnalisé',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/elementor-custom-css/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_ELEMENTOR_INCLUDES . 'custom-css/eac-custom-css.php',
				'name_space' => '',
			),
			'dynamic-tag'	=> array(
				'active'	=> true,
				'title'		=> 'Balises dynamiques',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/elementor-dynamic-tags/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_DYNAMIC_TAGS_PATH . 'eac-dynamic-tags.php',
				'name_space' => '',
			),
			'element-link'	=> array(
				'active'	=> false,
				'title'		=> 'Lien élément',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/add-link-to-a-section-column-using-elementor/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-links.php',
				'name_space' => '',
			),
			'lottie-background'	=> array(
				'active'	=> false,
				'title'		=> 'Lottie arrière-plan',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/add-lottie-animation-in-elementor/#use-lottie-animations-in-the-background-of-an-element/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-lottie.php',
				'name_space' => '',
			),
			'motion-effects'	=> array(
				'active'	=> false,
				'title'		=> 'Effets de mouvement',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/create-animation-effects-elementor/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-effect.php',
				'name_space' => '',
			),
			'element-sticky'	=> array(
				'active'	=> true,
				'title'		=> 'Sticky élément',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/use-sticky-scrolling-effect-with-elementor/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-sticky.php',
				'name_space' => '',
			),
			'woo-dynamic-tag'	=> array(
				'active'	=> false,
				'title'		=> 'WC balises dynamiques',
				'class_depends'	=> array('WooCommerce'),
				'func_depends'	=> array(),
				'help_url' => 'https://elementor-addon-components.com/dynamic-woocommerce-tags-for-elementor/',
				'help_url_class' => '',
				'file_path' => EAC_DYNAMIC_TAGS_PATH . 'woo/eac-woo-tags.php',
				'name_space' => '',
			),
			'all-features-common' => array(
				'active'	=> true,
				'title'		=> 'Active/Désactive tous les composants',
				'keywords'	=> array(),
				'icon'		=> '',
				'name'		=> '',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => '',
				'name_space' => '',
			),
			'custom-nav-menu'	=> array(
				'active'	=> false,
				'title'		=> 'Personnaliser le menu de navigation',
				'class_depends'	=> array(),
				'func_depends'	=> array(),
				'help_url' => "https://elementor-addon-components.com/customize-wordpress-navigation-menus/",
				'help_url_class' => 'eac-admin-help',
				'file_path' => EAC_ADDONS_PATH . 'admin/settings/eac-nav-menu.php',
				'name_space' => '',
			),
			'grant-option-page'	=> array(
				'active'	=> false,
				'title'		=> "Accès page d'options",
				'class_depends'	=> array(),
				'func_depends'	=> array('acf_get_field_groups'),
				'help_url' => '#',
				'help_url_class' => '',
				'file_path' => '',
				'name_space' => '',
			),
		);
	}
	
} new Eac_Config_Elements();