import { useEffect } from 'react';

export const usePointer = ( ref, handlers ) => {
	const { onPointerDown, onPointerMove, onPointerUp } = handlers;

	useEffect( () => {
		const element = ref.current;
		if ( ! element ) {
			return;
		}

		element.addEventListener( 'pointerdown', onPointerDown );
		element.addEventListener( 'pointermove', onPointerMove );
		window.addEventListener( 'pointerup', onPointerUp );

		return () => {
			element.removeEventListener( 'pointerdown', onPointerDown );
			element.removeEventListener( 'pointermove', onPointerMove );
			window.removeEventListener( 'pointerup', onPointerUp );
		};
	}, [ ref, onPointerDown, onPointerMove, onPointerUp ] );
};

export default usePointer;
