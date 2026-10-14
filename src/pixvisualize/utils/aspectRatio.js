export const parseAspectRatio = ( value ) => {
	const [ w, h ] = String( value || '16/9' )
		.split( '/' )
		.map( Number );
	if ( ! w || ! h ) {
		return { w: 16, h: 9 };
	}
	return { w, h };
};

export const heightToWidth = ( height, ratio ) => {
	const { w, h } = parseAspectRatio( ratio );
	return Math.round( ( height * w ) / h );
};

export const widthToHeight = ( width, ratio ) => {
	const { w, h } = parseAspectRatio( ratio );
	return Math.round( ( width * h ) / w );
};
