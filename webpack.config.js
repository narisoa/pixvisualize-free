const path = require( 'path' );
const webpack = require( 'webpack' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const isFreeBuild = process.env.PIXVISUALIZE_FREE === 'true';

const getDefaultEntry = () =>
	typeof defaultConfig.entry === 'function'
		? defaultConfig.entry()
		: { ...defaultConfig.entry };

const freeEntry = () => {
	const entry = getDefaultEntry();
	const viewKey = Object.keys( entry ).find( ( key ) =>
		key.endsWith( '/view' )
	);

	if ( viewKey ) {
		entry[ viewKey ] = path.resolve(
			__dirname,
			'src/pixvisualize/view-free.js'
		);
	}

	return entry;
};

const plugins = [
	...defaultConfig.plugins,
	new webpack.DefinePlugin( {
		'process.env.PIXVISUALIZE_FREE': JSON.stringify( isFreeBuild ),
	} ),
];

if ( isFreeBuild ) {
	plugins.push(
		new webpack.NormalModuleReplacementPlugin(
			/components[\\/]CompareSlider$/,
			path.resolve(
				__dirname,
				'src/pixvisualize/components/CompareSliderFree.jsx'
			)
		)
	);
}

module.exports = {
	...defaultConfig,
	entry: isFreeBuild ? freeEntry : defaultConfig.entry,
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, isFreeBuild ? 'build-free' : 'build' ),
	},
	plugins,
};
