import Sidebar from '../../../source/js/sidebar';

function createSidebarDom(menuHtml = '') {
    document.body.innerHTML = `
        <button data-toggle="offcanvas"></button>
        <button class="toc-toggle"></button>
        <a class="toc-item" href="#toc-item"></a>
        <div class="row-offcanvas"></div>
        <div class="sidebar"></div>
        <div class="sidebar-sticky">
            <ul class="docs-menu">
                ${menuHtml}
            </ul>
        </div>
        <h1 class="section-header"><a href="#intro"></a></h1>
        <a class="project-version-switcher" href="/projects/orm/en/3.4/index.html#old"></a>
    `;
}

describe('Sidebar', function () {
    beforeEach(function () {
        window.history.pushState({}, '', '/');
        window.onhashchange = null;
        createSidebarDom();
    });

    test('normalizes menu ids from paths, hashes and separators', function () {
        const sidebar = Object.create(Sidebar.prototype);

        expect(sidebar.normalize('../reference/foo_bar.html#mapping/basics')).toBe(
            'reference-foo-bar-html-mapping-basics',
        );
    });

    test('removes q query parameter while preserving other query parameters and hash', function () {
        const sidebar = Object.create(Sidebar.prototype);

        expect(
            sidebar.removeQueryStringParameter(
                'q',
                'http://localhost/docs/search.html?foo=bar&q=entity+manager&page=2#install',
            ),
        ).toBe('http://localhost/docs/search.html?foo=bar&page=2#install');
    });

    test('toggles offcanvas sidebar when menu button is clicked', function () {
        new Sidebar();

        $('[data-toggle="offcanvas"]').trigger('click');

        expect($('.row-offcanvas').hasClass('active')).toBe(true);
        expect($('.sidebar').css('display')).toBe('none');

        $('[data-toggle="offcanvas"]').trigger('click');

        expect($('.row-offcanvas').hasClass('active')).toBe(false);
        expect($('.sidebar').css('display')).not.toBe('none');
    });

    test('clicks toc toggle when toc item is clicked', function () {
        const tocToggleClick = jest.fn();

        $('.toc-toggle').on('click', tocToggleClick);

        new Sidebar();

        $('.toc-item').trigger('click');

        expect(tocToggleClick).toHaveBeenCalled();
    });

    test('updates version switcher links with current location hash', function () {
        window.history.pushState({}, '', '/projects/orm/en/current.html#install');

        new Sidebar();

        expect($('.project-version-switcher').attr('href')).toBe(
            '/projects/orm/en/3.4/index.html#install',
        );
    });

    test('opens current docs menu item and its parents', function () {
        window.history.pushState(
            {},
            '',
            '/projects/orm/en/reference/getting-started.html#install',
        );

        createSidebarDom(`
            <li id="getting-started-html-intro" class="closed">
                <ul class="closed-ul">
                    <li id="getting-started-html-install">
                        <ul class="closed-ul">
                            <li id="getting-started-html-install-child"></li>
                        </ul>
                    </li>
                </ul>
            </li>
        `);

        new Sidebar();

        expect($('#getting-started-html-install').hasClass('opened')).toBe(true);
        expect($('#getting-started-html-intro').hasClass('opened')).toBe(true);
        expect($('#getting-started-html-install > ul').hasClass('opened-ul')).toBe(
            true,
        );
        expect($('#getting-started-html-install > ul').hasClass('closed-ul')).toBe(
            false,
        );
    });

    test('opens primary section when current hash does not match a menu item', function () {
        window.history.pushState({}, '', '/projects/orm/en/tutorial.html#missing');

        createSidebarDom(`
            <li id="tutorial-html-intro">
                <ul class="closed-ul">
                    <li id="tutorial-html-install"></li>
                </ul>
            </li>
        `);

        new Sidebar();

        expect($('#tutorial-html-intro').hasClass('opened')).toBe(true);
        expect($('#tutorial-html-intro > ul').hasClass('opened-ul')).toBe(true);
    });

    test('opens primary section when URL has no hash', function () {
        window.history.pushState({}, '', '/projects/orm/en/tutorial.html');

        createSidebarDom(`
            <li id="tutorial-html-intro">
                <ul class="closed-ul">
                    <li id="tutorial-html-install"></li>
                </ul>
            </li>
        `);

        new Sidebar();

        expect($('#tutorial-html-intro').hasClass('opened')).toBe(true);
        expect($('#tutorial-html-intro > ul').hasClass('opened-ul')).toBe(true);
    });

    test('ignores q query parameter when matching current menu item', function () {
        window.history.pushState(
            {},
            '',
            '/projects/orm/en/tutorial.html?q=entity+manager#intro',
        );

        createSidebarDom(`
            <li id="tutorial-html-intro">
                <ul class="closed-ul">
                    <li id="tutorial-html-install"></li>
                </ul>
            </li>
        `);

        new Sidebar();

        expect($('#tutorial-html-intro').hasClass('opened')).toBe(true);
    });

    test('reloads menu state and version links when hash changes', function () {
        window.history.pushState({}, '', '/projects/orm/en/tutorial.html#intro');

        createSidebarDom(`
            <li id="tutorial-html-intro" class="opened">
                <ul class="opened-ul"></ul>
            </li>
            <li id="tutorial-html-install"></li>
        `);

        new Sidebar();

        window.history.pushState({}, '', '/projects/orm/en/tutorial.html#install');
        window.onhashchange();

        expect($('#tutorial-html-intro').hasClass('opened')).toBe(false);
        expect($('#tutorial-html-install').hasClass('opened')).toBe(true);
        expect($('.project-version-switcher').attr('href')).toBe(
            '/projects/orm/en/3.4/index.html#install',
        );
    });
});
