( function () {
	'use strict';

	/* global MutationObserver */

	function getAttr( el, name, fallback ) {
		const value = el.getAttribute( name );
		return value === null || value === '' ? fallback : value;
	}

	// Contain layout (mirrors src/pixvisualize/utils/contain.js used by the
	// front view-free.js): the slider always fits inside its parent (width AND
	// fixed height when there is one) with the ratio respected. The template
	// only sets width + aspect-ratio (a fixed height would over-constrain CSS
	// and the ratio would be ignored in narrow columns); this pass caps the
	// height when an ancestor has an explicit fixed height.
	function parseRatio( ratio ) {
		const parts = String( ratio || '16/9' ).split( '/' );
		const w = parseFloat( parts[ 0 ] ) || 16;
		const h = parseFloat( parts[ 1 ] ) || 9;
		return { w, h };
	}

	function findFixedParentHeight( element, maxDepth ) {
		maxDepth = maxDepth || 8;
		if ( ! element || ! element.parentElement ) {
			return Infinity;
		}
		const isExplicit = function ( value ) {
			return (
				!! value &&
				value !== 'auto' &&
				value !== '' &&
				value !== '0px' &&
				value !== 'none' &&
				value.indexOf( '%' ) === -1
			);
		};
		let node = element.parentElement;
		let depth = 0;
		while ( node && node.nodeType === 1 && depth < maxDepth ) {
			const inline = node.style || {};
			const hasInlineFixed =
				isExplicit( inline.height ) || isExplicit( inline.minHeight );
			const hasFullHeightClass =
				node.classList &&
				typeof node.classList.contains === 'function' &&
				node.classList.contains( 'elementor-section-height-full' );
			if (
				( hasInlineFixed || hasFullHeightClass ) &&
				node.clientHeight > 0
			) {
				return node.clientHeight;
			}
			node = node.parentElement;
			depth += 1;
		}
		return Infinity;
	}

	function containSize( desiredHeight, ratio, availWidth, availHeight ) {
		const ref = parseRatio( ratio );
		const safeAvailWidth =
			typeof availWidth === 'number' &&
			isFinite( availWidth ) &&
			availWidth > 0
				? availWidth
				: null;
		const cappedHeight =
			typeof availHeight === 'number' &&
			isFinite( availHeight ) &&
			availHeight > 0
				? Math.min( desiredHeight, availHeight )
				: desiredHeight;
		if ( ! safeAvailWidth ) {
			return {
				width: Math.round( ( cappedHeight * ref.w ) / ref.h ),
				height: Math.round( cappedHeight ),
			};
		}
		const widthForHeight = ( cappedHeight * ref.w ) / ref.h;
		if ( widthForHeight <= safeAvailWidth ) {
			return {
				width: Math.round( widthForHeight ),
				height: Math.round( cappedHeight ),
			};
		}
		return {
			width: Math.round( safeAvailWidth ),
			height: Math.round( ( safeAvailWidth * ref.h ) / ref.w ),
		};
	}

	function syncContainSizes() {
		const containers = document.querySelectorAll(
			'.pixvisualize-elementor-preview'
		);
		for ( let i = 0; i < containers.length; i++ ) {
			const container = containers[ i ];
			if (
				! container.querySelector(
					'.pixvisualize-elementor-preview__image'
				)
			) {
				continue;
			}
			const ratio = getAttr( container, 'data-aspect-ratio', '16/9' );
			const rawHeight = parseInt(
				getAttr( container, 'data-height', '450' ),
				10
			);
			const desiredHeight = isFinite( rawHeight )
				? Math.min( 2000, Math.max( 100, rawHeight ) )
				: 450;
			const card =
				container.parentElement &&
				container.parentElement.classList &&
				typeof container.parentElement.classList.contains ===
					'function' &&
				container.parentElement.classList.contains(
					'pixvisualize-generator-card'
				)
					? container.parentElement
					: null;
			const measureNode = card
				? card.parentElement
				: container.parentElement;
			const availWidth =
				measureNode && measureNode.clientWidth
					? measureNode.clientWidth
					: null;
			const availHeight = findFixedParentHeight( container );
			const contained = containSize(
				desiredHeight,
				ratio,
				availWidth,
				availHeight
			);
			if ( card ) {
				card.style.width = 'min(100%, ' + contained.width + 'px)';
				card.style.margin = '0 auto';
				card.style.maxWidth = '100%';
				container.style.width = '100%';
			} else {
				container.style.width = 'min(100%, ' + contained.width + 'px)';
				container.style.margin = '0 auto';
			}
			container.style.height = contained.height + 'px';
			container.style.aspectRatio = ratio;
		}
	}

	let containTimer = null;
	function debouncedContain() {
		if ( containTimer !== null ) {
			clearTimeout( containTimer );
		}
		containTimer = setTimeout( syncContainSizes, 100 );
	}

	window.addEventListener( 'resize', debouncedContain );

	if ( 'MutationObserver' in window ) {
		const observer = new MutationObserver( function () {
			debouncedContain();
		} );

		observer.observe( document.documentElement, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: [ 'data-aspect-ratio', 'data-height' ],
		} );
	}

	syncContainSizes();
} )();
