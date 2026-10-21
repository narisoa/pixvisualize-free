window.addEventListener( 'load', function () {
	// store tabs variables
	const tabs = document.querySelectorAll( 'ul.nav-tabs > li' );

	for ( let i = 0; i < tabs.length; i++ ) {
		tabs[ i ].addEventListener( 'click', switchTab );
	}

	function switchTab( event ) {
		event.preventDefault();

		const activeTab = document.querySelector( 'ul.nav-tabs li.active' );
		if ( activeTab ) {
			activeTab.classList.remove( 'active' );
		}

		const activePane = document.querySelector( '.tab-pane.active' );
		if ( activePane ) {
			activePane.classList.remove( 'active' );
		}

		const clickedTab = event.currentTarget;
		// NOTE: read href from the anchor itself, not event.target —
		// clicks may land on a child (e.g. the dashicon), which has
		// no href and left a blank pane (tab active, no content).
		const anchor = clickedTab.querySelector( 'a' );
		const activePaneID = anchor ? anchor.getAttribute( 'href' ) : null;

		clickedTab.classList.add( 'active' );
		if ( activePaneID ) {
			const pane = document.querySelector( activePaneID );
			if ( pane ) {
				pane.classList.add( 'active' );
			}
		}
	}
} );
