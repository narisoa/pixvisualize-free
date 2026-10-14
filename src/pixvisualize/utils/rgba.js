/**
 * Build a CSS rgba() color from a hex / rgb color and a 0-100 opacity.
 *
 * @param {string} color   CSS color (hex or rgb/rgba).
 * @param {number} opacity Opacity from 0 to 100.
 * @return {string} rgba() color string; falls back to the input color.
 */
export const toRgba = ( color, opacity ) => {
	const alpha = Math.max( 0, Math.min( 1, opacity / 100 ) );
	const c = ( color || '' ).trim();
	let r = 0;
	let g = 0;
	let b = 0;

	if ( c.startsWith( '#' ) ) {
		let hex = c.slice( 1 );
		if ( hex.length === 3 ) {
			hex = hex
				.split( '' )
				.map( ( ch ) => ch + ch )
				.join( '' );
		}
		if ( hex.length >= 6 ) {
			r = parseInt( hex.slice( 0, 2 ), 16 );
			g = parseInt( hex.slice( 2, 4 ), 16 );
			b = parseInt( hex.slice( 4, 6 ), 16 );
			return `rgba(${ r }, ${ g }, ${ b }, ${ alpha })`;
		}
	}

	const m = c.match( /rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i );
	if ( m ) {
		return `rgba(${ m[ 1 ] }, ${ m[ 2 ] }, ${ m[ 3 ] }, ${ alpha })`;
	}

	return c;
};

export default toRgba;
