/**
 * @param {jQuery} $
 *
 *                   Disables (greys out) the Height control in the Elementor editor panel
 *                   when the "Full Width" switch is enabled.
 *
 *                   Elementor has no native "disabled" state for controls, so this script:
 *                   - waits for `elementor:init` before hooking the panel events,
 *                   - targets the controls via their `data-setting` attributes,
 *                   - disables the Height inputs while Full Width is checked,
 *                   - adds a custom class on the Height wrapper for the greyed look,
 *                   - reacts in real time to the switch via delegated 'change'/'input'
 *                   events (delegation survives React/Backbone re-renders of the panel),
 *                   - re-syncs through a MutationObserver for programmatic changes
 *                   (undo, history, navigation).
 *
 *                   Debug: console.log traces are prefixed with "[PixVisualize]".
 */
/* global elementor, MutationObserver, jQuery */
( function ( $ ) {
	'use strict';

	const DEBUG = true;

	function log() {
		if ( DEBUG && window.console && window.console.log ) {
			window.console.log.apply( window.console, arguments );
		}
	}

	// Simulate a resize on the preview iframe so the React slider re-runs its
	// layout calculations. Elementor injects the widget markup (and images)
	// into the iframe asynchronously after the page load, so the slider's
	// initial dimensions are stale and the widget appears as a grey/empty zone.
	let resizeTimer = null;

	function triggerResize( delay ) {
		window.clearTimeout( resizeTimer );
		resizeTimer = window.setTimeout( function () {
			const previewWindow =
				window.elementor &&
				elementor.$previewContents &&
				elementor.$previewContents[ 0 ] &&
				elementor.$previewContents[ 0 ].defaultView;

			if ( ! previewWindow ) {
				log(
					'[PixVisualize] triggerResize(): preview window not ready.'
				);
				return;
			}

			try {
				previewWindow.dispatchEvent(
					new previewWindow.Event( 'resize', { bubbles: true } )
				);
				log(
					'[PixVisualize] Resize event simulated on preview iframe.'
				);
			} catch ( e ) {
				log( '[PixVisualize] triggerResize() failed:', e );
			}
		}, delay || 150 );
	}

	const observedPanels = new WeakSet();

	/**
	 * Remove Elementor's native Advanced > Border controls for our widget.
	 *
	 * Official filter: the editor merges the shared common controls into each
	 * widget (see elements/widget/controls/common/default in Elementor core).
	 * The native border targets the outer widget container while Style >
	 * Container styles the inner slider card — keeping both would stack two
	 * nested borders. Works on a copy: the shared common object must stay
	 * intact for the other widgets.
	 *
	 * @param {Object} controls   Common controls object.
	 * @param {string} widgetType Widget type being edited.
	 * @return {Object} Filtered controls.
	 */
	function stripNativeBorderControls( controls, widgetType ) {
		if ( 'pixvisualize' !== widgetType || ! controls ) {
			return controls;
		}

		const filtered = Object.assign( {}, controls );

		Object.keys( filtered ).forEach( function ( key ) {
			if ( '_section_border' === key ) {
				delete filtered[ key ];
				return;
			}

			const control = filtered[ key ];

			if ( control && '_section_border' === control.section ) {
				delete filtered[ key ];
			}
		} );

		log(
			'[PixVisualize] Native Advanced > Border hidden for pixvisualize.'
		);

		return filtered;
	}

	/**
	 * Wire the full_width <-> height logic for the currently open panel.
	 *
	 * @param {Object} panel Elementor panel view.
	 * @param {Object} model Elementor widget model.
	 */
	function setup( panel, model ) {
		const $panel = panel.$el;

		log(
			'[PixVisualize] setup() called. panel.$el found:',
			!! $panel.length
		);

		// Always re-query the DOM: control nodes get replaced on re-render.
		function sync() {
			const $height = $panel.find( '[data-setting="height"]' );

			if ( ! $height.length ) {
				log(
					'[PixVisualize] sync(): height control NOT found in panel.'
				);
				return;
			}

			const $fullWidthInput = $panel.find(
				'[data-setting="full_width"] input[type="checkbox"]'
			);

			const isFullWidth = $fullWidthInput.length
				? $fullWidthInput.prop( 'checked' )
				: 'yes' === model.getSetting( 'full_width' );

			log(
				'[PixVisualize] sync(): full_width switch found:',
				$fullWidthInput.length > 0,
				'| checked =',
				isFullWidth
			);

			// 3. + 4. Disable / re-enable the Height inputs.
			$height.find( 'input, button' ).prop( 'disabled', isFullWidth );

			// 5. Grey out the Height wrapper.
			$height.toggleClass( 'my-disabled-control', isFullWidth );
			$height.attr( 'aria-disabled', isFullWidth ? 'true' : 'false' );

			log(
				'[PixVisualize] sync(): height wrapper',
				isFullWidth ? 'DISABLED' : 'enabled',
				'(class applied:',
				$height.hasClass( 'my-disabled-control' ) + ')'
			);
		}

		// 6. Real-time updates: delegated events on the switch (change + input).
		$panel.off(
			'change.pixvisualize input.pixvisualize',
			'[data-setting="full_width"] input[type="checkbox"]'
		);
		$panel.on(
			'change.pixvisualize input.pixvisualize',
			'[data-setting="full_width"] input[type="checkbox"]',
			function () {
				log( '[PixVisualize] switch change/input event fired.' );
				sync();
			}
		);

		// Fallback: keep in sync when the panel DOM is re-rendered asynchronously.
		if ( ! observedPanels.has( $panel[ 0 ] ) ) {
			observedPanels.add( $panel[ 0 ] );

			if ( 'MutationObserver' in window ) {
				const observer = new MutationObserver( function () {
					log(
						'[PixVisualize] MutationObserver fired (DOM changed).'
					);
					sync();
				} );
				observer.observe( $panel[ 0 ], {
					childList: true,
					subtree: true,
					attributes: true,
				} );
			}
		}

		// 1. Initial state once the panel is open.
		sync();

		// Re-trigger the slider layout in case the preview re-rendered while
		// the panel was closed (images injected after load).
		triggerResize( 250 );
	}

	/**
	 * Register the panel hooks. Called once Elementor's editor is ready.
	 */
	function onElementorInit() {
		log(
			'[PixVisualize] onElementorInit() - elementor.hooks available:',
			!! ( window.elementor && window.elementor.hooks )
		);

		if ( ! window.elementor || ! window.elementor.hooks ) {
			return;
		}

		// Named hook: fires when the PixVisualize widget panel opens.
		elementor.hooks.addAction(
			'panel/open_editor/widget/pixvisualize',
			function ( panel, model ) {
				log(
					'[PixVisualize] HOOK fired: panel/open_editor/widget/pixvisualize. model =',
					model
				);
				setup( panel, model );
			}
		);

		// Fallback: generic hook in case the named one is skipped.
		elementor.hooks.addAction(
			'panel/open_editor/widget',
			function ( panel, model ) {
				const widgetType =
					model && model.get ? model.get( 'widgetType' ) : null;

				log(
					'[PixVisualize] HOOK fired: panel/open_editor/widget. widgetType =',
					widgetType
				);

				if ( 'pixvisualize' === widgetType ) {
					setup( panel, model );
				}
			}
		);

		log( '[PixVisualize] Elementor widget hooks registered.' );

		// Official filters: strip the native Advanced > Border section (and its
		// controls) for our widget only. The merge happens lazily on first
		// panel open, so registering at init is early enough. Both common
		// stacks covered (regular + optimized markup).
		elementor.hooks.addFilter(
			'elements/widget/controls/common/default',
			stripNativeBorderControls
		);
		elementor.hooks.addFilter(
			'elements/widget/controls/common-optimized/default',
			stripNativeBorderControls
		);

		// When the preview iframe finishes loading, simulate a resize after a
		// short delay so Elementor has time to inject the widget markup and
		// images before the slider recalculates its dimensions.
		$( window ).on( 'elementor:preview:loaded', function () {
			log(
				'[PixVisualize] Preview loaded, scheduling resize simulation.'
			);
			triggerResize( 400 );
			triggerResize( 1000 );
		} );
	}

	$( function () {
		log( '[PixVisualize] Script loaded (DOM ready).' );

		if ( window.elementor && window.elementor.hooks ) {
			log(
				'[PixVisualize] Editor already initialized, registering now.'
			);
			onElementorInit();
		} else {
			log( '[PixVisualize] Waiting for elementor:init...' );
			$( window ).on( 'elementor:init', onElementorInit );
		}
	} );
} )( jQuery );
