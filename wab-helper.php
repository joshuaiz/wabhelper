<?php
/**
* Plugin Name: WAB Helper
* Plugin URI: http://mypluginuri.com/
* Description: BioDesign helper functions for any theme
* Version: 1.0
* Author: Joshua Michaels/BioDesign
* Author URI: http://wearebio.com/
* License: WTFPL
* License URI: http://sam.zoy.org/wtfpl/
* 
*/


// Page Slug Body Class
function add_slug_body_class( $classes ) {
global $post;
if ( isset( $post ) ) {
/* $classes[] = $post->post_type . '-' . $post->post_name; *//*Un comment this if you want the post_type-post_name body class */
$pagetemplate = get_post_meta( $post->ID, '_wp_page_template', true);
$classes[] = sanitize_html_class( str_replace( '.', '-', $pagetemplate ), '' );
$classes[] = $post->post_name;
}

if (is_page()) {
        global $post;
        
        // If we *do* have an ancestors list, process it
        // http://codex.wordpress.org/Function_Reference/get_post_ancestors
        if ($parents = get_post_ancestors($post->ID)) {
            foreach ((array)$parents as $parent) {
                // As the array contains IDs only, we need to get each page
                if ($page = get_page($parent)) {
                    // Add the current ancestor to the body class array
                    $classes[] = "{$page->post_type}-{$page->post_name}";
                }
            }
        }
 
        // Add the current page to our body class array
        $classes[] = "{$post->post_type}-{$post->post_name}";
    }

return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );

// Custom body classes
function my_body_class( $classes ) {
    global $post;

    # Page
    if ( is_page() ) {
        # Has parent / is sub-page
        if ( $post->post_parent ) {
            # Parent post name/slug
            $parent = get_post( $post->post_parent );
            $classes[] = $parent->post_name;

            # Parent template name
            $parent_template = get_post_meta( $parent->ID, '_wp_page_template', true);
            
            if ( !empty($parent_template) )
                $classes[] = 'template-'.sanitize_html_class( str_replace( '.', '-', $parent_template ), '' );
        }
    }

    return $classes;
}
add_filter( 'body_class', 'my_body_class' );

// Add Quicktags
function custom_quicktags() {

  if ( wp_script_is( 'quicktags' ) ) {
  ?>
  <script type="text/javascript">
  QTags.addButton( 'qt-p', 'p', '<p>', '</p>', '', '', 1 );
  QTags.addButton( 'qt-span', 'span', '<span>', '</span>', '', '', 11 );
  QTags.addButton( 'qt-h2', 'h2', '<h2>', '</h2>', '', '', 12 );
  QTags.addButton( 'qt-h3', 'h3', '<h3>', '</h3>', '', '', 13 );
  QTags.addButton( 'qt-h4', 'h4', '<h4>', '</h4>', '', '', 14 );
  QTags.addButton( 'qt-h5', 'h5', '<h5>', '</h5>', '', '', 15 );
  </script>
  <?php
  }

}

// Hook into the 'admin_print_footer_scripts' action
add_action( 'admin_print_footer_scripts', 'custom_quicktags' );

// Automatically grab the post thumbnail for excerpts.
add_filter( 'the_excerpt', 'excerpt_thumbnail' );
function excerpt_thumbnail($excerpt){ if(is_single()) return $excerpt; global $post; if ( has_post_thumbnail() ) { $img .= '<a href="'.get_permalink($post->ID).'">'.get_the_post_thumbnail($post->ID, 'custom_thumb').'</a>'; } else { $img = ''; } return $img.$excerpt;
}

// Highlight search term
function highlight_search_term($text){
    if(is_search()){
		$keys = implode('|', explode(' ', get_search_query()));
		$text = preg_replace('/(' . $keys .')/iu', '<span class="search-term">\0</span>', $text);
	}
    return $text;
}
add_filter('the_excerpt', 'highlight_search_term');
add_filter('the_title', 'highlight_search_term');

add_action( 'wp_enqueue_scripts', 'wab_load_dashicons' );
function wab_load_dashicons() {
    wp_enqueue_style( 'dashicons' );
}

/* DON'T DELETE THIS CLOSING TAG */ ?>