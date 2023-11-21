<?php
    $properties = [
        'small' => 'h3',
        'medium' => 'h2',
        'large' => 'h1',
    ];
    $size = array_key_exists("size",$attributes) && isset($attributes["size"]) ? $attributes['size'] : "large";
    $text = array_key_exists('text',$attributes) && isset($attributes['text']) ? $attributes['text'] : '';
    $tagName = $properties[$size];?>

    <<?php echo $tagName ?> class="headline headline--<?php echo $size ?>"><?php echo $text; ?></<?php echo $tagName; ?>>