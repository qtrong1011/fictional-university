<?php
    //LOAD Search Route
    require get_theme_file_path('/includes/search_route.php');
    //LOAD Like Route
    require get_theme_file_path('/includes/like_route.php');

    //CUSTOMIZE DEFAULT REST API
    function university_custom_rest() {
        register_rest_field('post','author_name',array(
            'get_callback' => function () {
                return get_the_author();
            }
        ));
        register_rest_field('note','userNoteCount',array(
            'get_callback' => function () {
                return count_user_posts(get_current_user_id(),'note');
            }
        ));
    }
    add_action('rest_api_init', 'university_custom_rest');
    function pageBanner($args = NULL){
        //TO HANDLE IF THE TITLE/SUBTITLE/IMAGE IS NOT PROVIDED
        if(!isset($args['title'])){
            $args['title'] = get_the_title();
        }
        if(!isset($args['subtitle'])){
            $args['subtitle'] = get_field('page_banner_subtitle');
        }
        if(!isset($args['photo'])){
            if(get_field('page_banner_background_image') && !is_archive() && !is_home()){
                $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
            } else{
                $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
            }
        }

?>
        <div class="page-banner">
            <div class="page-banner__bg-image" style="background-image: url(<?php 
                    echo $args['photo'];

                ?>)">
            </div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
                <div class="page-banner__intro">
                    <p><?php echo $args['subtitle']; ?></p>
                </div>
            </div>
        </div>
    <?php }



    //LOADING CSS/JS FILES
    function university_files() {
        wp_enqueue_script('main-university-js',get_theme_file_uri('/build/index.js'), array('jquery'),'1.0',true);
        wp_enqueue_style('university_main_styles',get_theme_file_uri('/build/style-index.css'));
        wp_enqueue_style('university_extra_styles',get_theme_file_uri('/build/index.css'));
        wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('custom-google-fonts','//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
        //To set environment variable for local site and token nonce
        wp_localize_script('main-university-js','universityData',array(
            'root_url' => get_site_url(),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }
    //Loading CSS & JS above files
    add_action('wp_enqueue_scripts','university_files');

    function university_features(){
        add_theme_support('title-tag');
        //ACTIVE FEATURE IMAGES. REMEMBER TO UPDATE CUSTOM POST TYPE 'SUPPORTS'
        add_theme_support('post-thumbnails');
        //ADJUSTING IMAGE SIZE (nickname, width, height,cropping boolean)
        add_image_size('professorLandscape', 400, 260, true);
        add_image_size('professorPortrait', 480, 650, true);
        add_image_size('pageBanner', 1500, 350, true);
        add_theme_support('editor-styles');
        add_editor_style(array('https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i','build/style-index.css','build/index.css'));
    }
    add_action('after_setup_theme','university_features');
    

    function university_adjust_queries($query){
        //Adjust default query for archive program
        if(!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()){
            $query->set('orderby','title');
            $query->set('order','ASC');
            $query->set('post_per_page','-1');
        }

        //Adjust default query for archive event
        if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()){
            $today = date('Ymd');
            $query->set('meta_key','event_date');
            $query->set('orderby','meta_value_num');
            $query->set('order','ASC');
            $query->set('meta_query',array(
                array(
                    'key' => 'event_date',
                    'compare' => '>=',
                    'value' => $today,
                    'type' => 'numeric'
                  )
            ));
        }
        

    }
    add_action('pre_get_posts','university_adjust_queries');

    //REDIRECT SUBSCRIBER ACCOUNTS OUT OF ADMIN AND ONTO HOMEPAGE
    function redirectSubscriber() {
        $currentUser = wp_get_current_user();

        if(count($currentUser->roles) == 1 AND $currentUser->roles[0] == 'subscriber'){
            wp_redirect(esc_url(site_url('/')));
            exit;
        }
    }
    add_action('admin_init', 'redirectSubscriber');
    //REMOVE ADMIN BAR FOR SUBSCRIBER ACCOUNTS
    function noSubsAdminBar() {
        $currentUser = wp_get_current_user();

        if(count($currentUser->roles) == 1 AND $currentUser->roles[0] == 'subscriber'){
            show_admin_bar(false);
        }
    }
    add_action('wp_loaded', 'noSubsAdminBar');

    //CUSTOMIZE LOGIN SCREEN
    add_filter('login_headerurl', 'ourHeaderUrl');
    function ourHeaderUrl(){
        return esc_url(site_url('/'));
    }

    add_action('login_enqueue_scripts', 'ourLoginCSS');
    function ourLoginCSS(){
        wp_enqueue_style('university_main_styles',get_theme_file_uri('/build/style-index.css'));
        wp_enqueue_style('university_extra_styles',get_theme_file_uri('/build/index.css'));
        wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('custom-google-fonts','//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    }
    add_filter('login_headertitle','ourLoginLogo');
    function ourLoginLogo(){
        return get_bloginfo('name');
    }
    //FORCE NEW NOTE TO BE PRIVATE IN SERVER-SIDE
    add_filter('wp_insert_post_data','makeNotePrivate',10,2); //10 is priority number and 2 is number of params will be passed into
    function makeNotePrivate($data,$postarr){
        if($data['post_type']=='note'){
            //Limite each user to create more than 4 notes
            if(count_user_posts(get_current_user_id(),'note') > 4 AND !$postarr['ID'] ){
                die('You have reached your note limit');
            }
            //Sanitize content and title before get save to WP database
            $data['post_content'] = sanitize_textarea_field($data['post_content']);
            $data['post_title'] = sanitize_text_field($data['post_title']);
        }
        //force new note to be private
        if($data['post_type'] == 'note' AND $data['post_status'] != 'trash'){
            $data['post_status'] = 'private';
        }
        return $data;
    }


class PlaceHolderBlock {
        function __construct($name,$renderCallback = null, $data = null){
            $this->name = $name;

            add_action('init', array($this,'onInit'));
    
        }
        function ourRenderCallback($attributes, $content){
            ob_start(); //output buffer start
            require get_theme_file_path("/our-blocks/{$this->name}.php");
            return ob_get_clean(); //output buffer ends
    
        }
        function onInit(){
            wp_register_script($this->name, get_stylesheet_directory_uri() . "/our-blocks/{$this->name}.js", array('wp-blocks','wp-editor'));
            register_block_type("ourblocktheme/{$this->name}",array(
                'editor_script' => $this->name,
                'render_callback' => [$this, 'ourRenderCallback']
            ));
        }
}

new PlaceHolderBlock("eventsandblogs");
new PlaceHolderBlock('header');
new PlaceHolderBlock('footer');
new PlaceHolderBlock('single');
new PlaceHolderBlock('singlepage');
new PlaceHolderBlock('blogindex');
new PlaceHolderBlock('programarchive');
new PlaceHolderBlock('singleprogram');
new PlaceHolderBlock('singleprofessor');
new PlaceHolderBlock('mynotes');

class JSXBlock {
    function __construct($name,$renderCallback = null, $data = null){
        $this->name = $name;
        $this->renderCallback = $renderCallback;
        $this->data = $data;
        add_action('init', array($this,'onInit'));

    }
    function ourRenderCallback($attributes, $content){
        ob_start(); //output buffer start
        require get_theme_file_path("/our-blocks/{$this->name}.php");
        return ob_get_clean(); //output buffer ends

    }
    function onInit(){
        wp_register_script($this->name, get_stylesheet_directory_uri() . "/build/{$this->name}.js", array('wp-blocks','wp-editor'));
        if($this->data){
            wp_localize_script($this->name,$this->name,$this->data);
        }
        $ourArgs = array(
            'editor_script' => $this->name
        );
        if($this->renderCallback){
            $ourArgs['render_callback'] = array($this, 'ourRenderCallback');
        }
        register_block_type("ourblocktheme/{$this->name}",$ourArgs);
    }
}
new JSXBlock('banner',true,['fallbackimage'=> get_theme_file_uri('/images/library-hero.jpg')]);
new JSXBlock('genericheading',true);
new JSXBlock('genericbutton');
new JSXBlock('slideshow',true);
new JSXBlock('slide',true,['themeimagepath'=> get_theme_file_uri('/images/')]);
?>