<?php
//SEARCH ROUTE (REST API ROUTE)
function university_register_search(){
    register_rest_route('university/v1','search',array(
        'methods' => WP_REST_SERVER::READABLE,// Another way to GET method (safe way for any hostings)
        'callback' => 'universitySearchResults'
    ));
}
function universitySearchResults($data){
    $mainQuery = new WP_Query(array(
        'post_type' => array('post','page','professor','program','event'),
        's' => sanitize_text_field($data['term']) //'s' is a keyword for search in WP_Query
    ));
    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array()
    );
    while($mainQuery->have_posts()){
        $mainQuery->the_post();
        if(get_post_type()=='post' or get_post_type() == 'page'){
            array_push($results['generalInfo'],array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'post_type' => get_post_type(),
                'author_name' => get_the_author()
            ));
        }else if (get_post_type() == 'professor'){
            array_push($results['professors'],array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'post_type' => get_post_type(),
                'image' => get_the_post_thumbnail_url(0,'professorLandscape')
            ));
        }else if (get_post_type() == 'program'){
            array_push($results['programs'],array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'post_type' => get_post_type(),
                'author_name' => get_the_author(),
                'id' => get_the_id()
            ));
        }else if (get_post_type() == 'event'){
            date_default_timezone_set('America/Los_Angeles');
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if (has_excerpt()){
                $description = get_the_excerpt();
              } else{
                $description =  wp_trim_words(get_the_content(),20);
              } 
            array_push($results['events'],array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'post_type' => get_post_type(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
        }
    }
    //CUSTOM QUERY FOR RELATIONSHIP
    if($results['programs']){
        $programMetaQuery = array(
            'relation' => 'OR' //Default relation is AND
        );
        foreach($results['programs'] as $item){
            array_push($programMetaQuery,array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"'. $item['id']. '"'
            ));
        }
        $programRelationshipQuery = new WP_Query(array(
            'post_type' => array('professor','event'),
            'meta_query' => $programMetaQuery
        ));
        while($programRelationshipQuery->have_posts()){
            $programRelationshipQuery->the_post();
            if (get_post_type() == 'professor'){
                array_push($results['professors'],array(
                    'title' => get_the_title(),
                    'url' => get_the_permalink(),
                    'post_type' => get_post_type(),
                    'image' => get_the_post_thumbnail_url(0,'professorLandscape')
                ));
            } else if(get_post_type() == 'event'){
                date_default_timezone_set('America/Los_Angeles');
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if (has_excerpt()){
                $description = get_the_excerpt();
              } else{
                $description =  wp_trim_words(get_the_content(),20);
              } 
            array_push($results['events'],array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'post_type' => get_post_type(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
            }
        }
        //REMOVE DUPLICATES DATA
        $results['professors'] = array_values(array_unique($results['professors'],SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'],SORT_REGULAR));
    }
    
    
    return $results;
}

add_action('rest_api_init', 'university_register_search');

?>