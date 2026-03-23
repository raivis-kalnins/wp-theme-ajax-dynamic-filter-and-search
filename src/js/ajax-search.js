jQuery(document).ready(function($){
    let ajaxTimeout;

    $('#ajax-search-input').on('input', function(){
        const term = $(this).val();

        clearTimeout(ajaxTimeout);
        if(term.length < 2){
            $('#ajax-search-results').hide().empty();
            return;
        }

        ajaxTimeout = setTimeout(function(){
            $.post(
                '<?php echo admin_url("admin-ajax.php"); ?>',
                { action: 'global_search', term: term },
                function(data){
                    let html = '<ul class="ajax-search-list">';
                    data.forEach(item => {
                        if(item.image){
                            html += `<li><a href="${item.permalink}"><img src="${item.image}" alt="${item.title}">${item.title}</a></li>`;
                        } else {
                            html += `<li><a href="${item.permalink}">${item.title}</a></li>`;
                        }
                    });
                    html += '</ul>';
                    $('#ajax-search-results').html(html).show();
                }
            );
        }, 300); // debounce
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e){
        if(!$(e.target).closest('#ajax-search-form').length){
            $('#ajax-search-results').hide();
        }
    });
});