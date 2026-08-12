import main from '../../../source/js/main';

function createMainDom() {
    document.body.innerHTML = `
        <button id="back-to-top"></button>

        <div class="tabs">
            <div role="tablist">
                <button role="tab" aria-controls="tab-one" aria-selected="true" data-active="true"></button>
                <button role="tab" aria-controls="tab-two" aria-selected="false"></button>
            </div>
            <div id="tab-one" role="tabpanel">Tab one</div>
            <div id="tab-two" role="tabpanel" style="display: none;">Tab two</div>
        </div>

        <pre id="code-example">first line<span>second line</span><span>third line</span></pre>
        <button class="copy-to-clipboard" data-copy-element-id="code-example"></button>

        <a
            id="tracked-link"
            href="/tracked"
            data-ga-category="Navigation"
            data-ga-action="Click"
            data-ga-label="Docs"
            data-ga-value="4"
            data-ga-fields-object='{"transport":"beacon"}'
        ></a>
        <a id="untracked-link" href="/untracked" data-ga-category="Navigation"></a>
    `;
}

function ready() {
    return new Promise((resolve) => {
        $(resolve);
    });
}

describe('Main JavaScript', function () {
    beforeEach(function () {
        createMainDom();
        window.onscroll = null;
        delete window.ga;

        Object.defineProperty(navigator, 'clipboard', {
            configurable: true,
            value: {
                writeText: jest.fn(() => Promise.resolve()),
            },
        });
    });

    test('activates selected tab and hides sibling tab panels', async function () {
        main();
        await ready();

        $('button[aria-controls="tab-two"]').trigger('click');

        expect($('button[aria-controls="tab-one"]').attr('data-active')).toBeUndefined();
        expect($('button[aria-controls="tab-one"]').attr('aria-selected')).toBe(
            'false',
        );
        expect($('button[aria-controls="tab-two"]').attr('data-active')).toBe(
            'true',
        );
        expect($('button[aria-controls="tab-two"]').attr('aria-selected')).toBe(
            'true',
        );
        expect($('#tab-one').css('display')).toBe('none');
        expect($('#tab-two').css('display')).not.toBe('none');
    });

    test('copies child text content separated by newlines', async function () {
        main();
        await ready();

        $('button.copy-to-clipboard').trigger('click');
        await Promise.resolve();

        expect(navigator.clipboard.writeText).toHaveBeenCalledWith(
            'first line\nsecond line\nthird line',
        );
    });

    test('copies empty text when copy target does not exist', async function () {
        $('button.copy-to-clipboard').data('copyElementId', 'missing-element');

        main();
        await ready();

        $('button.copy-to-clipboard').trigger('click');
        await Promise.resolve();

        expect(navigator.clipboard.writeText).toHaveBeenCalledWith('');
    });

    test('sends google analytics event when tracked link has category and action', async function () {
        window.ga = jest.fn();
        window.ga.create = jest.fn();

        main();
        await ready();

        $('#tracked-link').trigger('click');

        expect(window.ga).toHaveBeenCalledWith(
            'send',
            'event',
            'Navigation',
            'Click',
            'Docs',
            4,
            { transport: 'beacon' },
        );
    });

    test('does not send google analytics event without category and action', async function () {
        window.ga = jest.fn();
        window.ga.create = jest.fn();

        main();
        await ready();

        $('#untracked-link').trigger('click');

        expect(window.ga).not.toHaveBeenCalled();
    });

    test('does not bind google analytics events when ga is unavailable', async function () {
        main();
        await ready();

        $('#tracked-link').trigger('click');

        expect(window.ga).toBeUndefined();
    });
});
