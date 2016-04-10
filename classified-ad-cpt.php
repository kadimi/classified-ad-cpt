<?php
/*
Plugin Name: Classified Ad CPT
Plugin URI: http://www.kadimi.com/
Description: Classified Ad CPT
Version: 1.0.0
Author: Nabil Kadimi
Author URI: http://kadimi.com
Plugin Type: Piklist
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
        $this->requirePlugin('Piklist');
        $this->registerCPT();
    }

    protected function autoload() {
        $autoload_file_path = $this->pluginDirPath . 'vendor/autoload.php';
        if (file_exists($autoload_file_path)) {
            require $autoload_file_path;
        }
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

    protected function registerCPT() {
        add_action('piklist_post_types', function($post_types) {
            $post_types['ad'] = array(
                'labels' => piklist('post_type_labels', 'Ad')
                , 'public' => true
                , 'rewrite' => array('slug' => 'ad')
                , 'supports' => array(
                    'title'
                    , 'editor'
                    , 'thumbnail'
                    , 'custom-fields'
                    , 'comments'
                    , 'trackbacks'
                    , 'revisions'
                    , 'author'
                )
                , 'hide_meta_box' => array()
            );
            return $post_types;
        });
    }
}

$classified_ad_cpt = ClassifiedAdCPT::getInstance();
