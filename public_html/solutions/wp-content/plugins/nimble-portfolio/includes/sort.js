jQuery(document).ready(function($) {
    
    // Portfolio Filtering
    $('.nimble-portfolio .-filter').click(function(e) {

        e.preventDefault();

        $('.nimble-portfolio .-filter.active').removeClass('active');
        $(this).addClass('active');

        var filterVal = $(this).data('rel');
        if (filterVal === '*') {
            $('.nimble-portfolio .-item.hidden').fadeIn('normal').removeClass('hidden');
        } else {
            $('.nimble-portfolio .-item').each(function() {
                if (!$(this).hasClass(filterVal)) {
                    $(this).fadeOut('slow').addClass('hidden');
                } else {
                    $(this).fadeIn('slow').removeClass('hidden');
                }
            });
        }

        // Apply lightbox gallery only to current items
        $('.nimble-portfolio').trigger("nimble_portfolio_lightbox", {items: $("a[data-rel^='nimblebox']", ".nimble-portfolio .-item:not(.hidden)")});
    });

    // DOM loaded, remove the loading animation
    $('.nimble-portfolio').removeClass("-isloading");
    
    // Apply lightbox gallery
    $('.nimble-portfolio').trigger("nimble_portfolio_lightbox", {items: $("a[data-rel^='nimblebox']")});

});
