import React from 'react';
import { __ } from '@wordpress/i18n';
import { clamp } from '../utils/clamp';

const Handle = ( {
	position,
	onPointerDown,
	onKeyDown,
	orientation = 'horizontal',
	handleStyle = 'circle',
	handleIconSvg = '',
	handleSize = 24,
	handleBackground = '',
	handleArrowColor = '#666666',
	handlePadding = 0,
	separator = { thickness: 2, color: '#ffffff', shadow: true },
} ) => {
	const isVertical = orientation === 'vertical';
	const resolvedStyle = handleStyle === 'custom' && ! handleIconSvg ? 'circle' : handleStyle;
	const isTransparent = resolvedStyle === 'transparent';
	// Circle diameter (with background) or glyph box (without background).
	const circleSize = Number.isFinite( handleSize )
		? clamp( handleSize, 16, 64 )
		: 24;
	// Inner content keeps breathing room inside the circle.
	const innerSize = Math.max( 12, Math.round( circleSize * 0.65 ) );
	// Standalone double arrows (no background) stay larger than the circle.
	const arrowsSize = circleSize + 10;
	// Single synchronized control: the vertical bar and the disc share the
	// same background (separator.color, default #FFFFFF). The legacy
	// handleBackground attribute is kept for backward compatibility: when
	// set (old content), it still wins; new content leaves it empty so the
	// disc always follows the separator.
	const discBackground =
		handleBackground ||
		( isTransparent ? 'transparent' : separator.color || '#ffffff' );
	// Independent arrows color control (default #666666).
	const arrowColor = handleArrowColor || '#666666';
	// Inner spacing between the glyph and the disc edge (custom SVG was
	// glued to the edge). Zero keeps the historical rendering.
	const glyphPadding = Number.isFinite( handlePadding )
		? clamp( handlePadding, 0, 20 )
		: 0;

	const separatorStyle = {
		width: isVertical ? '100%' : `${ separator.thickness }px`,
		height: isVertical ? `${ separator.thickness }px` : '100%',
		backgroundColor: separator.color,
		// No box-shadow here: the grouped drop-shadow lives on the parent
		// via filter so no shadow appears at the circle/bar junction.
		boxShadow: 'none',
	};

	// Grouped drop-shadow: applied once on the parent container so the bar
	// + handle cast a single unified shadow (no overlap at the junction).
	const groupedShadow = separator.shadow
		? 'drop-shadow(0 0 6px rgba(0, 0, 0, 0.6))'
		: 'none';

	return (
		<div
			className={
				'pixvisualize-handle' +
				( resolvedStyle !== 'circle' ? ` pixvisualize-handle--${ resolvedStyle }` : '' )
			}
			style={ {
				position: 'absolute',
				top: isVertical ? `${ position }%` : '50%',
				left: isVertical ? '50%' : `${ position }%`,
				transform: 'translate(-50%, -50%)',
				cursor: isVertical ? 'ns-resize' : 'ew-resize',
				backgroundColor: 'transparent',
				touchAction: 'none',
				zIndex: 10,
				filter: groupedShadow,
				'--pixvisualize-accent-color': separator.color,
			} }
			onPointerDown={ onPointerDown }
			role="slider"
			aria-valuemin="0"
			aria-valuemax="100"
			aria-valuenow={ Math.round( position ) }
			aria-label={ __( 'Image comparison slider', 'pixvisualize' ) }
			aria-orientation={ orientation }
			tabIndex="0"
			onKeyDown={ onKeyDown }
			data-handle-style={ handleStyle }
			data-orientation={ orientation }
			data-handle-icon-svg={ handleIconSvg }
			data-handle-size={ circleSize }
			data-handle-arrow-color={ arrowColor }
			data-separator-thickness={ separator.thickness }
			data-separator-color={ separator.color }
			data-separator-shadow={ separator.shadow ? '1' : '0' }
		>
			<div className="pixvisualize-handle-line" style={ separatorStyle } />
			{ resolvedStyle === 'custom' && (
				<div
					className="pixvisualize-handle-svg"
					style={ {
						color: arrowColor,
						// Local scope for fill="var(--svg-fill, currentColor)":
						// wins over any theme-level --svg-fill (dark mode,
						// matching background) and falls back to color.
						'--svg-fill': arrowColor,
						width: `${ circleSize }px`,
						height: `${ circleSize }px`,
						padding: `${ glyphPadding }px`,
						boxSizing: 'border-box',
						boxShadow: 'none',
						// Above the circle background disc (already
						// absolutely positioned by its CSS class).
						zIndex: 2,
					} }
					dangerouslySetInnerHTML={ { __html: handleIconSvg } }
				/>
			) }
			{ resolvedStyle !== 'line' && (
				<div
					className="pixvisualize-handle-circle"
					style={ {
						backgroundColor: discBackground,
						width: `${ circleSize }px`,
						height: `${ circleSize }px`,
						// Self-contained disc geometry: the circle shape must
						// never depend on the stylesheet (cache mismatch or
						// theme override would distort the handle).
						borderRadius: '50%',
						overflow: 'hidden',
						boxSizing: 'border-box',
						flexShrink: 0,
						display: 'flex',
						alignItems: 'center',
						justifyContent: 'center',
						// Above the separator line (absolute): otherwise the
						// line and its drop shadow overlap the handle.
						position: 'relative',
						zIndex: 1,
						// No box-shadow: grouped filter on the parent.
						boxShadow: 'none',
					} }
				/>
			) }
			{ ( resolvedStyle === 'circle' ||
				resolvedStyle === 'arrows' ||
				isTransparent ) && (
				<div
					className="pixvisualize-handle-arrows"
					style={ {
						color: arrowColor,
						width: `${ resolvedStyle === 'arrows' ? arrowsSize : innerSize }px`,
						height: `${ resolvedStyle === 'arrows' ? arrowsSize : innerSize }px`,
						padding: `${ glyphPadding }px`,
						boxSizing: 'border-box',
						// Above the circle background disc (which now
						// carries position + z-index against the line).
						zIndex: 2,
					} }
				>
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
						{ resolvedStyle === 'arrows' ? (
							isVertical ? (
								<>
									<path d="M7 9l5-5 5 5" />
									<path d="M7 15l5 5 5-5" />
									<path d="M12 6v12" />
								</>
							) : (
								<>
									<path d="M9 7l-5 5 5 5" />
									<path d="M15 7l5 5-5 5" />
									<path d="M6 12h12" />
								</>
							)
						) : isVertical ? (
							<>
								<path d="M6 9 L12 4 L18 9" />
								<path d="M6 15 L12 20 L18 15" />
							</>
						) : (
							<>
								<path d="M9 6l-5 6 5 6" />
								<path d="M15 6l5 6-5 6" />
							</>
						) }
					</svg>
				</div>
			) }
		</div>
	);
};

export default Handle;