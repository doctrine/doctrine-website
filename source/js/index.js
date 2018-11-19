import(/* webpackChunkName: "sentry" */ '@sentry/browser').then(module => {
    module.init({
        dsn: 'https://09ce137590054cfd8f0b7e9324d6ec14@sentry.io/1197701'
    });
});

import('bootstrap/js/src/index');
import(/* webpackChunkName: "main" */ './main').then(module => {
    module.default();
});

import(/* webpackChunkName: "search" */ './search').then(module => {
    module.default(projectSlug, versionSlug, searchBoxSettings);
});

if ($('#sidebar').length > 0) {
    import(/* webpackChunkName: "sidebar" */ './sidebar').then(module => {
        new module.default();
    });
}

window.googleTranslateElementInit = () => {
    console.log(window.$);
    $('#google_translate_element').html('');

    new google.translate.TranslateElement(
        {pageLanguage: 'en'}, 'google_translate_element'
    );

    $('#google_translate_element select').on('change', function() {
        var language = $('#google_translate_element select option:selected').text();

        googleAnalyticsEvent('Translate', 'click', language);
    });
};
$.getScript('https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit');