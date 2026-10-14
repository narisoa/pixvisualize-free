import { createRoot } from '@wordpress/element';
import CompareSlider from './components/CompareSlider';
import { containSize, findFixedParentHeight } from './utils/contain';

// Reuse the React roots so init() can be re-run safely (the editor re-renders
// the preview asynchronously after the page's DOMContentLoaded, which would
// otherwise leave the slider with stale dimensions).
const roots = new WeakMap();

const getAlignmentMargin = ( alignment ) => {
	if ( alignment === 'left' ) {
		return '0 auto 0 0';
	}
	if ( alignment === 'right' ) {
		return '0 0 0 auto';
	}
	return '0 auto';
};

const init = () => {
	const blocks = document.querySelectorAll( '.pixvisualize-block-wrapper' );

	blocks.forEach( ( block ) => {
		const rootElement = block.querySelector( '.pixvisualize-react-root' );
		if ( ! rootElement ) {
			return;
		}

		const beforeImage = block.getAttribute( 'data-before-image' ) || '';
		const afterImage = block.getAttribute( 'data-after-image' ) || '';
		const beforeLabel =
			block.getAttribute( 'data-before-label' ) || 'Before';
		const afterLabel = block.getAttribute( 'data-after-label' ) || 'After';
		const showLabels = block.getAttribute( 'data-show-labels' ) !== '0';
		const aspectRatio = block.getAttribute( 'data-aspect-ratio' ) || '16/9';
		const rawHeight = parseInt( block.getAttribute( 'data-height' ), 10 );
		const desiredHeight = Number.isFinite( rawHeight )
			? Math.min( 2000, Math.max( 100, rawHeight ) )
			: 450;
		// Effective ratio follows the same breakpoint priority as
		// CompareSlider (mobile > tablet > desktop) so the outer box
		// already contains the ratio that will actually render.
		const tabletRatioAttr = block.getAttribute( 'data-tablet-ratio' ) || '';
		const mobileRatioAttr = block.getAttribute( 'data-mobile-ratio' ) || '';
		let effectiveRatio = aspectRatio;
		if (
			typeof window !== 'undefined' &&
			typeof window.matchMedia === 'function'
		) {
			if (
				window.matchMedia( '(max-width: 767px)' ).matches &&
				mobileRatioAttr
			) {
				effectiveRatio = mobileRatioAttr;
			} else if (
				window.matchMedia( '(max-width: 1024px)' ).matches &&
				tabletRatioAttr
			) {
				effectiveRatio = tabletRatioAttr;
			}
		}
		// Contain layout inputs: the slider always fits inside its parent
		// (width AND fixed height when there is one) with the ratio
		// respected — 21/9 fills the width, 9/21 fills the height.
		// Auto parent -> availHeight = Infinity (height setting wins).
		const availWidth = block.clientWidth || null;
		const availHeight = findFixedParentHeight( block );
		const contained = containSize(
			desiredHeight,
			effectiveRatio,
			availWidth,
			availHeight
		);
		const boxWidth = contained.width;
		const containedHeight = contained.height;
		const parentFixed = Number.isFinite( availHeight );
		const fullWidth = block.getAttribute( 'data-full-width' ) === '1';
		const rawPosition = parseInt(
			block.getAttribute( 'data-initial-position' ),
			10
		);
		const initialPosition = Number.isFinite( rawPosition )
			? rawPosition
			: 50;
		const orientation =
			block.getAttribute( 'data-orientation' ) || 'horizontal';
		const handleStyle =
			block.getAttribute( 'data-handle-style' ) || 'circle';
		const handleIconSvg =
			block.getAttribute( 'data-handle-icon-svg' ) || '';
		const rawHandleSize = parseInt(
			block.getAttribute( 'data-handle-size' ),
			10
		);
		const handleSize = Number.isFinite( rawHandleSize )
			? rawHandleSize
			: 24;
		const handleBackground = block.getAttribute( 'data-handle-bg' ) || '';
		const handleArrowColor =
			block.getAttribute( 'data-handle-arrow-color' ) || '#666666';
		const rawHandlePadding = parseInt(
			block.getAttribute( 'data-handle-padding' ),
			10
		);
		const handlePadding = Number.isFinite( rawHandlePadding )
			? rawHandlePadding
			: 0;
		const labelPositionBefore =
			block.getAttribute( 'data-label-position-before' ) || 'top-left';
		const labelPositionAfter =
			block.getAttribute( 'data-label-position-after' ) || 'top-right';
		const labelBackground =
			block.getAttribute( 'data-label-background' ) || '#00000080';
		const labelColor =
			block.getAttribute( 'data-label-color' ) || '#ffffff';
		const rawLabelFontSize = parseInt(
			block.getAttribute( 'data-label-font-size' ),
			10
		);
		const labelFontSize = Number.isFinite( rawLabelFontSize )
			? rawLabelFontSize
			: 12;
		const rawLabelFontWeight = parseInt(
			block.getAttribute( 'data-label-font-weight' ),
			10
		);
		const labelFontWeight = Number.isFinite( rawLabelFontWeight )
			? rawLabelFontWeight
			: 600;
		const rawLabelPaddingX = parseInt(
			block.getAttribute( 'data-label-padding-x' ),
			10
		);
		const labelPaddingX = Number.isFinite( rawLabelPaddingX )
			? rawLabelPaddingX
			: 10;
		const rawLabelPaddingY = parseInt(
			block.getAttribute( 'data-label-padding-y' ),
			10
		);
		const labelPaddingY = Number.isFinite( rawLabelPaddingY )
			? rawLabelPaddingY
			: 5;
		const rawLabelBorderRadius = parseInt(
			block.getAttribute( 'data-label-border-radius' ),
			10
		);
		const labelBorderRadius = Number.isFinite( rawLabelBorderRadius )
			? rawLabelBorderRadius
			: 4;
		const rawSeparatorThickness = parseInt(
			block.getAttribute( 'data-separator-thickness' ),
			10
		);
		const separatorThickness = Number.isFinite( rawSeparatorThickness )
			? rawSeparatorThickness
			: 2;
		const separatorColor =
			block.getAttribute( 'data-separator-color' ) || '#ffffff';
		const separatorShadow =
			block.getAttribute( 'data-separator-shadow' ) === '1';
		const containerShadow =
			block.getAttribute( 'data-container-shadow' ) === '1';
		const containerShadowColor =
			block.getAttribute( 'data-container-shadow-color' ) || '#000000';
		const rawContainerShadowHorizontal = parseInt(
			block.getAttribute( 'data-container-shadow-horizontal' ),
			10
		);
		const containerShadowHorizontal = Number.isFinite(
			rawContainerShadowHorizontal
		)
			? rawContainerShadowHorizontal
			: 0;
		const rawContainerShadowVertical = parseInt(
			block.getAttribute( 'data-container-shadow-vertical' ),
			10
		);
		const containerShadowVertical = Number.isFinite(
			rawContainerShadowVertical
		)
			? rawContainerShadowVertical
			: 8;
		const rawContainerShadowBlur = parseInt(
			block.getAttribute( 'data-container-shadow-blur' ),
			10
		);
		const containerShadowBlur = Number.isFinite( rawContainerShadowBlur )
			? rawContainerShadowBlur
			: 20;
		const rawContainerShadowSpread = parseInt(
			block.getAttribute( 'data-container-shadow-spread' ),
			10
		);
		const containerShadowSpread = Number.isFinite(
			rawContainerShadowSpread
		)
			? rawContainerShadowSpread
			: 0;
		const rawContainerShadowOpacity = parseInt(
			block.getAttribute( 'data-container-shadow-opacity' ),
			10
		);
		const containerShadowOpacity = Number.isFinite(
			rawContainerShadowOpacity
		)
			? rawContainerShadowOpacity
			: 35;
		const rawContainerBorderWidth = parseInt(
			block.getAttribute( 'data-container-border-width' ),
			10
		);
		const containerBorderWidth = Number.isFinite( rawContainerBorderWidth )
			? rawContainerBorderWidth
			: 0;
		// FREE toggle (default border when on: 2px solid gray).
		const containerBorder =
			block.getAttribute( 'data-container-border' ) === '1';
		const containerBorderColor =
			block.getAttribute( 'data-container-border-color' ) || '#000000';
		const containerBorderStyle =
			block.getAttribute( 'data-container-border-style' ) || 'solid';
		const rawContainerBorderRadiusTL = parseInt(
			block.getAttribute( 'data-container-border-radius-tl' ),
			10
		);
		const containerBorderRadiusTL = Number.isFinite(
			rawContainerBorderRadiusTL
		)
			? rawContainerBorderRadiusTL
			: 0;
		const rawContainerBorderRadiusTR = parseInt(
			block.getAttribute( 'data-container-border-radius-tr' ),
			10
		);
		const containerBorderRadiusTR = Number.isFinite(
			rawContainerBorderRadiusTR
		)
			? rawContainerBorderRadiusTR
			: 0;
		const rawContainerBorderRadiusBR = parseInt(
			block.getAttribute( 'data-container-border-radius-br' ),
			10
		);
		const containerBorderRadiusBR = Number.isFinite(
			rawContainerBorderRadiusBR
		)
			? rawContainerBorderRadiusBR
			: 0;
		const rawContainerBorderRadiusBL = parseInt(
			block.getAttribute( 'data-container-border-radius-bl' ),
			10
		);
		const containerBorderRadiusBL = Number.isFinite(
			rawContainerBorderRadiusBL
		)
			? rawContainerBorderRadiusBL
			: 0;
		// FREE toggle (default radius when on: 8px each corner).
		const containerRounded =
			block.getAttribute( 'data-container-rounded' ) === '1';
		const hoverMode = block.getAttribute( 'data-hover-mode' ) === '1';
		const autoScan = block.getAttribute( 'data-auto-scan' ) === '1';
		const rawAutoScanDuration = parseInt(
			block.getAttribute( 'data-auto-scan-duration' ),
			10
		);
		const autoScanDuration = Number.isFinite( rawAutoScanDuration )
			? rawAutoScanDuration
			: 2500;
		const magnifier = block.getAttribute( 'data-magnifier' ) === '1';
		const rawMagnifierZoom = parseInt(
			block.getAttribute( 'data-magnifier-zoom' ),
			10
		);
		const magnifierZoom = Number.isFinite( rawMagnifierZoom )
			? rawMagnifierZoom
			: 2;
		const rawTabletPosition = parseInt(
			block.getAttribute( 'data-tablet-position' ),
			10
		);
		const tabletPosition = Number.isFinite( rawTabletPosition )
			? rawTabletPosition
			: 50;
		const rawMobilePosition = parseInt(
			block.getAttribute( 'data-mobile-position' ),
			10
		);
		const mobilePosition = Number.isFinite( rawMobilePosition )
			? rawMobilePosition
			: 50;
		const tabletRatio = block.getAttribute( 'data-tablet-ratio' ) || '';
		const mobileRatio = block.getAttribute( 'data-mobile-ratio' ) || '';
		const alignment = block.getAttribute( 'data-alignment' ) || 'center';
		const captionTitle = block.getAttribute( 'data-caption-title' ) || '';
		const captionText = block.getAttribute( 'data-caption-text' ) || '';
		const showCaption = block.getAttribute( 'data-show-caption' ) !== '0';
		const captionBg = block.getAttribute( 'data-caption-bg' ) || '#ffffff';
		const captionTitleColor =
			block.getAttribute( 'data-caption-title-color' ) || '#1e293b';
		const captionTextColor =
			block.getAttribute( 'data-caption-text-color' ) || '#64748b';
		const captionAlignment =
			block.getAttribute( 'data-caption-alignment' ) || 'center';
		const rawCaptionTitleSize = parseInt(
			block.getAttribute( 'data-caption-title-size' ),
			10
		);
		const captionTitleSize = Number.isFinite( rawCaptionTitleSize )
			? rawCaptionTitleSize
			: 22;
		const rawCaptionTextSize = parseInt(
			block.getAttribute( 'data-caption-text-size' ),
			10
		);
		const captionTextSize = Number.isFinite( rawCaptionTextSize )
			? rawCaptionTextSize
			: 15;
		const rawCaptionPadding = parseInt(
			block.getAttribute( 'data-caption-padding' ),
			10
		);
		const legacyCaptionPadding = Number.isFinite( rawCaptionPadding )
			? rawCaptionPadding
			: 16;
		const rawCaptionPaddingX = parseInt(
			block.getAttribute( 'data-caption-padding-x' ),
			10
		);
		const captionPaddingX = Number.isFinite( rawCaptionPaddingX )
			? rawCaptionPaddingX
			: legacyCaptionPadding;
		const rawCaptionPaddingY = parseInt(
			block.getAttribute( 'data-caption-padding-y' ),
			10
		);
		const captionPaddingY = Number.isFinite( rawCaptionPaddingY )
			? rawCaptionPaddingY
			: legacyCaptionPadding;

		let root = roots.get( rootElement );
		if ( ! root ) {
			root = createRoot( rootElement );
			roots.set( rootElement, root );
		}

		root.render(
			<div
				style={
					fullWidth || parentFixed
						? {
								// Contain: wide ratios fill the width, tall
								// ratios fill the capped height — centered,
								// ratio always respected. Full width is
								// useless with a fixed parent (contain
								// already caps it) but useful with an auto
								// parent (spans the content width).
								// NOTE: display stays block so the caption
								// keeps stacking below the slider. Like a
								// native Elementor shadow, ours is pure
								// paint (no reserved room): it needs visual
								// room around the card to show fully.
								width: `min(100%, ${ boxWidth }px)`,
								margin: '0 auto',
						  }
						: {
								width: `min(100%, ${ boxWidth }px)`,
								margin: getAlignmentMargin( alignment ),
						  }
				}
			>
				<CompareSlider
					beforeImage={ beforeImage }
					afterImage={ afterImage }
					initialPosition={ initialPosition }
					beforeLabel={ beforeLabel }
					afterLabel={ afterLabel }
					showLabels={ showLabels }
					aspectRatio={ effectiveRatio }
					height={ containedHeight }
					orientation={ orientation }
					handleStyle={ handleStyle }
					handleIconSvg={ handleIconSvg }
					handleSize={ handleSize }
					handleBackground={ handleBackground }
					handleArrowColor={ handleArrowColor }
					handlePadding={ handlePadding }
					labelPositionBefore={ labelPositionBefore }
					labelPositionAfter={ labelPositionAfter }
					labelBackground={ labelBackground }
					labelColor={ labelColor }
					labelFontSize={ labelFontSize }
					labelFontWeight={ labelFontWeight }
					labelPaddingX={ labelPaddingX }
					labelPaddingY={ labelPaddingY }
					labelBorderRadius={ labelBorderRadius }
					separatorThickness={ separatorThickness }
					separatorColor={ separatorColor }
					separatorShadow={ separatorShadow }
					containerShadow={ containerShadow }
					containerShadowColor={ containerShadowColor }
					containerShadowHorizontal={ containerShadowHorizontal }
					containerShadowVertical={ containerShadowVertical }
					containerShadowBlur={ containerShadowBlur }
					containerShadowSpread={ containerShadowSpread }
					containerShadowOpacity={ containerShadowOpacity }
					containerBorderWidth={ containerBorderWidth }
					containerBorder={ containerBorder }
					containerBorderColor={ containerBorderColor }
					containerBorderStyle={ containerBorderStyle }
					containerBorderRadiusTL={ containerBorderRadiusTL }
					containerBorderRadiusTR={ containerBorderRadiusTR }
					containerBorderRadiusBR={ containerBorderRadiusBR }
					containerBorderRadiusBL={ containerBorderRadiusBL }
					containerRounded={ containerRounded }
					hoverMode={ hoverMode }
					autoScan={ autoScan }
					autoScanDuration={ autoScanDuration }
					magnifier={ magnifier }
					magnifierZoom={ magnifierZoom }
					tabletPosition={ tabletPosition }
					mobilePosition={ mobilePosition }
					tabletRatio={ tabletRatio }
					mobileRatio={ mobileRatio }
					captionTitle={ captionTitle }
					captionText={ captionText }
					showCaption={ showCaption }
					captionBg={ captionBg }
					captionTitleColor={ captionTitleColor }
					captionTextColor={ captionTextColor }
					captionAlignment={ captionAlignment }
					captionTitleSize={ captionTitleSize }
					captionTextSize={ captionTextSize }
					captionPadding={ legacyCaptionPadding }
					captionPaddingX={ captionPaddingX }
					captionPaddingY={ captionPaddingY }
				/>
			</div>
		);
	} );
};

// Re-run init on resize. In the Elementor editor the widget markup is injected
// into the preview iframe after the initial load, so dimensions computed at
// load time are stale; a simulated resize (from the editor script) forces a
// recalculation without touching the slider logic itself.
let resizeTimer;
const debouncedInit = () => {
	window.clearTimeout( resizeTimer );
	resizeTimer = window.setTimeout( init, 150 );
};

window.addEventListener( 'resize', debouncedInit );

// Hydrate sliders injected after the initial load (Elementor editor preview
// iframes, page builders, lazy-loaded content). On load the block markup is
// present, but in those contexts `.pixvisualize-block-wrapper` nodes are added
// asynchronously after `DOMContentLoaded`, so a MutationObserver re-runs the
// debounced `init` whenever a new wrapper appears. `init` is idempotent (it
// reuses the React roots through the WeakMap), so re-running is safe.
let observer;
if (
	typeof window !== 'undefined' &&
	typeof window.MutationObserver !== 'undefined' &&
	document.body
) {
	observer = new window.MutationObserver( ( mutations ) => {
		for ( const mutation of mutations ) {
			if ( mutation.type !== 'childList' ) {
				continue;
			}
			for ( const node of mutation.addedNodes ) {
				if ( node.nodeType !== 1 ) {
					continue;
				}
				if (
					node.matches( '.pixvisualize-block-wrapper' ) ||
					( node.querySelector &&
						node.querySelector( '.pixvisualize-block-wrapper' ) )
				) {
					debouncedInit();
					return;
				}
			}
		}
	} );

	observer.observe( document.body, { childList: true, subtree: true } );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
