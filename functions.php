<?php 

/**
 *********************************************************
 *  
 * @package    195 metal CDs child theme
 * @subpackage functions.php
 * @version    1.0.0
 * @author     Gareth J M Saunders
 * 
 *  
 * TABLE OF CONTENTS
 *
 * 1. Enqueue child theme CSS
 * 2. Remove 'posted on' from top of review post
 * 3. Posts : review scores custom taxonomy : create new custom taxonomy
 * 4. Posts : review score columns on admin screen
 * 5. Posts : add meta box for review score to posts
 * 6. Pages : [fulllist] shortcode
 * 7. Pages : [genres] shortcode
 * 8. Pages : [scores] shortcode
 * 
 */



/**
 *********************************************************
 * 1. Enqueue child theme CSS
 *    Load theme CSS file to WordPress
 * 
 *    @package style.css
 *    @version 1.0.0 2022-05-23
 * 
 */

add_action( 'wp_enqueue_scripts', 'theme_195_metal_cds_enqueue_styles' );
function theme_195_metal_cds_enqueue_styles() {
    $parenthandle = 'parent-style';
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') // this only works if you have Version in the style header
    );
}



/**
 *********************************************************
 * 2. Remove 'Posted on' from top of review post
 *    Overrules function from files below
 * 
 *    @package     single.php
 *    @subpackage  template-parts/content-single.php
 *    @version     1.0.0 2022-05-23
 * 
 */

