( function () {
	'use strict';

	/* global requestAnimationFrame, cancelAnimationFrame, MutationObserver, IntersectionObserver */

	const MAGNIFIER_SIZE = 160;

	function clamp( value, min, max ) {
		return Math.min( max, Math.max( min, value ) );
	}

	function getAttr( el, name, fallback ) {
		const value = el.getAttribute( name );
		return value === null || value === '' ? fallback : value;
	}

	function getContainerPos( container ) {
		const value = parseFloat(
			container.style.getPropertyValue( '--pc-pos' )
		);
		return isNaN( value ) ? 50 : value;
	}

	function setContainerPos( container, pos ) {
		container.style.setProperty( '--pc-pos', pos + '%' );
	}

	function getOrientation( container ) {
		return getAttr( container, 'data-orientation', 'horizontal' );
	}

	const scanning = new Set();
	const scanControllers = new WeakMap();
	const knownConfigs = new WeakMap();

	function getWidgetKey( container ) {
		return (
			container.closest( '.elementor-widget-pixvisualize' ) || container
		);
	}

	function easeInOut( t ) {
		return t < 0.5 ? 2 * t * t : -1 + ( 4 - 2 * t ) * t;
	}

	function startAutoScan( container ) {
		if ( scanning.has( container ) ) {
			return;
		}

		const duration =
			parseInt(
				getAttr( container, 'data-auto-scan-duration', '2500' ),
				10
			) || 2500;
		const startPos = getContainerPos( container );
		// Spec: animate from 100 to 0, then back to the initial position.
		const forwardDistance = 100; // 100 → 0
		const returnDistance = startPos; // 0 → initial
		const totalDistance = forwardDistance + returnDistance;
		const forwardDuration =
			totalDistance > 0
				? ( forwardDistance / totalDistance ) * duration
				: duration;
		let startTime = null;
		let animationId = null;
		let cancelled = false;
		let observer = null;

		function onInterrupt() {
			stopAutoScan( container );
		}

		function step( timestamp ) {
			if ( cancelled ) {
				return;
			}
			if ( startTime === null ) {
				startTime = timestamp;
			}
			const elapsed = timestamp - startTime;

			if ( elapsed < forwardDuration ) {
				setContainerPos(
					container,
					100 -
						forwardDistance * easeInOut( elapsed / forwardDuration )
				);
			} else if ( returnDistance > 0 ) {
				const t =
					( elapsed - forwardDuration ) /
					( duration - forwardDuration );
				setContainerPos(
					container,
					returnDistance * easeInOut( Math.min( t, 1 ) )
				);
			} else {
				setContainerPos( container, 0 );
			}

			if ( elapsed < duration ) {
				animationId = requestAnimationFrame( step );
			} else {
				scanning.delete( container );
				scanControllers.delete( container );
			}
		}

		// Start from 100 so the sweep always begins at the same end.
		setContainerPos( container, 100 );

		function start() {
			if ( cancelled ) {
				return;
			}
			animationId = requestAnimationFrame( step );
		}

		container.addEventListener( 'pointerdown', onInterrupt );
		container.addEventListener( 'keydown', onInterrupt );

		scanning.add( container );
		scanControllers.set( container, {
			onInterrupt,
			cancel() {
				cancelled = true;
				if ( observer !== null ) {
					observer.disconnect();
					observer = null;
				}
				if ( animationId !== null ) {
					cancelAnimationFrame( animationId );
				}
			},
		} );

		// Only start the sweep once 30% of the slider is visible.
		if ( 'IntersectionObserver' in window ) {
			observer = new IntersectionObserver(
				function ( entries ) {
					const isVisible = entries.some( function ( entry ) {
						return entry.isIntersecting;
					} );
					if ( isVisible ) {
						observer.disconnect();
						observer = null;
						start();
					}
				},
				{ threshold: 0.3 }
			);
			observer.observe( container );
		} else {
			start();
		}
	}

	function stopAutoScan( container ) {
		if ( ! scanning.has( container ) ) {
			return;
		}
		const controller = scanControllers.get( container );
		if ( controller ) {
			controller.cancel();
			container.removeEventListener(
				'pointerdown',
				controller.onInterrupt
			);
			container.removeEventListener( 'keydown', controller.onInterrupt );
		}
		scanning.delete( container );
		scanControllers.delete( container );
	}

	function syncAutoScan() {
		const containers = document.querySelectorAll(
			'.pixvisualize-elementor-preview'
		);
		const presentWidgets = new WeakSet();

		for ( let i = 0; i < containers.length; i++ ) {
			const container = containers[ i ];
			const widget = getWidgetKey( container );
			presentWidgets.add( widget );

			const enabled = getAttr( container, 'data-auto-scan', '0' ) === '1';
			const config = enabled
				? getAttr( container, 'data-auto-scan-duration', '2500' )
				: 'off';
			const previous = knownConfigs.get( widget );

			if ( previous === undefined ) {
				knownConfigs.set( widget, config );
				continue;
			}

			if ( previous !== config ) {
				knownConfigs.set( widget, config );
				stopAutoScan( container );
				if ( enabled ) {
					startAutoScan( container );
				}
			}
		}

		scanning.forEach( function ( container ) {
			const widget = getWidgetKey( container );
			if ( ! container.isConnected || ! presentWidgets.has( widget ) ) {
				stopAutoScan( container );
				knownConfigs.delete( widget );
			}
		} );
	}

	function ensureMagnifier( container ) {
		let lens = container.querySelector( '.pixvisualize-preview-magnifier' );
		if ( ! lens ) {
			lens = document.createElement( 'div' );
			lens.className = 'pixvisualize-preview-magnifier';
			const img = document.createElement( 'img' );
			img.className = 'pixvisualize-preview-magnifier__img';
			img.alt = '';
			img.draggable = false;
			lens.appendChild( img );
			container.appendChild( lens );
		}
		return lens;
	}

	function updateMagnifier( container, x, y, rect ) {
		const zoom =
			parseFloat( getAttr( container, 'data-magnifier-zoom', '2' ) ) || 2;
		const lens = ensureMagnifier( container );
		const img = lens.querySelector( 'img' );
		const half = MAGNIFIER_SIZE / 2;
		const pos = getContainerPos( container );
		const orientation = getOrientation( container );
		const visiblePercent =
			orientation === 'vertical'
				? ( y / rect.height ) * 100
				: ( x / rect.width ) * 100;
		const beforeImg = container.querySelector(
			'.pixvisualize-elementor-preview__before'
		);
		const afterImg = container.querySelector(
			'.pixvisualize-elementor-preview__after'
		);
		// Vertical shows the After image on top (clipped from the top), so the
		// visible image at the cursor is the opposite of the horizontal case.
		const beforeVisible =
			orientation === 'vertical'
				? visiblePercent > pos
				: visiblePercent <= pos;
		let src;
		if ( beforeVisible && beforeImg ) {
			src = beforeImg.src;
		} else if ( afterImg ) {
			src = afterImg.src;
		} else {
			src = '';
		}

		img.src = src;
		img.style.display = src ? 'block' : 'none';
		lens.style.display = 'block';
		lens.style.left = x - half + 'px';
		lens.style.top = y - half + 'px';
		img.style.width = rect.width * zoom + 'px';
		img.style.height = rect.height * zoom + 'px';
		img.style.transform =
			'translate(' +
			( half - x * zoom ) +
			'px,' +
			( half - y * zoom ) +
			'px)';
	}

	function onMouseMove( event ) {
		const target = event.target;
		const container =
			target && target.closest
				? target.closest( '.pixvisualize-elementor-preview' )
				: null;
		if ( ! container ) {
			return;
		}
		const rect = container.getBoundingClientRect();
		if ( ! rect.width || ! rect.height ) {
			return;
		}
		const orientation = getOrientation( container );
		const x = event.clientX - rect.left;
		const y = event.clientY - rect.top;

		if ( getAttr( container, 'data-hover-mode', '0' ) === '1' ) {
			const pos =
				orientation === 'vertical'
					? ( y / rect.height ) * 100
					: ( x / rect.width ) * 100;
			setContainerPos( container, clamp( pos, 0, 100 ) );
		}

		if ( getAttr( container, 'data-magnifier', '0' ) === '1' ) {
			updateMagnifier( container, x, y, rect );
		}
	}

	function onMouseOut( event ) {
		const target = event.target;
		const container =
			target && target.closest
				? target.closest( '.pixvisualize-elementor-preview' )
				: null;
		if ( ! container ) {
			return;
		}
		if ( container.contains( event.relatedTarget ) ) {
			return;
		}
		const lens = container.querySelector(
			'.pixvisualize-preview-magnifier'
		);
		if ( lens ) {
			lens.style.display = 'none';
		}
	}

	function syncMagnifier() {
		const containers = document.querySelectorAll(
			'.pixvisualize-elementor-preview'
		);
		for ( let i = 0; i < containers.length; i++ ) {
			const el = containers[ i ];
			if ( getAttr( el, 'data-magnifier', '0' ) !== '1' ) {
				const lens = el.querySelector(
					'.pixvisualize-preview-magnifier'
				);
				if ( lens ) {
					lens.style.display = 'none';
				}
			}
		}
	}

	// Contain layout (mirrors src/pixvisualize/utils/contain.js used by the
	// front view.js): the slider always fits inside its parent (width AND
	// fixed height when there is one) with the ratio respected — 21/9 fills
	// the width, 9/21 fills the height. The template only sets width +
	// aspect-ratio (a fixed height would over-constrain CSS and the ratio
	// would be ignored in narrow columns); this pass caps the height when
	// an ancestor has an explicit fixed height.
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

	document.addEventListener( 'mousemove', onMouseMove );
	document.addEventListener( 'mouseout', onMouseOut );
	window.addEventListener( 'resize', debouncedContain );

	if ( 'MutationObserver' in window ) {
		const observer = new MutationObserver( function () {
			syncAutoScan();
			syncMagnifier();
			debouncedContain();
		} );

		observer.observe( document.documentElement, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: [
				'data-auto-scan',
				'data-auto-scan-duration',
				'data-magnifier',
				'data-magnifier-zoom',
				'data-hover-mode',
				'data-orientation',
				'data-aspect-ratio',
				'data-height',
			],
		} );
	}

	syncMagnifier();
	syncContainSizes();
} )();
