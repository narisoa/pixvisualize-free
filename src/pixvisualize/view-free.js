import { createRoot } from '@wordpress/element';
import CompareSliderFree from './components/CompareSliderFree';
import { containSize, findFixedParentHeight } from './utils/contain';

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
		const availWidth = block.clientWidth || null;
		const availHeight = findFixedParentHeight( block );
		const contained = containSize(
			desiredHeight,
			aspectRatio,
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
		const containerShadow =
			block.getAttribute( 'data-container-shadow' ) === '1';
		const containerBorder =
			block.getAttribute( 'data-container-border' ) === '1';
		const containerRounded =
			block.getAttribute( 'data-container-rounded' ) === '1';
		const alignment = block.getAttribute( 'data-alignment' ) || 'center';
		const labelBackground =
			block.getAttribute( 'data-label-background' ) || '#00000080';
		const labelColor =
			block.getAttribute( 'data-label-color' ) || '#ffffff';
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
								width: `min(100%, ${ boxWidth }px)`,
								margin: '0 auto',
						  }
						: {
								width: `min(100%, ${ boxWidth }px)`,
								margin: getAlignmentMargin( alignment ),
						  }
				}
			>
				<CompareSliderFree
					beforeImage={ beforeImage }
					afterImage={ afterImage }
					initialPosition={ initialPosition }
					beforeLabel={ beforeLabel }
					afterLabel={ afterLabel }
					showLabels={ showLabels }
					aspectRatio={ aspectRatio }
					height={ containedHeight }
					containerShadow={ containerShadow }
					containerBorder={ containerBorder }
					containerRounded={ containerRounded }
					labelBackground={ labelBackground }
					labelColor={ labelColor }
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

let resizeTimer;
const debouncedInit = () => {
	window.clearTimeout( resizeTimer );
	resizeTimer = window.setTimeout( init, 150 );
};

window.addEventListener( 'resize', debouncedInit );

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
