/**
 * Pick a readable foreground (black or white) for a given background color.
 * Accepts #RRGGBB, #RRGGBBAA and rgba()/rgb() strings.
 *
 * @param {string} color Background color.
 * @return {string} '#000000' or '#ffffff'.
 */
export const contrastColor = ( color ) => {
	if ( typeof color !== 'string' ) {
		return '#ffffff';
	}

	let r;
	let g;
	let b;

	const trimmed = color.trim();

	if ( trimmed.startsWith( '#' ) ) {
		let hex = trimmed.slice( 1 );

		if ( hex.length === 3 ) {
			hex = hex
				.split( '' )
				.map( ( c ) => c + c )
				.join( '' );
		}

		if ( hex.length < 6 ) {
			return '#ffffff';
		}

		r = parseInt( hex.slice( 0, 2 ), 16 );
		g = parseInt( hex.slice( 2, 4 ), 16 );
		b = parseInt( hex.slice( 4, 6 ), 16 );
	} else {
		const match = trimmed.match(
			/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)(?:\s*[,)]|\s*,\s*[\d.]+\))/i
		);

		if ( ! match ) {
			return '#ffffff';
		}

		r = parseInt( match[ 1 ], 10 );
		g = parseInt( match[ 2 ], 10 );
		b = parseInt( match[ 3 ], 10 );
	}

	const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;

	return luminance > 150 ? '#000000' : '#ffffff';
};
