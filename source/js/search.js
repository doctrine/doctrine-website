var Search = function(projectSlug, versionSlug) {
    var searchParameters = {
        tagFilters: [],
        hitsPerPage: 5
    };

    if (projectSlug) {
        searchParameters.tagFilters.push(projectSlug);
    }

    if (versionSlug) {
        searchParameters.tagFilters.push(versionSlug);
    }

    var search = instantsearch({
        appId: 'YVYTFT9XMW',
        apiKey: 'a6dada5f33f148586b92cc3afefeaaf6',
        indexName: 'pages',
        autofocus: false,
        poweredBy: false,
        reset: false,
        searchParameters: searchParameters,
        searchFunction: function(helper) {
            if (helper.state.query === "") {
                $('.search-results').hide();
                $('.container-wrapper').css('opacity', '1');

                return;
            }

            helper.search();

            $('.container-wrapper').css('opacity', '.25');
            $('.search-results').show();
        }
    });

    search.addWidget(
        instantsearch.widgets.searchBox({
            container: '#search-box',
            placeholder: 'Search'
        })
    );

    search.addWidget(
        instantsearch.widgets.hits({
            container: '#hits',
            templates: {
                empty: 'No results',
                item: $('#instantsearch-template').html()
            }
        })
    );

    search.start();

    $('#search-box input').on('blur', function() {
        setTimeout(function() {
            $('.container-wrapper').css('opacity', '1');
            $('.search-results').hide();
        }, 200);
    });
};
