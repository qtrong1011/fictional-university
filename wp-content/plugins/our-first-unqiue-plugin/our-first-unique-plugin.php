<?php

/*
    Plugin Name: Our Test Plugin
    Description: A truly amazing plugin.
    Version: 1.0
    Author: Jason
    Text Domain: wcpdomain
    Domain Path: /languages
*/

class WordCountAndTimePlugin {
    function __construct(){
        add_action('admin_menu',array($this,'adminPage'));
        add_action('admin_init',array($this,'settings'));
        add_filter('the_content',array($this,'ifWrap'));
        add_action('init',array($this,'translation'));
    }
    function translation(){
        load_plugin_textdomain('wcpdomain',false,dirname(plugin_basename(__FILE__)),'/languages');
    }
    function ifWrap($content){
        if((is_main_query() && is_single()) AND (get_option('wcp_word_count','1') OR  get_option('wcp_character_count','1') OR get_option('wcp_read_time','1'))){
            return $this->createHTML($content);
        }else{
            return $content;
        }
    }
    function createHTML($content){
        $html = '<h3>' . esc_html(get_option('wcp_headline','Post Statistics')) .'</h3><p>';
        // Get word count
        if(get_option('wcp_word_count','1') == '1' OR get_option('wcp_read_time','1') == '1'){
            $wordCount = str_word_count(strip_tags($content));
        }
        // Concat word count sentence with translation feature
        if(get_option('wcp_word_count','1') == '1'){
            $html .= esc_html__('This post has','wcpdomain'). ' ' . $wordCount . ' ' . __('words','wcpdomain') . '.<br>';
        }
        // Concat character count sentence
        if(get_option('wcp_character_count','1')=='1'){
            $html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>';
        }
        //Concat read time sentence
        if(get_option('wcp_read_time','1')=='1'){
            $html .= 'This post will take about ' . round($wordCount/225) . ' minute(s) to read.</p>';
        }
        if(get_option('wcp_location','0') == '0'){
            return $html . $content;
        }
        else{
            return $content . $html;
        }

    }
    function settings(){
        add_settings_section('wcp_first_section',null,null,'word-count-setting-page');
        //LOCATION FIELD
        add_settings_field('wcp_location','Display Location',array($this,'locationHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordcountplugin','wcp_location',array(
            'sanitize_callback' => array($this,'sanitizeLocation'),
            'default' => '0'
        ));
        //HEADLINE FIELD
        add_settings_field('wcp_headline','Headline Text',array($this,'headlineHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordcountplugin','wcp_headline',array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Post Statistics'
        ));
        //WordCount FIELD
        add_settings_field('wcp_word_count','Word Count',array($this,'wordCountHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordcountplugin','wcp_word_count',array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
        //Character Count FIELD
        add_settings_field('wcp_character_count','Character Count',array($this,'characterCountHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordcountplugin','wcp_character_count',array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
        //Character Count FIELD
        add_settings_field('wcp_read_time','Read Time',array($this,'readTimeHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordcountplugin','wcp_read_time',array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
    }

    function sanitizeLocation($input){
        if($input != '0' && $input != '1'){
            add_settings_error('wcp_location','wcp_location_error','Display Location must be either begin or end');
            return get_option('wcp_location');
        }else{
            return $input;
        }
    }


    function locationHTML(){?>
        <select name="wcp_location">
            <option value="0" <?php selected(get_option('wcp_location'),'0') ?>>Beginning of post</option>
            <option value="1" <?php selected(get_option('wcp_location'),'1') ?>>End of post</option>
        </select>
    <?php }


    function headlineHTML(){?>
        <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')); ?>">
    <?php }

    function wordCountHTML(){?>
        <input type="checkbox" name="wcp_word_count" value="1" <?php checked(get_option('wcp_word_count'),'1') ?>>
    <?php }

    function characterCountHTML(){?>
        <input type="checkbox" name="wcp_character_count" value="1" <?php checked(get_option('wcp_character_count'),'1') ?>>
    <?php }


    function readTimeHTML(){?>
        <input type="checkbox" name="wcp_read_time" value="1" <?php checked(get_option('wcp_read_time'),'1') ?>>
    <?php }



    function adminPage(){
        add_options_page('Word Count Settings',__('Word Count','wcpdomain'),'manage_options','word-count-setting-page',array($this,'ourHTML'));
    }
    
    function ourHTML(){?>
        <div class="wrap">
            <h1>Word Count Settings</h1>
            <form action="options.php" method="POST">
                <?php 
                    settings_fields('wordcountplugin');
                    do_settings_sections('word-count-setting-page');
                    submit_button();
                ?>
            </form>
        </div>
    <?php }
}
$wordCountAndTimePlugin = new WordCountAndTimePlugin();


