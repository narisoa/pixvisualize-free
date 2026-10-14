import React, { useEffect, useMemo } from 'react';
import { __ } from '@wordpress/i18n';
import { useDrag } from '../hooks/useDrag';
import useBreakpoint from '../hooks/useBreakpoint';
import { clamp } from '../utils/clamp';
import ImageLayer from './ImageLayer';
import Handle from './Handle';
import { getPositionStyle } from './Labels';
import Magnifier from './Magnifier';
import { toRgba } from '../utils/rgba';

const CompareSlider = ( {
	beforeImage,
	afterImage,
	initialPosition = 50,
	beforeLabel = 'Before',
	afterLabel = 'After',
	showLabels = true,
	aspectRatio = '16/9',
	// Real zone height in px. Null (default) keeps the legacy ratio-driven
	// height — the block editor does not pass this prop yet (shortcode
	// first, Gutenberg later). The front-end view always passes it.
	height = null,
	// Full-width mode: the slider spans 100% of its container, so the fixed
	// px height is dropped and CSS aspect-ratio derives the container height
	// from the width. The ratio always drives the height in full width.
	fullWidth = false,
	orientation = 'horizontal',
	handleStyle = 'circle',
	handleIconSvg = '',
	handleSize = 24,
	handleBackground = '',
	handleArrowColor = '#666666',
	handlePadding = 0,
	labelPositionBefore = 'top-left',
	labelPositionAfter = 'top-right',
	labelBackground = '#00000080',
	labelColor = '#ffffff',
	labelFontSize = 12,
	labelFontWeight = 600,
	labelPaddingX = 10,
	labelPaddingY = 5,
	labelBorderRadius = 4,
	separatorThickness = 2,
	separatorColor = '#ffffff',
	separatorShadow = true,
	containerShadow = false,
	containerShadowColor = '#000000',
	containerShadowHorizontal = 0,
	containerShadowVertical = 8,
	containerShadowBlur = 20,
	containerShadowSpread = 0,
	containerShadowOpacity = 35,
	containerBorderWidth = 0,
	// FREE toggle (checkbox): show a default 2px solid gray border.
	containerBorder = false,
	containerBorderColor = '#000000',
	containerBorderStyle = 'solid',
	containerBorderRadiusTL = 0,
	containerBorderRadiusTR = 0,
	containerBorderRadiusBR = 0,
	containerBorderRadiusBL = 0,
	// FREE toggle (checkbox): rounded corners with the default radius.
	containerRounded = false,
	hoverMode = false,
	autoScan = false,
	autoScanDuration = 2500,
	magnifier = false,
	magnifierZoom = 2,
	tabletPosition = 50,
	mobilePosition = 50,
	tabletRatio = '',
	mobileRatio = '',
	captionTitle = '',
	captionText = '',
	showCaption = true,
	captionBg = '#ffffff',
	captionTitleColor = '#1e293b',
	captionTextColor = '#64748b',
	captionAlignment = 'center',
	captionTitleSize = 22,
	captionTextSize = 15,
	captionPadding = 16,
	captionPaddingX = 5,
	captionPaddingY = 9,
} ) => {
	const isVertical = orientation === 'vertical';
	const axis = isVertical ? 'y' : 'x';
	const breakpoint = useBreakpoint();

	const effectiveInitialPosition = useMemo( () => {
		if ( breakpoint === 'mobile' ) {
			return mobilePosition;
		}
		if ( breakpoint === 'tablet' ) {
			return tabletPosition;
		}
		return initialPosition;
	}, [ breakpoint, mobilePosition, tabletPosition, initialPosition ] );

	const effectiveAspectRatio = useMemo( () => {
		if ( breakpoint === 'mobile' && mobileRatio ) {
			return mobileRatio;
		}
		if ( breakpoint === 'tablet' && tabletRatio ) {
			return tabletRatio;
		}
		return aspectRatio;
	}, [ breakpoint, mobileRatio, tabletRatio, aspectRatio ] );

	const {
		position,
		setPosition,
		moveTo,
		isDragging,
		onPointerDown,
		onPointerMove,
		onPointerUp,
		onPointerCancel,
		containerRef,
	} = useDrag( effectiveInitialPosition, undefined, axis );

	const separator = {
		thickness: separatorThickness,
		color: separatorColor,
		shadow: separatorShadow,
	};

	useEffect( () => {
		if ( ! autoScan ) {
			return undefined;
		}

		const container = containerRef.current;
		if ( ! container ) {
			return undefined;
		}
		let startTime = null;
		let animationId = null;
		let cancelled = false;
		let observer = null;

		const easeInOut = ( t ) =>
			t < 0.5 ? 2 * t * t : -1 + ( 4 - 2 * t ) * t;

		// Spec: animate from 100 to 0, then back to the initial position.
		const forwardDistance = 100; // 100 → 0
		const returnDistance = effectiveInitialPosition; // 0 → initial
		const totalDistance = forwardDistance + returnDistance;
		const forwardDuration =
			totalDistance > 0
				? ( forwardDistance / totalDistance ) * autoScanDuration
				: autoScanDuration;

		// Start from 100 so the sweep always begins at the same end.
		setPosition( 100 );

		const step = ( timestamp ) => {
			if ( cancelled ) {
				return;
			}
			if ( startTime === null ) {
				startTime = timestamp;
			}
			const elapsed = timestamp - startTime;

			if ( elapsed < forwardDuration ) {
				setPosition(
					100 - forwardDistance * easeInOut( elapsed / forwardDuration )
				);
			} else if ( returnDistance > 0 ) {
				const t =
					( elapsed - forwardDuration ) /
					( autoScanDuration - forwardDuration );
				setPosition(
					returnDistance * easeInOut( Math.min( t, 1 ) )
				);
			} else {
				setPosition( 0 );
			}

			if ( elapsed < autoScanDuration ) {
				animationId = requestAnimationFrame( step );
			}
		};

		const start = () => {
			if ( cancelled ) {
				return;
			}
			animationId = requestAnimationFrame( step );
		};

		const interrupt = () => {
			if ( cancelled ) {
				return;
			}
			cancelled = true;
			if ( animationId !== null ) {
				cancelAnimationFrame( animationId );
			}
		};

		container?.addEventListener( 'pointerdown', interrupt );
		container?.addEventListener( 'keydown', interrupt );

		// Only start the sweep once 30% of the slider is visible.
		if ( typeof IntersectionObserver !== 'undefined' ) {
			observer = new IntersectionObserver(
				( entries ) => {
					if ( entries.some( ( entry ) => entry.isIntersecting ) ) {
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

		return () => {
			interrupt();
			if ( observer ) {
				observer.disconnect();
			}
			container?.removeEventListener( 'pointerdown', interrupt );
			container?.removeEventListener( 'keydown', interrupt );
		};
	}, [
		autoScan,
		autoScanDuration,
		effectiveInitialPosition,
		setPosition,
		containerRef,
	] );

	const handlePointerMove = ( e ) => {
		if ( hoverMode && e.pointerType === 'mouse' && ! isDragging ) {
			moveTo( e.clientX, e.clientY );
			return;
		}
		onPointerMove( e );
	};

	if ( ! beforeImage || ! afterImage ) {
		return (
			<div className="pixvisualize-placeholder">
				{ __(
					'Please select both images for the comparator.',
					'pixvisualize'
				) }
			</div>
		);
	}

	const labelStyle = {
		position: 'absolute',
		backgroundColor: labelBackground,
		color: labelColor,
		padding: `${ labelPaddingY }px ${ labelPaddingX }px`,
		borderRadius: `${ labelBorderRadius }px`,
		fontSize: `${ labelFontSize }px`,
		fontWeight: labelFontWeight,
		zIndex: 5,
	};

	const beforeLabelStyle = {
		...labelStyle,
		...getPositionStyle( labelPositionBefore ),
	};

	const afterLabelStyle = {
		...labelStyle,
		...getPositionStyle( labelPositionAfter ),
	};

	const onKeyDown = ( event ) => {
		const step = event.shiftKey ? 10 : 2;
		if (
			! isVertical &&
			( event.key === 'ArrowLeft' || event.key === 'ArrowRight' )
		) {
			setPosition(
				clamp(
					position + ( event.key === 'ArrowRight' ? step : -step ),
					0,
					100
				)
			);
			event.preventDefault();
		}
		if (
			isVertical &&
			( event.key === 'ArrowUp' || event.key === 'ArrowDown' )
		) {
			setPosition(
				clamp(
					position + ( event.key === 'ArrowDown' ? step : -step ),
					0,
					100
				)
			);
			event.preventDefault();
		}
		if ( event.key === 'Home' ) {
			setPosition( 0 );
			event.preventDefault();
		}
		if ( event.key === 'End' ) {
			setPosition( 100 );
			event.preventDefault();
		}
	};

	const showCaptionBlock =
		showCaption && ( captionTitle || captionText );

	// Card look: with a caption the container border wraps the whole card
	// (slider on top, caption at the bottom) instead of circling the slider
	// alone. Without a caption the slider keeps the full border + radius.
	// FREE defaults (checkboxes): border = 2px solid gray, radius = 16px.
	// PRO customization (width/color/style, per-corner radius) wins when set.
	const FREE_BORDER_WIDTH = 2;
	const FREE_BORDER_COLOR = '#E5E7EB';
	const FREE_BORDER_STYLE = 'solid';
	const FREE_RADIUS = 16;
	const effBorderWidth =
		containerBorderWidth > 0
			? containerBorderWidth
			: containerBorder
			  ? FREE_BORDER_WIDTH
			  : 0;
	const effBorderColor =
		containerBorderWidth > 0 ? containerBorderColor : FREE_BORDER_COLOR;
	const effBorderStyle =
		containerBorderWidth > 0 ? containerBorderStyle : FREE_BORDER_STYLE;
	const effRadiusTL =
		containerBorderRadiusTL > 0
			? containerBorderRadiusTL
			: containerRounded
			  ? FREE_RADIUS
			  : 0;
	const effRadiusTR =
		containerBorderRadiusTR > 0
			? containerBorderRadiusTR
			: containerRounded
			  ? FREE_RADIUS
			  : 0;
	const effRadiusBR =
		containerBorderRadiusBR > 0
			? containerBorderRadiusBR
			: containerRounded
			  ? FREE_RADIUS
			  : 0;
	const effRadiusBL =
		containerBorderRadiusBL > 0
			? containerBorderRadiusBL
			: containerRounded
			  ? FREE_RADIUS
			  : 0;
	const containerHasBorder = effBorderWidth > 0;
	const containerBorderValue = containerHasBorder
		? `${ effBorderWidth }px ${ effBorderStyle } ${ effBorderColor }`
		: 'none';

	const containerBoxShadowValue = containerShadow
		? `${ containerShadowHorizontal }px ${ containerShadowVertical }px ${ containerShadowBlur }px ${ containerShadowSpread }px ${ toRgba(
				containerShadowColor,
				containerShadowOpacity
		  ) }`
		: 'none';

	// Card look (shadow): with a caption the shadow wraps the whole card,
	// so it moves to a card wrapper and the slider shadow is turned off
	// (otherwise the slider and the card would cast two stacked shadows).
	const wrapCard = showCaptionBlock && containerShadow;

	// The height setting is the real zone height (clamped like the admin
	// and editor fields). The outer box (view.js) already applies the
	// contain layout — fit inside the parent width AND fixed height with
	// the ratio respected (21/9 fills the width, 9/21 fills the height).
	// The slider keeps aspect-ratio + fixed height here; images use
	// object-fit cover so nothing gets distorted.
	const numericHeight = Number( height );
	const realHeight = Number.isFinite( numericHeight )
		? Math.min( 2000, Math.max( 100, numericHeight ) )
		: null;

	const sliderStyle = {
		position: 'relative',
		width: '100%',
		maxWidth: '100%',
		maxHeight: '100%',
		margin: 'auto',
		aspectRatio: effectiveAspectRatio,
		...( realHeight !== null ? { height: `${ realHeight }px` } : {} ),
		overflow: 'hidden',
		userSelect: 'none',
		// Horizontal : pan-y → scroll page vertical libre, drag horizontal.
		// Vertical : pan-x pan-y → tout le scroll natif libre sur l'image,
		// drag vertical réservé à la poignée (touch-action: none).
		touchAction: isVertical ? 'pan-x pan-y' : 'pan-y',
		cursor: isVertical ? 'ns-resize' : 'ew-resize',
		boxShadow: wrapCard ? 'none' : containerBoxShadowValue,
		borderRadius: showCaptionBlock
			? `${ effRadiusTL }px ${ effRadiusTR }px 0 0`
			: `${ effRadiusTL }px ${ effRadiusTR }px ${ effRadiusBR }px ${ effRadiusBL }px`,
		...( showCaptionBlock
			? {
					borderTop: containerBorderValue,
					borderLeft: containerBorderValue,
					borderRight: containerBorderValue,
					borderBottom: 'none',
			  }
			: { border: containerBorderValue } ),
	};

	// Card look: with a caption the container bottom corners move to the
	// caption (the slider bottom stays square); without a caption the
	// slider keeps all four corners.
	const captionBottomRadius = `${ effRadiusBR }px ${ effRadiusBL }px`;

	const captionStyle = {
		backgroundColor: captionBg,
		textAlign: [ 'left', 'center', 'right' ].includes( captionAlignment )
			? captionAlignment
			: 'center',
		// Marges internes séparées : X = gauche/droite (défaut 5),
		// Y = haut/bas (défaut 9, contrôle directement la hauteur).
		// Repli legacy : captionPadding uniforme quand X/Y absents.
		padding: `${ captionPaddingY ?? captionPadding ?? 9 }px ${ captionPaddingX ?? captionPadding ?? 5 }px`,
		borderRadius: `0 0 ${ captionBottomRadius }`,
		borderTop: containerHasBorder ? 'none' : '1px solid #e5e7eb',
		...( containerHasBorder
			? {
					borderLeft: containerBorderValue,
					borderRight: containerBorderValue,
					borderBottom: containerBorderValue,
			  }
			: {} ),
	};

	const captionTitleStyle = {
		color: captionTitleColor,
		fontSize: `${ captionTitleSize }px`,
	};

	const captionTextStyle = {
		color: captionTextColor,
		fontSize: `${ captionTextSize }px`,
	};

	// The wrapper follows the outer corners of the card: top from the
	// container radius, bottom from the caption (container corners when a
	// border is active, caption setting otherwise).
	const cardStyle = {
		boxShadow: containerBoxShadowValue,
		borderRadius: `${ effRadiusTL }px ${ effRadiusTR }px ${ captionBottomRadius }`,
	};

	const sliderElement = (
		<div
			ref={ containerRef }
			className={
				'pixvisualize-slider-container' +
				( isVertical ? ' pixvisualize-slider-container--vertical' : '' )
			}
			style={ sliderStyle }
			// Horizontal : drag depuis toute la surface (pan-y garde
			// le scroll page). Vertical : drag UNIQUEMENT depuis la
			// poignée (Handle garde son onPointerDown) pour que le
			// doigt sur l'image scrolle la page au lieu de bouger le curseur.
			onPointerDown={ isVertical ? undefined : onPointerDown }
			onPointerMove={ handlePointerMove }
			onPointerUp={ onPointerUp }
			onPointerCancel={ onPointerCancel }
			onDragStart={ ( event ) => event.preventDefault() }
		>
			<ImageLayer
				imageSrc={ afterImage }
				isBefore={ isVertical }
				position={ position }
				orientation={ orientation }
			>
				{ showLabels && (
					<div
						className="pixvisualize-label pixvisualize-label-after"
						style={ afterLabelStyle }
					>
						{ afterLabel || 'After' }
					</div>
				) }
			</ImageLayer>
			<ImageLayer
				imageSrc={ beforeImage }
				isBefore={ ! isVertical }
				position={ position }
				orientation={ orientation }
			>
				{ showLabels && (
					<div
						className="pixvisualize-label pixvisualize-label-before"
						style={ beforeLabelStyle }
					>
						{ beforeLabel || 'Before' }
					</div>
				) }
			</ImageLayer>
			<Magnifier
				enabled={ magnifier && ! isDragging }
				zoom={ magnifierZoom }
				beforeImage={ beforeImage }
				afterImage={ afterImage }
				position={ position }
				orientation={ orientation }
				containerRef={ containerRef }
			/>
			<Handle
				position={ position }
				onPointerDown={ onPointerDown }
				onKeyDown={ onKeyDown }
				orientation={ orientation }
				handleStyle={ handleStyle }
				handleIconSvg={ handleIconSvg }
				handleSize={ handleSize }
				handleBackground={ handleBackground }
				handleArrowColor={ handleArrowColor }
				handlePadding={ handlePadding }
				separator={ separator }
			/>
		</div>
	);

	const captionElement = showCaptionBlock ? (
		<div className="pixvisualize-caption" style={ captionStyle }>
			{ captionTitle ? (
				<div
					className="pixvisualize-caption-title"
					style={ captionTitleStyle }
				>
					{ captionTitle }
				</div>
			) : null }
			{ captionText ? (
				<div
					className="pixvisualize-caption-text"
					style={ captionTextStyle }
				>
					{ captionText }
				</div>
			) : null }
		</div>
	) : null;

	if ( wrapCard ) {
		return (
			<div className="pixvisualize-card" style={ cardStyle }>
				{ sliderElement }
				{ captionElement }
			</div>
		);
	}

	return (
		<>
			{ sliderElement }
			{ captionElement }
		</>
	);
};

export default CompareSlider;
