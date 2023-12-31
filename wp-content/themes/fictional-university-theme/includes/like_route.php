<?php
add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes() {
    //CREATE LIKE
    register_rest_route('university/v1','manageLike',array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));
    //REMOVE LIKE
    register_rest_route('university/v1','manageLike',array(
        'methods' => 'DELETE',
        'callback' => 'removeLike'
    ));
}

function createLike($data){
    if(is_user_logged_in()){
        $professor = sanitize_text_field($data['professor_id']);
        $existQuery = new WP_Query(array(
            'author' => get_current_user_id(),
            'post_type' => 'like',
            'meta_query' => array(array(
              'key' => 'liked_professor_id',
              'compare' => '=',
              'value' => $professor
            ))
            ));
        if($existQuery->found_posts == 0 AND get_post_type($professor) == 'professor'){
            //create new like post
            return wp_insert_post(array(
                'post_type' => 'like',
                'post_status' => 'publish',
                'post_title' => 'Second PHP Test',
                'meta_input' =>array(
                'liked_professor_id' => $professor
                )
            ));
        }else{
            die("Invalid professor id");
        }
        
    }else{
        die('Only logged in users can create a like');
    }
    
}

function removeLike($data){
    $likeID = sanitize_text_field($data['like']);
    if(get_current_user_id() == get_post_field('post_author',$likeID) AND get_post_type($likeID) == 'like'){
        wp_delete_post($likeID, true);
        return 'Congrats, like deleted.';
    }else{
        die("You dont have permission to delete it");
    }
    
}