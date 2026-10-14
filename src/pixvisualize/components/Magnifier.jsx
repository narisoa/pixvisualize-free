import React, { useEffect, useState } from 'react';

const MAGNIFIER_SIZE = 160;

const Magnifier = ( {
	enabled,
	zoom = 2,
	beforeImage,
	afterImage,
	position = 50,
	orientation = 'horizontal',
	containerRef,
} ) => {
	const [ pointer, setPointer ] = useState( null );

	useEffect( () => {
		const container = containerRef.current;
		if ( ! enabled || ! container ) {
			return undefined;
		}

		const handlePointerMove = ( e ) => {
			if ( e.pointerType !== 'mouse' ) {
				return;
			}
			const rect = container.getBoundingClientRect();
			setPointer( {
				x: e.clientX - rect.left,
				y: e.clientY - rect.top,
				width: rect.width,
				height: rect.height,
			} );
		};

		const handlePointerLeave = () => {
			setPointer( null );
		};

		container.addEventListener( 'pointermove', handlePointerMove );
		container.addEventListener( 'pointerleave', handlePointerLeave );

		return () => {
			container.removeEventListener( 'pointermove', handlePointerMove );
			container.removeEventListener( 'pointerleave', handlePointerLeave );
		};
	}, [ enabled, containerRef ] );

	if ( ! enabled || ! pointer || ! pointer.width || ! pointer.height ) {
		return null;
	}

	const half = MAGNIFIER_SIZE / 2;
	const translateX = half - pointer.x * zoom;
	const translateY = half - pointer.y * zoom;

	const visiblePercent =
		orientation === 'vertical'
			? ( pointer.y / pointer.height ) * 100
			: ( pointer.x / pointer.width ) * 100;
	const imageSrc = visiblePercent <= position ? beforeImage : afterImage;

	return (
		<div
			className="pixvisualize-magnifier"
			style={ {
				position: 'absolute',
				left: pointer.x - half,
				top: pointer.y - half,
				width: MAGNIFIER_SIZE,
				height: MAGNIFIER_SIZE,
				zIndex: 8,
				pointerEvents: 'none',
				borderRadius: '50%',
				overflow: 'hidden',
				border: '3px solid #fff',
				boxShadow: '0 0 20px rgba(0, 0, 0, 0.5)',
			} }
		>
			<img
				src={ imageSrc }
				alt=""
				draggable={ false }
				style={ {
					width: pointer.width * zoom,
					height: pointer.height * zoom,
					maxWidth: 'none',
					maxHeight: 'none',
					display: 'block',
					objectFit: 'cover',
					transform: `translate(${ translateX }px, ${ translateY }px)`,
				} }
			/>
		</div>
	);
};

export default Magnifier;