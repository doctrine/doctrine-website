import search from '../../../source/js/search';

let mockInstantsearchInstance;
let mockInstantsearchOptions;
let mockHelperSearch;

jest.mock('algoliasearch', () => jest.fn(() => 'algolia-client'));

jest.mock('instantsearch.js', () => {
    return jest.fn((options) => {
        mockInstantsearchOptions = options;
        mockHelperSearch = jest.fn();

        mockInstantsearchInstance = {
            addWidget: jest.fn(),
            start: jest.fn(),
            helper: {
                setQuery: jest.fn(() => ({
                    search: mockHelperSearch,
                })),
            },
        };

        return mockInstantsearchInstance;
    });
});

jest.mock('instantsearch.js/es/widgets', () => ({
    configure: jest.fn((options) => ({
        type: 'configure',
        options,
    })),
    searchBox: jest.fn((options) => ({
        type: 'searchBox',
        options,
    })),
    hits: jest.fn((options) => ({
        type: 'hits',
        options,
    })),
}));

import instantsearch from 'instantsearch.js';
import algoliasearch from 'algoliasearch';
import { configure, searchBox, hits } from 'instantsearch.js/es/widgets';

describe('Algolia Search', function () {
    beforeEach(function () {
        jest.clearAllMocks();
        jest.useFakeTimers();

        document.body.innerHTML = `
            <div class="search-results"></div>
            <div class="container-wrapper"></div>
            <div id="search-box">
                <input />
            </div>
            <div id="hits"></div>
            <script id="instantsearch-template" type="text/template">
                <div class="hit">{{ title }}</div>
            </script>
        `;

        window.history.pushState({}, '', '/');
    });

    afterEach(function () {
        jest.useRealTimers();
    });

    test('initializes instantsearch correctly', function () {
        search();

        expect(algoliasearch).toHaveBeenCalledWith(
            'YVYTFT9XMW',
            'a6dada5f33f148586b92cc3afefeaaf6',
        );

        expect(instantsearch).toHaveBeenCalledWith(
            expect.objectContaining({
                indexName: 'pages',
                autofocus: false,
                poweredBy: false,
                reset: false,
                searchClient: 'algolia-client',
                searchFunction: expect.any(Function),
            }),
        );

        expect(mockInstantsearchInstance.start).toHaveBeenCalled();
    });

    test('adds configure, searchBox and hits widgets', function () {
        const searchBoxSettings = {
            container: '#search-box',
            placeholder: 'Search',
        };

        search('orm', '3.4', searchBoxSettings);

        expect(configure).toHaveBeenCalledWith({
            tagFilters: ['orm', '3.4'],
            hitsPerPage: 5,
        });

        expect(searchBox).toHaveBeenCalledWith(searchBoxSettings);

        expect(hits).toHaveBeenCalledWith({
            container: '#hits',
            templates: {
                empty: 'No results',
                item: expect.stringContaining('{{ title }}'),
            },
        });

        expect(mockInstantsearchInstance.addWidget).toHaveBeenCalledTimes(3);
    });

    test('does not add empty project or version slugs as tag filters', function () {
        search(null, null, {
            container: '#search-box',
        });

        expect(configure).toHaveBeenCalledWith({
            tagFilters: [],
            hitsPerPage: 5,
        });
    });

    test('adds only project slug as tag filter when version slug is empty', function () {
        search('orm', null, {
            container: '#search-box',
        });

        expect(configure).toHaveBeenCalledWith({
            tagFilters: ['orm'],
            hitsPerPage: 5,
        });
    });

    test('adds only version slug as tag filter when project slug is empty', function () {
        search(null, '3.4', {
            container: '#search-box',
        });

        expect(configure).toHaveBeenCalledWith({
            tagFilters: ['3.4'],
            hitsPerPage: 5,
        });
    });

    test('hides search results when query is empty', function () {
        search();

        const helper = {
            state: {
                query: '',
            },
            search: jest.fn(),
        };

        $('.search-results').show();
        $('.container-wrapper').css('opacity', '.25');

        mockInstantsearchOptions.searchFunction(helper);

        expect(helper.search).not.toHaveBeenCalled();
        expect($('.search-results').css('display')).toBe('none');
        expect($('.container-wrapper').css('opacity')).toBe('1');
    });

    test('shows search results and searches when query is not empty', function () {
        search();

        const helper = {
            state: {
                query: 'orm',
            },
            search: jest.fn(),
        };

        $('.search-results').hide();
        $('.container-wrapper').css('opacity', '1');

        mockInstantsearchOptions.searchFunction(helper);

        expect(helper.search).toHaveBeenCalled();
        expect($('.search-results').css('display')).not.toBe('none');
        expect($('.container-wrapper').css('opacity')).toBe('0.25');
    });

    test('hides search results when search input loses focus', function () {
        search();

        $('.search-results').show();
        $('.container-wrapper').css('opacity', '.25');

        $('#search-box input').trigger('blur');

        jest.advanceTimersByTime(199);

        expect($('.search-results').css('display')).not.toBe('none');
        expect($('.container-wrapper').css('opacity')).toBe('0.25');

        jest.advanceTimersByTime(1);

        expect($('.search-results').css('display')).toBe('none');
        expect($('.container-wrapper').css('opacity')).toBe('1');
    });

    test('uses q query parameter to prefill and execute search', function () {
        window.history.pushState({}, '', '/?q=entity+manager');

        search();

        $(() => {
            expect($('#search-box input').val()).toBe('entity manager');
            expect(
                mockInstantsearchInstance.helper.setQuery,
            ).toHaveBeenCalledWith('entity manager');
            expect(mockHelperSearch).toHaveBeenCalled();
        });
    });

    test('does not prefill or execute search when q query parameter is missing', function () {
        search();

        $(() => {
            expect($('#search-box input').val()).toBe('');
            expect(
                mockInstantsearchInstance.helper.setQuery,
            ).not.toHaveBeenCalled();
        });
    });
});
