<?php
/**
 * Displays a generic post grid or list
 * This element is kinda ugly, but just has to have a lot of options
 */

// Atom values
$molecule = wp_parse_args( $molecule, array(
    'ajax'          => true,                            // To paginate using ajax
    'args'          => array(),                         // Query arguments for retrieving posts
    'contentAtoms'   => array(                          // Accepts a set of atoms for within the content
        'content' => array( 'type' => 'excerpt') 
    ),          
    'id'            => 'molecule-posts',                // Used to match requests for ajax
    'filter'        => false,                           // Adds a custom filter for a certain taxonomy. Accepts a certain taxonomy name in an array.
    'footerAtoms'   => array(                           // Accepts a set of atoms
        'button' => array( 'link' => 'post', 'title' => __('View post', 'components')) 
    ),                                           
    'headerAtoms'   => array(                           // Accepts a set of atoms
        'title' => array( 'tag' => 'h2', 'link' => 'post' ) 
    ),          
    'image'         => array( 'link' => 'post', 'size' => 'medium' ),
    'infinite'      => false,    
    'itemprop'      => '',
    'pagination'    => array('type' => 'numbers'),      // Pagination settings. If you remove this but have infinite enabled, infinite will break
    'postsGrid'     => '',                              // Accepts a custom grid class or pattern to display the thing into coloms
    'query'         => array(),                         // Accepts a custom query for posts. Pretty useful in existing WordPress templates. 
    'scheme'        => 'http://schema.org/BlogPosting', // Grand scheme
    'type'          => '',                              // Itemtype
    'view'          => 'list',                          // Type of display. Accepts list, grid or a custom class.
    'wrapper'       => ''                               // Wrapper class for our posts-wrapper
) );

// Query vars for pagination
if( get_query_var('paged') )
    $molecule['args']['paged'] = get_query_var('paged');

// Get our posts
if( ! $molecule['query'] )
    $molecule['query'] = new WP_Query( $molecule['args'] );

// Return if there are no posts to show
if( ! $molecule['query']->have_posts() )
    return;

// Set the query for our pagination
$molecule['pagination']['query'] = $molecule['query'];

// Output our arguments if we have a filter
if( $molecule['filter'] ) {
    add_action('wp_footer', function() use ($molecule) {
        echo '<script type="text/javascript"> var posts' . $molecule['id'] . '=' . json_encode($molecule) . ';</script>';
    });
}

// Ajax pagination
if( $molecule['ajax'] )
    $molecule['style'] .= ' molecule-posts-ajax'; 

// Display style
if( $molecule['view'] )
    $molecule['style'] .= ' molecule-posts-' . $molecule['view']; 

// Individal posts grid
if( $molecule['postsGrid'] )
    $molecule['wrapper'] .= ' components-grid-wrapper'; 

// Infinite scroll
if( $molecule['infinite'] ) 
    $molecule['style'] .= ' molecule-posts-infinite'; 

// Fallback if a users by accident removes the pagination
if( $molecule['infinite'] ) {
    $molecule['pagination']['size'] = 99999; 
    $molecule['pagination']['type'] = 'numbers'; 
}

// Alternate schemes for blogposting
if( strpos($molecule['scheme'], 'BlogPosting') ) {
    $molecule['itemprop']   = 'itemprop="blogPost"'; 
    $molecule['type']       = 'itemscope="itemscope" itemtype="http://schema.org/Blog"'; 
} 

// Key for counting grid patterns
$key = 0; ?>

<div class="molecule-posts <?php echo $molecule['style']; ?>" <?php echo $molecule['type']; ?> data-id="<?php echo $molecule['id']; ?>" <?php echo $molecule['inlineStyle']; ?>>
    
    <?php do_action( 'components_posts_before', $molecule ); ?>
    
    <?php 
        // Filter
        if( $molecule['filter'] ) { 
            Components\Build::atom( 'tags', $molecule['filter'] );
        } 
    ?>
    
    <div class="molecule-posts-wrapper <?php echo $molecule['wrapper']; ?>">
        
        <?php while ( $molecule['query']->have_posts() ) { ?>

            <?php
     
                // Set-up our post data
                $molecule['query']->the_post();
                $id = get_the_ID();
                
                // Allows for grid patterns
                if( $molecule['postsGrid'] ) {
                    $grid = is_array($molecule['postsGrid']) ? 'components-' . $molecule['postsGrid'][$key] . '-grid' : 'components-' . $molecule['postsGrid'] . '-grid';
                } else {
                    $grid = '';
                }
    
                $key++;
    
            ?>

            <article <?php post_class('molecule-post ' . $grid); ?> <?php echo $molecule['itemprop']; ?> itemscope="itemscope" itemtype="<?php echo $molecule['scheme']; ?>">

                <?php
                    // Actions at beginning of a post
                    do_action('components_posts_post_before', $id);

                    if( $molecule['image'] ) {
                        Components\Build::atom( 'image', $molecule['image'] );  
                    } 
                ?>
                
                <div class="molecule-post-content">
                    
                    <?php
                        // Header of this post                                
                        if( $molecule['headerAtoms'] ) { 
                    ?>
                        <header class="entry-header">    
                            <?php
                                foreach( $molecule['headerAtoms'] as $name => $variables ) { 

                                    Components\Build::atom( $name, $variables );

                                } 
                            ?>
                        </header>   

                    <?php

                        }                                  

                        // Header of this post                                
                        if( $molecule['contentAtoms'] ) { 
                    ?>
                        <div class="entry-content">    
                            <?php
                                foreach( $molecule['contentAtoms'] as $name => $variables ) { 

                                    Components\Build::atom( $name, $variables );

                                } 
                            ?>
                        </div>   

                    <?php

                        } 

                        // Footer of this post                                
                        if( $molecule['footerAtoms'] ) { 
                    ?>
                        <footer class="entry-footer">    
                            <?php
                                foreach( $molecule['footerAtoms'] as $name => $variables ) { 

                                    Components\Build::atom( $name, $variables );

                                } 
                            ?>
                        </footer>   

                    <?php } ?>
                    
                </div>
                
                <?php
                    // Actions at end of a post
                    do_action('components_posts_post_after', $id);
                ?>

            </article>

        <?php                                        
            } 
        ?>
        
    </div>

    <?php 
        // Pagination
        if( $molecule['pagination'] ) { 
            Components\Build::atom( 'pagination', $molecule['pagination'] );
        } 
    ?>
    
    <?php 
        // Reset our postdata so our main queries keep working well
        wp_reset_query(); 
    
        do_action( 'components_posts_after', $molecule ); 
    ?>
    
</div>