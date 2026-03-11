module.exports = {
    verbose: true,
    rootDir: './',
    testEnvironmentOptions: {
        url: 'http://localhost/'
    },
    testMatch: ['**/jest/**/*.test.js'],
    "transform": {
        "^.+\\.js$": "babel-jest",
    },
    "setupFiles": ["./jest/setup/jest.js"],
    testEnvironment: 'jsdom'
};
