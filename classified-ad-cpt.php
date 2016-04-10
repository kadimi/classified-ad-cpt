<?php
/*
Plugin Name: Classified Ad CPT
Plugin URI: http://www.kadimi.com/
Description: Classified Ad CPT
Version: 1.0.0
Author: Nabil Kadimi
Author URI: http://kadimi.com
License: GPL2
*/

// Avoid direct calls to this file.
if ( !function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

Class ClassifiedAdCPT {

    protected static $instance = null;
    protected $pluginDirPath;

    public function __construct() {
    }

    public function __clone() {
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new static;
            self::$instance->init();
        }
        return self::$instance;
    }

    protected function init() {
        $this->pluginDirPath = plugin_dir_path(__FILE__);
        $this->autoload();
        // $this->requirePlugin('akismet');
    }

    protected function autoload() {
        $autoload_file_path = $this->pluginDirPath . 'vendor/autoload.php';
        if (file_exists($autoload_file_path)) {
            require $autoload_file_path;
        }
    }

    protected function requirePlugin($name, $options = []) {
        add_action('tgmpa_register', function() use($name, $options) {
            $options['name'] =  $name;
            $options['slug'] =  !empty($options['slug'])
                ? $options['slug']
                : strtolower(preg_replace('/[^\w\d]+/', '-', $name))
            ;
            $options['required'] = true;
            tgmpa([$options]);
        });
    }
}

$classified_ad_cpt = ClassifiedAdCPT::getInstance();
