import { parseAspectRatio } from './aspectRatio';

/**
 * Contain layout: fit a W/H-ratio box inside an available Wc x Hc box.
 *
 * - Ratio is ALWAYS respected (never distorted, never cropped).
 * - 21/9 inside a portrait container -> width-constrained (fills the width).
 * - 9/21 inside a landscape container -> height-constrained (fills the height).
 * - A fixed parent height caps Hc; an auto parent means Hc = Infinity.
 *
 * @param {number} desiredHeight Slider height setting (px, clamped).
 * @param {string} ratio         Aspect ratio string (e.g. '16/9').
 * @param {number} availWidth    Available width in px (parent content width).
 * @param {number} availHeight   Available height in px (Infinity = auto parent).
 * @return {{width:number,height:number}} Contained integer dimensions.
 */
export const containSize = (
	desiredHeight,
	ratio,
	availWidth,
	availHeight
) => {
	const { w, h } = parseAspectRatio( ratio );
	const safeAvailWidth =
		Number.isFinite( availWidth ) && availWidth > 0 ? availWidth : null;
	const cappedHeight =
		Number.isFinite( availHeight ) && availHeight > 0
			? Math.min( desiredHeight, availHeight )
			: desiredHeight;

	if ( ! safeAvailWidth ) {
		return {
			width: Math.round( ( cappedHeight * w ) / h ),
			height: Math.round( cappedHeight ),
		};
	}

	const widthForHeight = ( cappedHeight * w ) / h;
	if ( widthForHeight <= safeAvailWidth ) {
		return {
			width: Math.round( widthForHeight ),
			height: Math.round( cappedHeight ),
		};
	}

	return {
		width: Math.round( safeAvailWidth ),
		height: Math.round( ( safeAvailWidth * h ) / w ),
	};
};

/**
 * Walk up the ancestors looking for a fixed parent height.
 *
 * Only EXPLICIT heights count (inline height/min-height style or a known
 * full-height section class). Computed heights are ignored on purpose: for
 * height:auto the browser still reports a used px value, which would flag
 * almost every ancestor as fixed and wrongly shrink the slider.
 *
 * Returns the available height in px, or Infinity when no fixed ancestor
 * exists (auto parent: the height setting wins).
 *
 * @param {Element} element  Starting element (the block wrapper).
 * @param {number}  maxDepth Max ancestors to inspect.
 * @return {number} Available height in px or Infinity.
 */
export const findFixedParentHeight = ( element, maxDepth = 8 ) => {
	if ( ! element || ! element.parentElement ) {
		return Infinity;
	}
	const isExplicit = ( value ) =>
		!! value &&
		value !== 'auto' &&
		value !== '' &&
		value !== '0px' &&
		value !== 'none' &&
		! value.endsWith( '%' );
	let node = element.parentElement;
	let depth = 0;
	while ( node && node.nodeType === 1 && depth < maxDepth ) {
		const inline = node.style || {};
		const hasInlineFixed =
			isExplicit( inline.height ) || isExplicit( inline.minHeight );
		const hasFullHeightClass =
			node.classList &&
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
};
