import path from 'node:path';
import { spawnSync } from 'node:child_process';

const wpScripts = path.resolve(
	process.cwd(),
	'node_modules',
	'@wordpress',
	'scripts',
	'bin',
	'wp-scripts.js'
);
const args = [ wpScripts, 'build', '--webpack-copy-php', '--blocks-manifest' ];

const result = spawnSync( process.execPath, args, {
	stdio: 'inherit',
	env: { ...process.env, PIXVISUALIZE_FREE: 'true' },
} );

if ( result.error ) {
	process.stderr.write( `${ result.error }\n` );
}

process.exit( result.status ?? 1 );
