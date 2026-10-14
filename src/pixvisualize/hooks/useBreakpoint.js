import { useState, useEffect } from 'react';

const getBreakpoint = () => {
	if ( typeof window === 'undefined' || ! window.matchMedia ) {
		return 'desktop';
	}
	if ( window.matchMedia( '(max-width: 767px)' ).matches ) {
		return 'mobile';
	}
	if ( window.matchMedia( '(max-width: 1024px)' ).matches ) {
		return 'tablet';
	}
	return 'desktop';
};

const useBreakpoint = () => {
	const [ breakpoint, setBreakpoint ] = useState( getBreakpoint );

	useEffect( () => {
		if ( typeof window === 'undefined' || ! window.matchMedia ) {
			return undefined;
		}

		const mobileQuery = window.matchMedia( '(max-width: 767px)' );
		const tabletQuery = window.matchMedia( '(max-width: 1024px)' );

		const update = () => setBreakpoint( getBreakpoint );

		update();
		mobileQuery.addEventListener( 'change', update );
		tabletQuery.addEventListener( 'change', update );

		return () => {
			mobileQuery.removeEventListener( 'change', update );
			tabletQuery.removeEventListener( 'change', update );
		};
	}, [] );

	return breakpoint;
};

export default useBreakpoint;
