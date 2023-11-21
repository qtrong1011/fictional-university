<?php

/*
    Plugin Name: Are You Paying Attention Quiz
    Description: A truly amazing plugin.
    Version: 1.0
    Author: Jason
*/

if(! defined('ABSPATH')) exit; //Exit if accessed directly

class AreYouPayingAttention {
    function __construct(){
        add_action('init', array($this, 'adminAssets'));
    }
    function adminAssets(){
        register_block_type(__DIR__,array(
            'render_callback' => array($this, 'theHTML')
        ));
    }
    function theHTML($attributes){
        if(!is_admin()){
            wp_enqueue_script('attentionFrontend', plugin_dir_url(__FILE__) . 'build/frontend.js', array('wp-element'));
        }
        ob_start(); //output buffer start ?>
        <div class="paying-attention-update-me">
            <pre style="display:none"><?php echo wp_json_encode($attributes) ?> </pre>

        </div>
    <?php return ob_get_clean(); //output buffer end to have clean HTML structure
    }
}

$areYouPayingAttention = new AreYouPayingAttention();