import React from 'react';
import { __ } from '@wordpress/i18n';

const HandleFree = ( { position, onPointerDown, onKeyDown } ) => {
	return (
		<div
			className="pixvisualize-handle"
			style={ {
				position: 'absolute',
				top: '50%',
				left: `${ position }%`,
				transform: 'translate(-50%, -50%)',
				cursor: 'ew-resize',
				backgroundColor: 'transparent',
				touchAction: 'none',
				zIndex: 10,
				filter: 'drop-shadow(0 0 6px rgba(0, 0, 0, 0.6))',
			} }
			onPointerDown={ onPointerDown }
			role="slider"
			aria-valuemin="0"
			aria-valuemax="100"
			aria-valuenow={ Math.round( position ) }
			aria-label={ __( 'Image comparison slider', 'pixvisualize' ) }
			aria-orientation="horizontal"
			tabIndex="0"
			onKeyDown={ onKeyDown }
		>
			<div
				className="pixvisualize-handle-line"
				style={ {
					width: '2px',
					height: '100%',
					backgroundColor: '#ffffff',
					boxShadow: 'none',
				} }
			/>
			<div
				className="pixvisualize-handle-circle"
				style={ {
					backgroundColor: '#ffffff',
					width: '24px',
					height: '24px',
					borderRadius: '50%',
					overflow: 'hidden',
					boxSizing: 'border-box',
					flexShrink: 0,
					display: 'flex',
					alignItems: 'center',
					justifyContent: 'center',
					position: 'relative',
					zIndex: 1,
					boxShadow: 'none',
				} }
			/>
			<div
				className="pixvisualize-handle-arrows"
				style={ {
					color: '#666666',
					width: '16px',
					height: '16px',
					zIndex: 2,
				} }
			>
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path d="M9 6l-5 6 5 6" />
					<path d="M15 6l5 6-5 6" />
				</svg>
			</div>
		</div>
	);
};

export default HandleFree;
