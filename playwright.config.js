// @ts-check
const { defineConfig, devices } = require( '@playwright/test' );

module.exports = defineConfig( {
	testDir: './tests/e2e',
	fullyParallel: false,
	retries: 0,
	reporter: [ [ 'list' ] ],
	use: {
		baseURL: 'http://localhost:8888',
		trace: 'on',
		video: 'on',
		screenshot: 'only-on-failure',
	},
	projects: [
		{
			name: 'chromium',
			use: { ...devices[ 'Desktop Chrome' ] },
		},
	],
} );
