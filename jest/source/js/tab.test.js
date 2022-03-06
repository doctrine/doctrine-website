import * as fs from 'fs';
import tabs from '../../../source/js/tab';
document.body.innerHTML = fs.readFileSync('source/projects.html');

describe('Project Tabs', function () {
    test('Active projects loads on default', function () {
        tabs();

        const activeProjectsTab = document.getElementById('active-projects-tab');
        const integrationProjectsTab = document.getElementById('integration-projects-tab');
        const inactiveProjectsTab = document.getElementById('inactive-projects-tab');

        expect(activeProjectsTab.className).toBe('nav-link active');
        expect(integrationProjectsTab.className).toBe('nav-link');
        expect(inactiveProjectsTab.className).toBe('nav-link');
    });

    test('Active tab selects on click', function () {
        tabs();

        const activeProjectsTab = document.getElementById('active-projects-tab');
        const integrationProjectsTab = document.getElementById('integration-projects-tab');
        const inactiveProjectsTab = document.getElementById('inactive-projects-tab');

        inactiveProjectsTab.click();
        activeProjectsTab.click();

        expect(activeProjectsTab.className).toBe('nav-link active');
        expect(integrationProjectsTab.className).toBe('nav-link');
        expect(inactiveProjectsTab.className).toBe('nav-link');

        expect(document.getElementById('active-projects').className).toContain('show active');
        expect(document.getElementById('integration-projects').className).not.toContain('show active');
        expect(document.getElementById('inactive-projects').className).not.toContain('show active');
    });

    test('Integration tab selects on click', function () {
        tabs();

        const activeProjectsTab = document.getElementById('active-projects-tab');
        const integrationProjectsTab = document.getElementById('integration-projects-tab');
        const inactiveProjectsTab = document.getElementById('inactive-projects-tab');

        integrationProjectsTab.click();

        expect(activeProjectsTab.className).toBe('nav-link');
        expect(integrationProjectsTab.className).toBe('nav-link active');
        expect(inactiveProjectsTab.className).toBe('nav-link');

        expect(document.getElementById('active-projects').className).not.toContain('show active');
        expect(document.getElementById('integration-projects').className).toContain('show active');
        expect(document.getElementById('inactive-projects').className).not.toContain('show active');
    });

    test('Inactive tab selects on click', function () {
        tabs();

        const activeProjectsTab = document.getElementById('active-projects-tab');
        const integrationProjectsTab = document.getElementById('integration-projects-tab');
        const inactiveProjectsTab = document.getElementById('inactive-projects-tab');

        inactiveProjectsTab.click();

        expect(activeProjectsTab.className).toBe('nav-link');
        expect(integrationProjectsTab.className).toBe('nav-link');
        expect(inactiveProjectsTab.className).toBe('nav-link active');

        expect(document.getElementById('active-projects').className).not.toContain('show active');
        expect(document.getElementById('integration-projects').className).not.toContain('show active');
        expect(document.getElementById('inactive-projects').className).toContain('show active');
    });
});
