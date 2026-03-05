jQuery(document).ready(function($) {
    let searchTimeout;
    let currentPage = 1;
    const postsPerPage = 50;
    
    // Search posts
    $('#asap-posts-search-input').on('keyup', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val();
        
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            searchPosts(searchTerm, currentPage);
        }, 500);
    });
    
    // Load more posts
    $('#asap-load-more-posts').on('click', function() {
        currentPage++;
        const searchTerm = $('#asap-posts-search-input').val();
        searchPosts(searchTerm, currentPage, true);
    });
    
    function searchPosts(searchTerm, page, append = false) {
        const $selection = $('#asap-posts-selection');
        const $loadMoreBtn = $('#asap-load-more-posts');
        
        if (!append) {
            $selection.html('<div style="text-align: center; padding: 20px; color: #666;">Buscando...</div>');
        }
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asap_search_posts',
                search: searchTerm,
                page: page,
                per_page: postsPerPage,
                nonce: asapThickbox.nonce
            },
            success: function(response) {
                if (response.success) {
                    let html = '';
                    
                    if (response.data.posts.length === 0) {
                        if (page === 1) {
                            html = '<div style="text-align: center; padding: 20px; color: #666;">No se encontraron posts</div>';
                        }
                    } else {
                        response.data.posts.forEach(function(post) {
                            html += '<div>';
                            html += '<input id="post_' + post.ID + '" type="checkbox" name="checkfield[]" value="' + post.ID + '" />';
                            html += '<label for="post_' + post.ID + '">' + post.post_title + '</label>';
                            html += '</div>';
                        });
                    }
                    
                    if (append) {
                        $selection.append(html);
                    } else {
                        $selection.html(html);
                    }
                    
                    // Show/hide load more button
                    if (response.data.has_more) {
                        $loadMoreBtn.show();
                    } else {
                        $loadMoreBtn.hide();
                    }
                } else {
                    $selection.html('<div style="text-align: center; padding: 20px; color: #c00;">Error: ' + response.data + '</div>');
                }
            },
            error: function() {
                $selection.html('<div style="text-align: center; padding: 20px; color: #c00;">Error al buscar posts</div>');
            }
        });
    }
});