if (!function_exists('imagegridly_posted_on')):
    
    // Prints HTML with meta information for the current post-date/time and author
    function imagegridly_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf( $time_string,
            esc_attr( get_the_date( 'c' ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( 'c' ) ),
            esc_html( get_the_modified_date() )
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x( '%s', 'post date', 'imagegridly' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
        );

        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x( 'by %s', 'post author', 'imagegridly' ),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . '</span>'; // WPCS: XSS OK.

    }
endif;



/**
 *******************************************************************************
 * 3. Posts : Create new custom taxonomy for review scores
 *    New taxonomy called 'score' that acts like standard categories. This will
 *    allow me to select a score category for use with the [scores] shortcode.
 * 
 *    @package WordPress core
 *    @version 1.0.0 2022-05-23
 *    @link  http://justintadlock.com/archives/2010/06/10/a-refresher-on-custom-taxonomies
 * 
 */
function metalcds_custom_taxonomy()  {
    $labels = array(
        'name'                       => _x( 'Scores', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Score', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Scores', 'text_domain' ),
        'all_items'                  => __( 'All Scores', 'text_domain' ),
        'parent_item'                => __( 'Parent Score', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Score:', 'text_domain' ),
        'new_item_name'              => __( 'New Score Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Score', 'text_domain' ),
        'edit_item'                  => __( 'Edit Score', 'text_domain' ),
        'update_item'                => __( 'Update Score', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate scores with commas', 'text_domain' ),
        'search_items'               => __( 'Search scores', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove scores', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used scores', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true, // true = like categories; false = like tags
        'public'                     => true,
        'show_admin_column'          => true, // View on the All Posts admin screen
        'show_in_menu'               => true, 
        'show_in_nav_menus'          => true,
        'show_in_rest'               => true, // View in the new blocks editor
        'show_tagcloud'              => true,
        'show_ui'                    => true
    );
    // Register the new 'score' taxonomy for the 'post' post_type.
    register_taxonomy( 'score', 'post', $args );
}
add_action( 'init', 'metalcds_custom_taxonomy', 0 );



/**
 *******************************************************************************
 * 4. Posts : Add review score columns to Posts admin screen
 *    Add review score columns to Posts admin screen
 *
 *    @package Posts
 *    @version 1.0.0 2022-05-23
 *    @link    http://wordpress.stackexchange.com/questions/43970/adding-menu-order-column-to-custom-post-type-admin-screen
 *    @link    http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_edit-post_type_columns
 * 
 */

function add_reviewscore_admin_columns($columns) {
    $new_columns = array(
        '195metalcds-score' => __('Review score', '195-metal-cds')
    );
    return array_merge($columns, $new_columns);
}

// First parameter must be: manage_{post-type}_columns
// Second parameter names the function to be called
add_filter('manage_posts_columns','add_reviewscore_admin_columns');

// Show data within the column
function show_review_score_column($name){
    global $post;
    switch ($name) {
        case '195metalcds-score':
            $review_score = get_post_meta(get_the_ID(), '195metalcds-score', true);
            echo ($review_score);
            break;
        default:
            break;
    }
}
add_action('manage_posts_custom_column','show_review_score_column');



/**
 *********************************************************
 * 5. Posts : Add meta box to Posts to enter review score
 *    This defines a meta box, adds an input field,
 *    populates the input field with any stored meta data,
 *    and updates or deletes the meta data on page save.
 *
 *    @package Standard WP post
 *    @version 1.0.0 2022-05-23
 */


// 1. Define meta box

$prefix   = '195metalcds-';
$meta_box = array(
    'id'        => '195metalcds-meta-box', // HTML 'id' attribute of the edit screen section
    'title'     => 'Review score %',       // Title of the edit screen section, visible to user
    'posttype'  => 'post',                 // The type of write screen on which to show the edit screen section ('post', 'page', 'link', 'attachment' or 'custom_post_type' where custom_post_type is the custom post type slug)
    'context'   => 'side',                 // The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side')
    'priority'  => 'high',                 // The priority within the context where the boxes should show ('high', 'core', 'default' or 'low')
    'fields'    => array
    (
        array
        (
            'desc' => 'Min 0, Max 100, intervals of 5',
            'id'   => $prefix . 'score',
            'type' => 'text',
            'placeholder'  => '%'
        )
    )
);
add_action('admin_menu', 'mytheme_add_box');

function mytheme_add_box() {
    global $meta_box;
    add_meta_box($meta_box['id'], $meta_box['title'], 'mytheme_show_box', $meta_box['posttype'], $meta_box['context'], $meta_box['priority']);
}


// 2. Callback function (called as a parameter in add_meta_box, above) 
//    to show input field in meta box and populate it with any data.

function mytheme_show_box() {
    global $meta_box, $post;

    // Use nonce for verification
    echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    
    // Loop through the fields array defined in 1. above.
    foreach ($meta_box['fields'] as $field) {

        // Get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);
        ?>
        <p><input type="text" name="<?php echo($field['id']); ?>" id="<?php echo($field['id']); ?>" value="<?php echo($meta); ?>" placeholder="<?php echo($field['placeholder']); ?>" style="width: 50%;" /></p>
        <p><span class="howto" style="margin-left: 1em;"><?php echo($field['desc']); ?></span></p>
        <?php
    }
}


// 3. Save data from meta box on post save

function mytheme_save_data($post_id) {
    global $meta_box;

    // Verify nonce
    if (!wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    foreach ($meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}
add_action('save_post', 'mytheme_save_data');



/**
 *********************************************************
 * 6. PAGE : FULL-LIST SHORTCODE
 *
 * Add [fulllist] shortcode to list all items alphabetically by genre
 * @package     Page
 */

// shortcode_name, function
add_shortcode( 'fulllist', 'custom_shortcode_fulllist' );

function custom_shortcode_fulllist() {
ob_start();
// CODE


// Exclude category 2 which is 'About the project'
$arguments = array(
    'numberposts'      => -1,
    'orderby'          => 'title',
    'post_status'      => 'publish',
    'order'            => 'ASC',
    'category__not_in' => [2,764],
);

$posts = get_posts($arguments);
echo('<ol>');

foreach($posts as $post) {
    $postid = $post->ID;
    $link = get_permalink($postid);
    $review_score = get_post_meta( $postid, '195metalcds-score', true );

    if ($review_score) {
        echo("<li><a href='$link'>" . $post->post_title . "</a> — $review_score%</li>");
    } else {
        echo("<li><a href='$link'>" . $post->post_title . "</a></li>");
    }
}
echo('</ol>');



// END CODE
$return_string = ob_get_clean();
return $return_string;
}



/**
 *********************************************************
 * 7. PAGE : GENRES SHORTCODE
 *
 * Add [genres] shortcode to list all items alphabetically by genre
 * @package     Page
 */

// shortcode_name, function
add_shortcode( 'genres', 'custom_shortcode_genres' );

function custom_shortcode_genres() {

    ob_start();

    $post_type = 'post';
    $taxonomy = 'category';

    // exclude category 3 = 'metal'
    $tax_args = array(
        'order' => 'ASC',
        'exclude' => '3',
    );
    $terms = get_terms( $taxonomy, $tax_args );

    foreach( $terms as $term ) :

        $post_args = array(
            'taxonomy'       => $taxonomy,
            'term'           => $term->slug,
            'order'          => 'ASC',
            'orderby'        => 'title',
            'posts_per_page' => '-1',
            'post_type'      => 'post',
            'post_status'    => 'publish',
            );
        $posts = new WP_Query( $post_args );
    ?>
        <h2 class="genre-heading"><?php echo($term->name); ?></h2>
        <ul>
            <?php
            if( $posts->have_posts() ):
                while( $posts->have_posts() ) : $posts->the_post(); ?>
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            <?php
                                $review_score = get_post_meta( get_the_ID(), '195metalcds-score', true );

                                if ($review_score) {
                                    echo('<span class="review-score"> — ' . $review_score . '%</span>');
                                } else {
                                    echo('<span class="review-score"></span>');
                                }
                            ?>
                    </li>
                <?php endwhile;
            endif; ?>
        </ul>
    <?php endforeach;

        $return_string = ob_get_clean();
        return $return_string;
}



/**
 *********************************************************
 * 8. PAGE : SCORES SHORTCODE
 *
 * Add [scores] shortcode to list all items alphabetically by genre
 * WordPress wouldn't sort the taxonomy names in proper numerical order
 * it went 90, 80, 70, ... 20, 10, 100.
 * So, list 100 first, then loop through 95 to zero.
 * @package     Page
 */

// shortcode_name, function
add_shortcode( 'scores', 'custom_shortcode_scores' );

function custom_shortcode_scores() {
ob_start();

// 100
// Update 'include' to reflect the live category ID for 100
// localhost include 767
// live      include 770

    $post_type = 'post';
    $taxonomy = 'score';
    $tax_args = array(
        'order' => 'DESC',
        'include' => '770',
    );
    $terms = get_terms( $taxonomy, $tax_args );

    foreach( $terms as $term ) :

        $post_args = array(
            'taxonomy'       => $taxonomy,
            'term'           => $term->slug,
            'order'          => 'ASC',
            'orderby'        => 'title',
            'posts_per_page' => '-1',
            'post_type'      => 'post',
            'post_status'    => 'publish',
            );
        $posts = new WP_Query( $post_args );
    ?>
        <h2 class="scores-heading"><?php echo($term->name); ?></h2>
        <ul>
            <?php
            if( $posts->have_posts() ):
                while( $posts->have_posts() ) : $posts->the_post(); ?>
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php endwhile;
            endif; ?>
        </ul>
    <?php endforeach;

// 95 to 0
// Update 'exclude' to reflect the live category ID for 100

    $post_type = 'post';
    $taxonomy = 'score';
    $tax_args = array(
        'order' => 'DESC',
        'exclude' => '770',
    );
    $terms = get_terms( $taxonomy, $tax_args );

    foreach( $terms as $term ) :

        $post_args = array(
            'taxonomy'       => $taxonomy,
            'term'           => $term->slug,
            'order'          => 'ASC',
            'orderby'        => 'title',
            'posts_per_page' => '-1',
            'post_type'      => 'post'
            );
        $posts = new WP_Query( $post_args );
    ?>
        <h2 class="scores-heading"><?php echo($term->name); ?></h2>
        <ul>
            <?php
            if( $posts->have_posts() ):
                while( $posts->have_posts() ) : $posts->the_post(); ?>
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php endwhile;
            endif; ?>
        </ul>
    <?php endforeach;

// END
$return_string = ob_get_clean();
return $return_string;
}