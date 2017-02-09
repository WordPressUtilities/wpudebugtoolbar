<?php

/*
Plugin Name: WP Utilities Debug toolbar
Description: Display a debug toolbar for developers.
Version: 0.7.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUDebugToolbar {
    public $plugin_version = '0.7.2';

    public function __construct() {
        add_action('init', array(&$this,
            'init'
        ));
    }

    public function init() {
        if (current_user_can('administrator') && !is_admin()) {
            $this->load_hooks();
        }
    }

    public function load_hooks() {
        add_action('wp_footer', array(&$this,
            'launch_bar'
        ), 999);
        add_action('wp_enqueue_scripts', array(&$this,
            'enqueue_assets'
        ), 1000);
    }

    public function enqueue_assets() {
        wp_register_script('wpudebugtoolbar_scripts', plugins_url('assets/script.js', __FILE__), array(), $this->plugin_version, 1);
        wp_register_style('wpudebugtoolbar_style', plugins_url('assets/style.css', __FILE__), array(), $this->plugin_version);
        wp_enqueue_script('wpudebugtoolbar_scripts');
        wp_enqueue_style('wpudebugtoolbar_style');
    }

    /* ----------------------------------------------------------
      Bar
    ---------------------------------------------------------- */

    public function launch_bar() {
        global $template, $pagenow, $wp_filter, $wp_actions;
        if ($pagenow == 'wp-login.php') {
            return;
        }
        if (!empty($_POST) && isset($_POST['wp_customize'])) {
            return;
        }
        echo '<div data-show-queries="" id="wputh-debug-toolbar" class="wputh-debug-toolbar">';

        // All queries
        if (defined('SAVEQUERIES') && SAVEQUERIES) {
            global $wpdb;
            echo '<div id="wputh-debug-queries">';
            echo "<pre>";
            print_r($wpdb->queries);
            echo "</pre>";
            echo '</div>';
        }

        // All hooks
        echo '<div id="wputh-debug-hooks">';
        foreach ($wp_filter as $hook => $hooks) {
            if (empty($hooks)) {
                continue;
            }
            echo '<strong>' . $hook . ': </strong>';
            $hookstosort = $hooks;
            if(is_object($hooks)){
                $hookstosort = $hooks->callbacks;
            }
            ksort($hookstosort, SORT_NATURAL);
            foreach ($hookstosort as $priority => $hooked_func) {
                echo '<br /> -<em>' . $priority . '</em> : ' . implode(', ', array_keys($hooked_func));
            }
            echo '<br />';
        }
        echo '</div>';

        echo '<div class="wputh-debug-toolbar-content">';

        // Theme
        echo 'Theme : <strong>' . wp_get_theme() . '</strong>';
        echo ' <em>&bull;</em> ';

        // Template
        echo 'File : <strong>' . basename($template) . '</strong>';
        echo ' <em>&bull;</em> ';

        // Current language
        echo 'Lang : <strong>' . get_bloginfo('language') . '</strong>';
        echo ' <em>&bull;</em> ';

        // Memory used
        echo 'Memory : <strong>' . round(memory_get_peak_usage() / (1024 * 1024), 3) . '</strong> mb';
        echo ' <em>&bull;</em> ';

        // Queries
        echo 'Queries : <strong>' . get_num_queries() . '</strong>';

        if (defined('SAVEQUERIES') && SAVEQUERIES) {
            echo ' <em>&bull;</em> ';
            echo '<span class="wputh-toggle" id="wputh-debug-display-queries">Display queries</span>';
            echo '<span class="wputh-toggle" id="wputh-debug-hide-queries">Hide queries</span>';
        }

        // Execution time
        echo ' <em>&bull;</em> ';
        echo 'Time : <strong>' . timer_stop(0) . '</strong> sec';

        // Hooks
        echo ' <em>&bull;</em> ';
        echo '<span class="wputh-toggle" id="wputh-debug-display-hooks">Display hooks</span>';
        echo '<span class="wputh-toggle" id="wputh-debug-hide-hooks">Hide hooks</span>';

        echo '</div>';

        echo '</div>';
    }
}

$WPUDebugToolbar = new WPUDebugToolbar();
