String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

function loadCurrentDocsMenu() {
    console.log('Trying to find menu');

    var currentUrl = window.location.href;
    var lastPart = currentUrl.substr(currentUrl.lastIndexOf('/') + 1);

    if (!lastPart) {
        return;
    }

    if (!window.location.hash) {
        lastPart = lastPart + '#title.1';
    }

    var id = lastPart.replaceAll('#', '-').replaceAll('.', '-');

    console.log(id);

    // close open stuff
    $('.opened').removeClass('opened');
    $('.opened-ul').removeClass('opened-ul').addClass('closed-ul');

    var elem = $('#' + id);

    elem.addClass('opened');
    elem.parent('ul').prev('li').addClass('opened');
    elem.parent('ul').prev('li').parent('ul').prev('li').addClass('opened');

    // open parents
    elem.next('ul').addClass('opened-ul');
    elem.parents('ul').addClass('opened-ul');

}

$(function() {
    loadCurrentDocsMenu();

    $('.project-version-switcher').each(function() {
        $(this).attr('href', $(this).attr('href') + window.location.hash);
    });
});

window.onhashchange = loadCurrentDocsMenu;

