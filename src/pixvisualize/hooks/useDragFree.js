import { useState, useEffect, useCallback, useRef } from 'react';
import { clamp } from '../utils/clamp';
import { calculatePercentage } from '../utils/percentage';

export const useDragFree = ( initialPosition = 50, onPositionChange ) => {
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
		( clientX ) => {
			if ( ! containerRef.current ) {
				return;
			}

			const rect = containerRef.current.getBoundingClientRect();
			const percentage = clamp(
				calculatePercentage( clientX - rect.left, rect.width ),
				0,
				100
			);

			setPosition( percentage );
			if ( onPositionChange ) {
				onPositionChange( percentage );
			}
		},
		[ onPositionChange ]
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
			if ( e.pointerType === 'mouse' ) {
				e.preventDefault();
			}
			draggingRef.current = true;
			if ( containerRef.current && typeof e.pointerId === 'number' ) {
				containerRef.current.setPointerCapture?.( e.pointerId );
			}
			setIsDragging( true );
			updatePosition( e.clientX );
		},
		[ updatePosition ]
	);

	const onPointerMove = useCallback(
		( e ) => {
			if ( ! draggingRef.current ) {
				return;
			}
			updatePosition( e.clientX );
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
