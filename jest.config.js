module.exports = {
    verbose: true,
    rootDir: './',
    testURL: 'http://localhost/',
    testMatch: ['**/jest/**/*.test.js'],
    "transform": {
        "^.+\\.js$": "babel-jest",
    },
    testEnvironment: 'jsdom'
};
