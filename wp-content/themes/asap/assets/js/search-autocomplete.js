jQuery(document).ready(function($) {
    // Ocultar inicialmente los enlaces "Ver todos los resultados"
    $('#view-all-results, #view-all-results-home').hide();

    // Detecta la entrada en el campo #search-header
    $('#search-header').on('input', function() {
        var searchQuery = $(this).val();
        if (searchQuery.length > 2) {
            $.ajax({
                url: asap_vars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'asap_search_autocomplete',
                    query: searchQuery
                },
                success: function(data) {
                    $('#results-list').html(data);
                    var viewAllLink = $('#view-all-link');
                    var viewAllResults = $('#view-all-results');

                    // Comprueba si hay resultados
                    if ($('#results-list ul').length > 0) {
                        // Actualiza el enlace y muestra los botones y resultados para #search-header
                        viewAllLink.attr('href', asap_vars.siteUrl + '/?s=' + encodeURIComponent(searchQuery));
                        viewAllResults.show();
                    } else {
                        viewAllResults.hide();
                    }

                    $('#autocomplete-results').addClass('show');
                }
            });
        } else {
            $('#autocomplete-results').removeClass('show');
            $('#view-all-results').hide();
        }
    });

    // Detecta la entrada en el campo #search-home
    $('#search-home').on('input', function() {
        var searchQuery = $(this).val();
        if (searchQuery.length > 2) {
            $.ajax({
                url: asap_vars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'asap_search_autocomplete',
                    query: searchQuery
                },
                success: function(data) {
                    $('#results-list-home').html(data);
                    var viewAllLink = $('#view-all-link-home');
                    var viewAllResults = $('#view-all-results-home');

                    // Comprueba si hay resultados
                    if ($('#results-list-home ul').length > 0) {
                        // Actualiza el enlace y muestra los botones y resultados para #search-home
                        viewAllLink.attr('href', asap_vars.siteUrl + '/?s=' + encodeURIComponent(searchQuery));
                        viewAllResults.show();
                    } else {
                        viewAllResults.hide();
                    }

                    $('#autocomplete-results-home').addClass('show');
                }
            });
        } else {
            $('#autocomplete-results-home').removeClass('show');
            $('#view-all-results-home').hide();
        }
    });

    // Ocultar los resultados cuando se hace clic fuera de cualquier campo de búsqueda
    $(document).on('click', function(event) {
        if (!$(event.target).closest('#search-header, #search-home').length) {
            $('#autocomplete-results, #autocomplete-results-home').removeClass('show');
            $('#view-all-results, #view-all-results-home').hide();
        }
    });
});
