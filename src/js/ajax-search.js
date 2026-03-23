jQuery(document).ready(function($){
    let timer;

    $('#ajax-search-input').on('input', function(){
        const term = $(this).val().trim();

        clearTimeout(timer);
        if (term.length < 2) {
            $('#ajax-search-results').hide().empty();
            return;
        }
        
        timer = setTimeout(function(){
            $.post(
                '<?php echo esc_url( admin_url("admin-ajax.php") ); ?>',
                { action: 'global_search', term: term },
                function(data){
                    let html = '<ul class="ajax-search-list">';
                    data.forEach(item => {
                        if (item.image) {
                            html += `<li><a href="${item.permalink}"><img src="${item.image}" alt="${item.title}" /> ${item.title}</a></li>`;
                        } else {
                            html += `<li><a href="${item.permalink}">${item.title}</a></li>`;
                        }
                    });
                    html += '</ul>';
                    $('#ajax-search-results').html(html).show();
                }
            );
        }, 250);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#ajax-search-form').length) {
            $('#ajax-search-results').hide();
        }
    });
});