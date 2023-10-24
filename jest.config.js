module.exports = {
    verbose: true,
    rootDir: './',
    testURL: 'http://localhost/',
    testMatch: ['**/jest/**/*.test.js'],
    "transform": {
        "^.+\\.js$": "babel-jest",
    },
    "setupFiles": ["./jest/setup/jest.js"],
    testEnvironment: 'jsdom'
};
