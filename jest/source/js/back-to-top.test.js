import * as fs from 'fs';
import main from '../../../source/js/main';
document.body.innerHTML = fs.readFileSync('templates/layouts/layout.html.twig');

describe('Scroll back to the top button', function () {
    test('Scroll back-to-top button is invisible at the top', function () {
        main();

        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
        window.onscroll();

        const backToTopButton = document.getElementById('back-to-top');

        expect(backToTopButton.style.display).toBe('none');
    });

    test('Scoll back-to-top button is visible above 20 from top', function () {
        main();

        document.body.scrollTop = 21;
        document.documentElement.scrollTop = 21;
        window.onscroll();

        const backToTopButton = document.getElementById('back-to-top');

        expect(backToTopButton.style.display).toBe('block');
    });

    test('Click on back-to-top button scrolls page to the top', function () {
        main();

        document.body.scrollTop = 100;
        document.documentElement.scrollTop = 100;
        window.onscroll();

        const backToTopButton = document.getElementById('back-to-top');

        backToTopButton.click();

        expect(document.body.scrollTop).toBe(0);
        expect(document.documentElement.scrollTop).toBe(0);
    });
});
