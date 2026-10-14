import { useState, useEffect, useCallback, useRef } from 'react';
import { clamp } from '../utils/clamp';
import { calculatePercentage } from '../utils/percentage';

export const useDrag = (
	initialPosition = 50,
	onPositionChange,
	axis = 'x'
) => {
	const [ position, setPosition ] = useState( initialPosition );
	const [ isDragging, setIsDragging ] = useState( false );
	const draggingRef = useRef( false );
	const containerRef = useRef( null );

	useEffect( () => {
		if ( ! draggingRef.current ) {
			setPosition( initialPosition );
		}
	}, [ initialPosition ] );

	const updatePosition = useCallback(
		( clientX, clientY ) => {
			if ( ! containerRef.current ) {
				return;
			}

			const rect = containerRef.current.getBoundingClientRect();
			const isVertical = axis === 'y';
			const client = isVertical ? clientY : clientX;
			const offset = isVertical ? rect.top : rect.left;
			const size = isVertical ? rect.height : rect.width;
			const percentage = clamp(
				calculatePercentage( client - offset, size ),
				0,
				100
			);

			setPosition( percentage );
			if ( onPositionChange ) {
				onPositionChange( percentage );
			}
		},
		[ onPositionChange, axis ]
	);

	const releaseCapture = useCallback( ( e ) => {
		if ( containerRef.current && typeof e.pointerId === 'number' ) {
			try {
				containerRef.current.releasePointerCapture?.( e.pointerId );
			} catch {
				// Ignore invalid pointer capture releases.
			}
		}
	}, [] );

	const onPointerDown = useCallback(
		( e ) => {
			// Ne jamais bloquer le scroll tactile au pointerdown :
			// avec touch-action: pan-y / pan-x le navigateur décide
			// (scroll vs drag). preventDefault uniquement pour la
			// souris (sélection texte / drag image natif).
			if ( e.pointerType === 'mouse' ) {
				e.preventDefault();
			}
			draggingRef.current = true;
			if ( containerRef.current && typeof e.pointerId === 'number' ) {
				containerRef.current.setPointerCapture?.( e.pointerId );
			}
			setIsDragging( true );
			updatePosition( e.clientX, e.clientY );
		},
		[ updatePosition ]
	);

	const onPointerMove = useCallback(
		( e ) => {
			if ( ! draggingRef.current ) {
				return;
			}
			updatePosition( e.clientX, e.clientY );
		},
		[ updatePosition ]
	);

	const onPointerUp = useCallback(
		( e ) => {
			draggingRef.current = false;
			releaseCapture( e );
			setIsDragging( false );
		},
		[ releaseCapture ]
	);

	const onPointerCancel = useCallback(
		( e ) => {
			draggingRef.current = false;
			releaseCapture( e );
			setIsDragging( false );
		},
		[ releaseCapture ]
	);

	return {
		position,
		setPosition,
		moveTo: updatePosition,
		isDragging,
		onPointerDown,
		onPointerMove,
		onPointerUp,
		onPointerCancel,
		containerRef,
	};
};
