import React from 'react';
import { __ } from '@wordpress/i18n';
import { useDragFree } from '../hooks/useDragFree';
import { clamp } from '../utils/clamp';
import ImageLayerFree from './ImageLayerFree';
import HandleFree from './HandleFree';
import { toRgba } from '../utils/rgba';

const FREE_POSITIONS = {
	'top-left': { top: 20, left: 20 },
	'top-right': { top: 20, right: 20 },
};

const getFreePositionStyle = ( position ) =>
	FREE_POSITIONS[ position ] || FREE_POSITIONS[ 'top-left' ];

const CompareSliderFree = ( {
	beforeImage,
	afterImage,
	initialPosition = 50,
	beforeLabel = 'Before',
	afterLabel = 'After',
	showLabels = true,
	aspectRatio = '16/9',
	height = null,
	containerShadow = false,
	containerBorder = false,
	containerRounded = false,
	labelBackground = '#00000080',
	labelColor = '#ffffff',
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
	const {
		position,
		setPosition,
		onPointerDown,
		onPointerMove,
		onPointerUp,
		onPointerCancel,
		containerRef,
	} = useDragFree( initialPosition );

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
		padding: '5px 10px',
		borderRadius: '4px',
		fontSize: '12px',
		fontWeight: 600,
		zIndex: 5,
	};

	const beforeLabelStyle = {
		...labelStyle,
		...getFreePositionStyle( 'top-left' ),
	};

	const afterLabelStyle = {
		...labelStyle,
		...getFreePositionStyle( 'top-right' ),
	};

	const onKeyDown = ( event ) => {
		const step = event.shiftKey ? 10 : 2;
		if ( event.key === 'ArrowLeft' || event.key === 'ArrowRight' ) {
			setPosition(
				clamp(
					position + ( event.key === 'ArrowRight' ? step : -step ),
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

	const FREE_BORDER_WIDTH = 2;
	const FREE_BORDER_COLOR = '#E5E7EB';
	const FREE_BORDER_STYLE = 'solid';
	const FREE_RADIUS = 16;
	const effBorderWidth = containerBorder ? FREE_BORDER_WIDTH : 0;
	const effBorderColor = FREE_BORDER_COLOR;
	const effBorderStyle = FREE_BORDER_STYLE;
	const effRadiusTL = containerRounded ? FREE_RADIUS : 0;
	const effRadiusTR = containerRounded ? FREE_RADIUS : 0;
	const effRadiusBR = containerRounded ? FREE_RADIUS : 0;
	const effRadiusBL = containerRounded ? FREE_RADIUS : 0;
	const containerHasBorder = effBorderWidth > 0;
	const containerBorderValue = containerHasBorder
		? `${ effBorderWidth }px ${ effBorderStyle } ${ effBorderColor }`
		: 'none';

	const containerBoxShadowValue = containerShadow
		? `0px 8px 20px 0px ${ toRgba( '#000000', 35 ) }`
		: 'none';

	const wrapCard = showCaptionBlock && containerShadow;

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
		aspectRatio,
		...( realHeight !== null ? { height: `${ realHeight }px` } : {} ),
		overflow: 'hidden',
		userSelect: 'none',
		touchAction: 'pan-y',
		cursor: 'ew-resize',
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

	const captionBottomRadius = `${ effRadiusBR }px ${ effRadiusBL }px`;

	const captionStyle = {
		backgroundColor: captionBg,
		textAlign: [ 'left', 'center', 'right' ].includes( captionAlignment )
			? captionAlignment
			: 'center',
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

	const cardStyle = {
		boxShadow: containerBoxShadowValue,
		borderRadius: `${ effRadiusTL }px ${ effRadiusTR }px ${ captionBottomRadius }`,
	};

	const sliderElement = (
		<div
			ref={ containerRef }
			className="pixvisualize-slider-container"
			style={ sliderStyle }
			onPointerDown={ onPointerDown }
			onPointerMove={ onPointerMove }
			onPointerUp={ onPointerUp }
			onPointerCancel={ onPointerCancel }
			onDragStart={ ( event ) => event.preventDefault() }
		>
			<ImageLayerFree
				imageSrc={ afterImage }
				isBefore={ false }
				position={ position }
			>
				{ showLabels && (
					<div
						className="pixvisualize-label pixvisualize-label-after"
						style={ afterLabelStyle }
					>
						{ afterLabel || 'After' }
					</div>
				) }
			</ImageLayerFree>
			<ImageLayerFree
				imageSrc={ beforeImage }
				isBefore
				position={ position }
			>
				{ showLabels && (
					<div
						className="pixvisualize-label pixvisualize-label-before"
						style={ beforeLabelStyle }
					>
						{ beforeLabel || 'Before' }
					</div>
				) }
			</ImageLayerFree>
			<HandleFree
				position={ position }
				onPointerDown={ onPointerDown }
				onKeyDown={ onKeyDown }
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

export default CompareSliderFree;
