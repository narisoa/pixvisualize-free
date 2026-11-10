/**
 * Client-side allowlist for the custom handle SVG.
 *
 * The SVG is injected with dangerouslySetInnerHTML, so it must be stripped of
 * scripts, event handlers and javascript: URLs even if a stored value somehow
 * bypassed the PHP sanitizer (Elementor data, imported content, older posts).
 */

const ALLOWED_TAGS = {
	svg: [
		'viewBox',
		'xmlns',
		'xmlns:xlink',
		'preserveAspectRatio',
		'width',
		'height',
		'fill',
		'stroke',
		'stroke-width',
		'role',
		'aria-hidden',
		'aria-label',
		'focusable',
		'class',
		'version',
	],
	g: [
		'fill',
		'stroke',
		'stroke-width',
		'stroke-linecap',
		'stroke-linejoin',
		'transform',
		'opacity',
		'fill-rule',
		'clip-rule',
		'clip-path',
		'class',
	],
	path: [
		'd',
		'fill',
		'stroke',
		'stroke-width',
		'stroke-linecap',
		'stroke-linejoin',
		'fill-rule',
		'clip-rule',
		'opacity',
		'class',
	],
	polygon: [
		'points',
		'fill',
		'stroke',
		'stroke-width',
		'fill-rule',
		'opacity',
	],
	polyline: [ 'points', 'fill', 'stroke', 'stroke-width', 'opacity' ],
	circle: [ 'cx', 'cy', 'r', 'fill', 'stroke', 'stroke-width', 'opacity' ],
	ellipse: [
		'cx',
		'cy',
		'rx',
		'ry',
		'fill',
		'stroke',
		'stroke-width',
		'opacity',
	],
	rect: [
		'x',
		'y',
		'width',
		'height',
		'rx',
		'ry',
		'fill',
		'stroke',
		'stroke-width',
		'opacity',
	],
	line: [
		'x1',
		'y1',
		'x2',
		'y2',
		'stroke',
		'stroke-width',
		'stroke-linecap',
		'opacity',
	],
	title: [],
	desc: [],
};

const isScriptedUrl = ( value ) =>
	/^\s*(?:javascript|vbscript|data):/i.test( value || '' );

const cleanNode = ( node ) => {
	const tag = node.tagName ? node.tagName.toLowerCase() : '';
	const allowedAttributes = ALLOWED_TAGS[ tag ];

	if ( ! allowedAttributes ) {
		node.remove();
		return;
	}

	Array.from( node.attributes ).forEach( ( attribute ) => {
		const name = attribute.name;

		if (
			! allowedAttributes.some(
				( allowed ) => allowed.toLowerCase() === name.toLowerCase()
			) ||
			/^on/i.test( name ) ||
			( /href/i.test( name ) && isScriptedUrl( attribute.value ) )
		) {
			node.removeAttribute( name );
		}
	} );

	Array.from( node.children ).forEach( cleanNode );
};

export const sanitizeSvg = ( svg ) => {
	if ( typeof svg !== 'string' || ! svg.trim() ) {
		return '';
	}

	if (
		typeof window === 'undefined' ||
		typeof window.DOMParser === 'undefined'
	) {
		return '';
	}

	const doc = new window.DOMParser().parseFromString(
		`<div>${ svg }</div>`,
		'text/html'
	);
	const holder = doc.body && doc.body.firstElementChild;
	const svgElement = holder && holder.querySelector( 'svg' );

	if ( ! svgElement ) {
		return '';
	}

	cleanNode( svgElement );

	return svgElement.outerHTML;
};
