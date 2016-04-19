<?php
/*
Plugin Name: KDS Favorites
Plugin URI: http://www.kadimi.com/
Description: KDS Favorites
Version: 1.0.0
Author: Nabil Kadimi
Author URI: http://kadimi.com
License: GPL2
*/

// Avoid direct calls to this file.
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

Class KDSFavorites {

    protected static $instance = null;
    protected $pluginDirPath;
    protected $pluginDirURL;
    protected $pluginSlug;

    public function __construct() {
    }

    public function __clone() {
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new static;
            self::$instance->init();
        }
        return $GLOBALS[self::$instance->pluginSlug] = self::$instance;
    }

    protected function init() {
        $this->pluginDirPath = plugin_dir_path(__FILE__);
        $this->pluginDirURL = plugin_dir_url(__FILE__);
        $this->pluginSlug = $this->camelCaseToUnderscore(__CLASS__);
        $this->autoload();
        // $this->requirePlugin('akismet');
        $this->enqueuePublicAssets();
        $this->shortcode();
        $this->modifyEmail();
    }

    protected function autoload() {
        $autoload_file_path = $this->pluginDirPath . 'vendor/autoload.php';
        if (file_exists($autoload_file_path)) {
            require $autoload_file_path;
        }
    }

    protected function enqueuePublicAssets() {
        add_action('wp_enqueue_scripts', function() {
            if (file_exists($this->pluginDirPath . 'public/css/frontend-main.css')) {
                wp_enqueue_style($this->pluginSlug, $this->pluginDirURL . 'public/css/frontend-main.css');
            }
        });
        add_action('wp_enqueue_scripts', function() {
            if (file_exists($this->pluginDirPath . 'public/js/frontend-main.js')) {
                wp_enqueue_script($this->pluginSlug, $this->pluginDirURL . 'public/js/frontend-main.js', ['jquery'], null, true);
            }
        });
        add_action('admin_enqueue_scripts', function() {
            if (file_exists($this->pluginDirPath . 'public/css/backend-main.css')) {
                wp_enqueue_style($this->pluginSlug, $this->pluginDirURL . 'public/css/backend-main.css');
            }
        });
        add_action('admin_enqueue_scripts', function() {
            if (file_exists($this->pluginDirPath . 'public/js/backend-main.js')) {
                wp_enqueue_script($this->pluginSlug, $this->pluginDirURL . 'public/js/backend-main.js', ['jquery'], null, true);
            }
        });
    }

    protected function requirePlugin($name, $options = []) {
        add_action('tgmpa_register', function() use($name, $options) {
            $options['name'] = $name;
            $options['slug'] = !empty($options['slug'])
                ? $options['slug']
                : strtolower(preg_replace('/[^\w\d]+/', '-', $name))
            ;
            $options['required'] = true;
            tgmpa([$options]);
        });
    }

    protected function camelCaseToUnderscore($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        foreach ($matches[0] as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $matches[0]);
    }

    protected function getImageId($url) {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url));
        return $attachment[0]; 
    }

    protected function shortcode() {
        add_shortcode($this->pluginSlug, function() {

            $output = '';
            $favs = $_COOKIE['kds_favorites'] ? json_decode(stripslashes($_COOKIE['kds_favorites'])) : [];

            foreach ($favs as $fav) {
                $fav_origin = preg_replace('/-\d+x\d+(\.(?:jpe?g|png))/', '$1', $fav);
                $fav_id = $this->getImageId($fav_origin) ;
                if (!in_array(parse_url($fav, PHP_URL_SCHEME), ['http', 'https'])) {
                    continue;
                }
                if (!getimagesize($fav)) {
                    continue;
                }
                $output .= sprintf('<a title="%s" href="%s" rel="prettyPhoto[kds_favorites]"><img src="%s"/></a> ', get_the_title($fav_id), $fav, $fav);
            }

            wp_enqueue_script('isotope');
            wp_enqueue_script('prettyphoto');
            wp_enqueue_style('prettyphoto');

            $output = sprintf('<div id="kds_favorites">%s</div>', $output);
            return $output;
        });
    }

    protected function modifyEmail() {
        add_filter('wp_mail', function($args) {
            if (strpos($args['message'], __('Site: ', 'mk_framework')) === false) return $args;
            if (strpos($args['message'], __('Name: ', 'mk_framework')) === false) return $args;
            if (strpos($args['message'], __('Email: ', 'mk_framework')) === false) return $args;
            if (strpos($args['message'], __('Messages: ', 'mk_framework')) === false) return $args;

            $args['message'] .= "\n\n";
            $args['message'] .= __('Favorites: ', 'kds_favorites');
            $args['message'] .= "\n";

            $favs = $_COOKIE['kds_favorites'] ? json_decode(stripslashes($_COOKIE['kds_favorites'])) : [];
            foreach ($favs as $fav) {
                if (!in_array(parse_url($fav, PHP_URL_SCHEME), ['http', 'https'])) {
                    continue;
                }
                if (!getimagesize($fav)) {
                    continue;
                }
                $args['message'] .= '- ';
                $args['message'] .= $fav;
                $args['message'] .= "\n";
            }

            return $args;
        });

    }
}

KDSFavorites::getInstance();
