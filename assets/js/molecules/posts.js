/**
 * Defines the custom header scripts
 */
module.exports.initialize = function() {
    
    jQuery('.molecule-posts').each( function(index) {
        
        var paginate = jQuery(this).find('.atom-pagination .page-numbers'),
            pagenumber = 1,
            postsHeight = jQuery(this).height,
            postsPosition = jQuery(this).offset().top,
            self = this,
            unique = jQuery(this).data('unique'),
            url = false;
        
        // Infinite scrolling
        if( jQuery(this).hasClass('do-infinite') ) {
            
            jQuery(window).scroll( function() {

                if( (jQuery(window).scrollTop() + jQuery(window).height()) > (postsPosition + postsHeight - 320) ) {

                    pageNumber++;

                    // Check our pagination and retrieve our urls
                    jQuery(paginate).each( function(index) {

                        if( jQuery(this).text() == pageNumber ) {
                            url = jQuery(this).attr('href');
                        }

                    });

                    // We've exceeded our urls
                    if( ! url ) {
                        return;
                    }

                    jQuery.get(url, function(data) {
                        var posts = jQuery(data).find('.molecule-posts[data-unique="' + unique + '"] .molecule-posts-post');

                        jQuery(self).find('.molecule-posts-wrapper').append(posts);

                    });                



                }

            });
            
        }
        
        
        // Normal Pagination
        if( jQuery(this).hasClass('do-ajax') ) {
        
            jQuery(this).on('click', '.atom-pagination a', function() {

                var current = jQuery(self).find('.atom-pagination current'),
                    currentPage = jQuery(current).text(),
                    page = jQuery(this).attr('href'),
                    pageCurrent = page.replace(/\/page\/[0-9]+/, '/page/' + currentPage );

                /**
                 * Update our pagination and add the right classes
                 */
                jQuery(current).replaceWith('<a class="page-numbers" href="' + pageCurrent + '">' + currentPage + '</a>');
                jQuery(this).addClass('current'); 

                // Load our data
                jQuery.get(page, function(data) {
                    var posts = jQuery(data).find('.molecule-posts[data-unique="' + unique + '"] .molecule-posts-post');

                    jQuery(self).find('.molecule-posts-wrapper').html(posts);

                });             

            });
            
        }
        
        // Filtering

        
        
    });      
        
};