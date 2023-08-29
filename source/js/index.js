import(/* webpackChunkName: "sentry" */ '@sentry/browser').then((module) => {
  module.init({
    dsn: 'https://09ce137590054cfd8f0b7e9324d6ec14@sentry.io/1197701',
  });
});

import 'bootstrap/js/dist/index';

import(/* webpackChunkName: "main" */ './main').then((module) => {
  module.default();
});

import(/* webpackChunkName: "search" */ './search').then((module) => {
  module.default(projectSlug, versionSlug, searchBoxSettings);
});

if ($('#sidebar').length > 0) {
  import(/* webpackChunkName: "sidebar" */ './sidebar').then((module) => {
    new module.default();
  });
}

import(/* webpackChunkName: "tab" */ './tab').then((module) => {
  module.default();
});
