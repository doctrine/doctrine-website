var Sidebar = function() {
    this.loadCurrentDocsMenu();
    this.initVersionLinks();

    $('[data-toggle="offcanvas"]').click(function () {
        $('.row-offcanvas').toggleClass('active')
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
        var linkUrl = $(this).attr('href').split('#')[0];

        $(this).attr('href', linkUrl + window.location.hash);
    });
};

Sidebar.prototype.getTopLevelParent = function(elem) {
    // scroll menu to element
    var topLevelParent = elem.parent('ul').prev('li').parent('ul').prev('li');

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

Sidebar.prototype.getCurrentDocsMenu = function() {
    var currentUrl = window.location.href;
    var lastPart = currentUrl.substr(currentUrl.lastIndexOf('/') + 1);

    if (!lastPart) {
        lastPart = 'index.html';
    }

    if (lastPart[0] === '#') {
        lastPart = 'index.html' + lastPart;
    }

    if (!window.location.hash) {
        lastPart = lastPart + '#title.1';
    }

    var id = lastPart.replaceAll('#', '-').replaceAll('.', '-');

    return $('#' + id);
};

Sidebar.prototype.closeAll = function() {
    $('.opened').removeClass('opened');
    $('.opened-ul').removeClass('opened-ul');
};

Sidebar.prototype.openElement = function(elem) {
    elem.addClass('opened');

    // top level clicked, open children
    elem.next('ul').addClass('opened-ul').removeClass('closed-ul');

    // child clicked, open parents
    elem.parents('ul').addClass('opened-ul').removeClass('closed-ul');
    elem.parents('ul').prev('li').addClass('opened');
};

Sidebar.prototype.loadCurrentDocsMenu = function() {
    var elem = this.getCurrentDocsMenu();

    if (!elem) {
        return;
    }

    this.closeAll();

    this.openElement(elem);

    this.scrollToElement(elem);
};
