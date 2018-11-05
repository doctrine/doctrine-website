window.String.prototype.replaceAll = function(search, replacement) {
    var target = this;

    return target.split(search).join(replacement);
};

var Sidebar = function() {
  this.loadCurrentDocsMenu();
  this.initVersionLinks();

  $('[data-toggle="offcanvas"]').click(function() {
    $('.row-offcanvas').toggleClass('active');
    $('.sidebar').toggle();
  });

  $('.toc-item').on('click', function() {
    $('.toc-toggle').click();
  });

  var self = this;

  window.onhashchange = function() {
    self.loadCurrentDocsMenu();
    self.initVersionLinks();
  };
};

Sidebar.prototype.initVersionLinks = function() {
  $('.project-version-switcher').each(function() {
    var linkUrl = $(this)
      .attr('href')
      .split('#')[0];

    $(this).attr('href', linkUrl + window.location.hash);
  });
};

Sidebar.prototype.getTopLevelParent = function(elem) {
  // scroll menu to element
  var topLevelParent = elem
    .parent('ul')
    .prev('li')
    .parent('ul')
    .prev('li');

  if (!topLevelParent.length) {
    topLevelParent = elem.parent('ul').prev('li');
  }

  if (!topLevelParent.length) {
    topLevelParent = elem;
  }

  return topLevelParent;
};

Sidebar.prototype.scrollToElement = function(elem) {
  var topLevelParent = this.getTopLevelParent(elem);

  var topElem = document.getElementById(topLevelParent.attr('id'));

  if (topElem) {
    var offsetTop = topElem.offsetTop;

    $('.sidebar-sticky').scrollTop(offsetTop);
  }
};

Sidebar.prototype.removeQueryStringParameter = function(key, url) {
  if (!url) url = window.location.href;

  var hashParts = url.split('#');

  var regex = new RegExp('([?&])' + key + '=.*?(&|#|$)', 'i');

  if (hashParts[0].match(regex)) {
    //REMOVE KEY AND VALUE
    url = hashParts[0].replace(regex, '$1');

    //REMOVE TRAILING ? OR &
    url = url.replace(/([?&])$/, '');

    //ADD HASH
    if (typeof hashParts[1] !== 'undefined' && hashParts[1] !== null)
      url += '#' + hashParts[1];
  }

  return url;
};

Sidebar.prototype.getCurrentDocsMenu = function() {
  var currentUrl = this.removeQueryStringParameter('q', window.location.href);
  var lastPart = currentUrl.substr(currentUrl.lastIndexOf('/') + 1);
  var primaryHash = $('h1.section-header a').attr('href');

  if (!lastPart) {
    lastPart = 'index.html';
  }

  if (lastPart[0] === '#') {
    lastPart = 'index.html' + lastPart;
  }

  if (!window.location.hash) {
    lastPart = lastPart + primaryHash;
  }

  var id = this.normalize(lastPart);
  var lastPartWithPrimaryHash =
    lastPart.substr(0, lastPart.lastIndexOf('#')) + primaryHash;
  var idWithPrimaryHash = this.normalize(lastPartWithPrimaryHash);

  // try the current link with the hash
  var currentDocsMenu = $('#' + id);

  // if we can't find it, open the primary menu item for this page
  if (!currentDocsMenu.length) {
    currentDocsMenu = $('#' + idWithPrimaryHash);
  }

  return currentDocsMenu;
};

Sidebar.prototype.normalize = function(string) {
  return string
    .replaceAll('../', '')
    .replaceAll('#', '-')
    .replaceAll('.', '-')
    .replaceAll('/', '-')
    .replaceAll('_', '-');
};

Sidebar.prototype.closeAll = function() {
  $('.opened').removeClass('opened');
  $('.opened-ul').removeClass('opened-ul');
};

Sidebar.prototype.openElement = function(elem) {
  elem.addClass('opened');

  // top level clicked, open children
  elem
    .find('ul')
    .first()
    .addClass('opened-ul')
    .removeClass('closed-ul');

  // child clicked, open parents
  elem
    .parents('ul')
    .addClass('opened-ul')
    .removeClass('closed-ul');
  elem
    .parents('ul')
    .parent('li')
    .addClass('opened');
};

Sidebar.prototype.loadCurrentDocsMenu = function() {
  var currentDocsMenu = this.getCurrentDocsMenu();

  if (!currentDocsMenu.length) {
    return;
  }

  this.closeAll();

  this.openElement(currentDocsMenu);

  this.scrollToElement(currentDocsMenu);
};

export default Sidebar;
