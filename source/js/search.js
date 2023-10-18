import instantsearch from 'instantsearch.js';
import algoliasearch from 'algoliasearch-helper';
import { searchBox, hits, configure } from 'instantsearch.js/es/widgets';

export default function (projectSlug, versionSlug, searchBoxSettings) {
  var searchParameters = {
    tagFilters: [],
    hitsPerPage: 5,
  };

  if (projectSlug) {
    searchParameters.tagFilters.push(projectSlug);
  }

  if (versionSlug) {
    searchParameters.tagFilters.push(versionSlug);
  }

  var search = instantsearch({
    indexName: 'pages',
    autofocus: false,
    poweredBy: false,
    reset: false,
    searchClient: algoliasearch(
        'YVYTFT9XMW',
        'a6dada5f33f148586b92cc3afefeaaf6'
    ),
    searchFunction: function (helper) {
      if (helper.state.query === '') {
        $('.search-results').hide();
        $('.container-wrapper').css('opacity', '1');

        return;
      }

      helper.search();

      $('.container-wrapper').css('opacity', '.25');
      $('.search-results').show();
    },
  });

  search.addWidget(configure(searchParameters));

  search.addWidget(searchBox(searchBoxSettings));

  search.addWidget(
    hits({
      container: '#hits',
      templates: {
        empty: 'No results',
        item: $('#instantsearch-template').html(),
      },
    }),
  );

  search.start();

  function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
  }

  $('#search-box input').on('blur', function () {
    setTimeout(function () {
      $('.container-wrapper').css('opacity', '1');
      $('.search-results').hide();
    }, 200);
  });

  $(function () {
    var q = getParameterByName('q', window.location.href);

    if (q) {
      $('#search-box input').val(q);
      search.helper.setQuery(q).search();
    }
  });
}
