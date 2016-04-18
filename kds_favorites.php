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
        $this->requirePlugin('Piklist');
        $this->enqueuePublicAssets();
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
}

KDSFavorites::getInstance();
