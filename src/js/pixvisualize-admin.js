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

/* global jQuery */
// Shortcode Generator
const bootPixVisualizeGenerator = function () {
	const root = document.querySelector( '.pixvisualize-generator' );
	if ( ! root ) {
		return;
	}
	if ( typeof wp === 'undefined' || typeof wp.media === 'undefined' ) {
		// Media scripts not ready yet: retry once on window load instead
		// of waiting for every page asset.
		if ( document.readyState !== 'complete' ) {
			window.addEventListener( 'load', bootPixVisualizeGenerator, {
				once: true,
			} );
		}
		return;
	}

	const state = { before: '', after: '' };

	const DEFAULTS = {
		ratio: '16/9',
		orientation: 'horizontal',
		position: '50',
		height: '450',
		full_width: '0',
		alignment: 'center',
		before_label: 'Before',
		after_label: 'After',
		show_labels: '1',
		label_position_before: 'top-left',
		label_position_after: 'top-right',
		label_bg: '#00000080',
		label_color: '#ffffff',
		label_font_size: '12',
		label_font_weight: '600',
		label_padding_x: '10',
		label_padding_y: '5',
		label_border_radius: '4',
		show_caption: '1',
		caption_title: '',
		caption_text: '',
		caption_bg: '#ffffff',
		caption_title_color: '#1e293b',
		caption_text_color: '#64748b',
		caption_alignment: 'center',
		caption_title_size: '22',
		caption_text_size: '15',
		caption_padding: '16',
		caption_padding_x: '5',
		caption_padding_y: '9',
		handle: 'circle',
		svg: '',
		handle_size: '24',
		handle_bg: '',
		handle_arrow_color: '#666666',
		handle_padding: '0',
		separator_thickness: '2',
		separator_color: '#ffffff',
		separator_shadow: '1',
		hover: '0',
		autoscan: '0',
		autoscan_duration: '2500',
		magnifier: '0',
		magnifier_zoom: '2',
		tablet_position: '50',
		mobile_position: '50',
		tablet_ratio: '',
		mobile_ratio: '',
		container_shadow: '0',
		container_shadow_color: '#000000',
		container_shadow_horizontal: '0',
		container_shadow_vertical: '8',
		container_shadow_blur: '20',
		container_shadow_spread: '0',
		container_shadow_opacity: '35',
		container_border_width: '0',
		// FREE toggles (checkboxes): default 2px solid gray border, default 16px radius.
		container_border: '0',
		container_rounded: '0',
		container_border_color: '#000000',
		container_border_style: 'solid',
		container_border_radius_tl: '0',
		container_border_radius_tr: '0',
		container_border_radius_br: '0',
		container_border_radius_bl: '0',
	};

	// Shortcode attribute order in the generated output.
	const ATTR_ORDER = [
		'orientation',
		'position',
		'height',
		'full_width',
		'alignment',
		'before_label',
		'after_label',
		'show_labels',
		'label_position_before',
		'label_position_after',
		'label_bg',
		'label_color',
		'label_font_size',
		'label_font_weight',
		'label_padding_x',
		'label_padding_y',
		'label_border_radius',
		'show_caption',
		'caption_title',
		'caption_text',
		'caption_bg',
		'caption_title_color',
		'caption_text_color',
		'caption_alignment',
		'caption_title_size',
		'caption_text_size',
		'caption_padding',
		'caption_padding_x',
		'caption_padding_y',
		'handle',
		'svg',
		'handle_size',
		'handle_arrow_color',
		'handle_padding',
		'separator_thickness',
		'separator_color',
		'separator_shadow',
		'hover',
		'autoscan',
		'autoscan_duration',
		'magnifier',
		'magnifier_zoom',
		'tablet_position',
		'mobile_position',
		'tablet_ratio',
		'mobile_ratio',
		'container_shadow',
		'container_shadow_color',
		'container_shadow_horizontal',
		'container_shadow_vertical',
		'container_shadow_blur',
		'container_shadow_spread',
		'container_shadow_opacity',
		'container_border_width',
		'container_border',
		'container_rounded',
		'container_border_color',
		'container_border_style',
		'container_border_radius_tl',
		'container_border_radius_tr',
		'container_border_radius_br',
		'container_border_radius_bl',
	];

	const fieldValue = ( name ) => {
		const el = root.querySelector( '[data-field="' + name + '"]' );
		if ( ! el ) {
			return '';
		}
		if ( el.type === 'checkbox' ) {
			return el.checked ? '1' : '0';
		}
		return el.value;
	};

	const cleanSvg = ( value ) => {
		// Inline SVG only: strip XML prolog, DOCTYPE and potrace metadata.
		// They bloat the shortcode and break shortcode parsing in post_content.
		// Force fill to currentColor so the icon inherits Handle Arrows Color
		// (a var(--svg-fill,...) would resolve against the theme in front,
		// invisible on matching background). Drop width/height in pt: only
		// viewBox matters, CSS forces 100% inside the handle circle.
		let cleaned = String( value )
			.replace( /<\?xml[^?]*\?>\s*/g, '' )
			.replace( /<!DOCTYPE[^>]*>\s*/g, '' )
			.replace( /<metadata>[\s\S]*?<\/metadata>\s*/g, '' );
		// Single-line output: the generated shortcode is shown/copied from
		// an <input>, which strips CR/LF (joining lines and merging numbers
		// in path data). Newlines become plain spaces (safe separator).
		cleaned = cleaned.replace( /\s*[\r\n]+\s*/g, ' ' );
		cleaned = cleaned.replace(
			/var\(\s*--svg-fill\s*,\s*currentColor\s*\)/g,
			'currentColor'
		);
		cleaned = cleaned.replace( /<svg([^>]*)>/i, ( match, attrs ) => {
			const kept = String( attrs )
				.replace( /\s+width\s*=\s*(?:"[^"]*"|'[^']*')/gi, '' )
				.replace( /\s+height\s*=\s*(?:"[^"]*"|'[^']*')/gi, '' );
			return '<svg' + kept + '>';
		} );
		return cleaned.trim();
	};

	const escapeValue = ( value ) => {
		// & first, then < > " : raw < > inside post_content would be parsed
		// as real HTML tags and corrupt the stored shortcode.
		return String( value )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' )
			.trim();
	};

	const updateShortcode = () => {
		const ratio = fieldValue( 'ratio' ) || '16/9';

		let attrs =
			'before="' +
			escapeValue( state.before ) +
			'"' +
			' after="' +
			escapeValue( state.after ) +
			'"' +
			' ratio="' +
			escapeValue( ratio ) +
			'"';

		// Only non-default values are emitted to keep the shortcode concise.
		ATTR_ORDER.forEach( ( name ) => {
			let value = fieldValue( name );
			if ( name === 'svg' ) {
				value = cleanSvg( value );
			}
			if ( value !== '' && value !== DEFAULTS[ name ] ) {
				attrs += ' ' + name + '="' + escapeValue( value ) + '"';
			}
		} );

		const field = root.querySelector( '.pixvisualize-shortcode-field' );
		if ( field ) {
			field.value = '[pixvisualize ' + attrs + ']';
		}

		const copyButton = root.querySelector( '.pixvisualize-copy-button' );
		if ( copyButton ) {
			copyButton.disabled = ! ( state.before && state.after );
		}

		const saveButton = root.querySelector( '.pixvisualize-save-button' );
		if ( saveButton ) {
			saveButton.disabled = ! ( state.before && state.after );
		}
	};

	const previewBox = () =>
		root.querySelector( '.pixvisualize-generator-preview' );

	const clamp = ( value, min, max ) =>
		Math.min( max, Math.max( min, value ) );

	// Write a data-* attribute only when it changes: untouched attributes
	// don't wake the shared preview MutationObserver on every keystroke.
	const setData = ( el, name, value ) => {
		if ( el.dataset[ name ] !== value ) {
			el.dataset[ name ] = value;
		}
	};

	// Guarded DOM writes: re-assigning src / innerHTML / textContent on
	// every keystroke reloads images, rebuilds the handle subtree and wakes
	// the shared preview MutationObserver (childList) even when nothing
	// changed — that is what made the preview jump on each typed character.
	const setSrc = ( img, url ) => {
		if ( img && img.getAttribute( 'src' ) !== url ) {
			img.src = url;
		}
	};

	const setHTML = ( el, html ) => {
		if ( el && el._pixLastHTML !== html ) {
			el._pixLastHTML = html;
			el.innerHTML = html;
		}
	};

	const setText = ( el, value ) => {
		if ( el && el.textContent !== value ) {
			el.textContent = value;
		}
	};

	const numVal = ( name, fallback ) => {
		const parsed = parseInt( fieldValue( name ), 10 );
		return Number.isNaN( parsed ) ? fallback : parsed;
	};

	// Chevron paths (same geometry as the front-end Handle component:
	// 20px chevrons, 34px double arrows).
	const chevronPaths = ( useArrows, isVertical ) => {
		if ( useArrows && isVertical ) {
			return '<path d="M7 9l5-5 5 5"/><path d="M7 15l5 5 5-5"/><path d="M12 6v12"/>';
		}
		if ( useArrows ) {
			return '<path d="M9 7l-5 5 5 5"/><path d="M15 7l5 5-5 5"/><path d="M6 12h12"/>';
		}
		if ( isVertical ) {
			return '<path d="M6 9 L12 4 L18 9"/><path d="M6 15 L12 20 L18 15"/>';
		}
		return '<path d="M9 6l-5 6 5 6"/><path d="M15 6l5 6-5 6"/>';
	};

	const labelPositionCss = ( position, defaultPos ) => {
		switch ( position ) {
			case 'top-left':
				return 'top: 12px; left: 12px;';
			case 'top-right':
				return 'top: 12px; right: 12px;';
			case 'bottom-left':
				return 'bottom: 12px; left: 12px;';
			case 'bottom-right':
				return 'bottom: 12px; right: 12px;';
			default:
				return defaultPos;
		}
	};

	// Normalize rgba() (output by the alpha picker) back to #RRGGBBAA so the
	// value matches the hex format used by the shortcode.
	const toHexAlpha = ( value ) => {
		const m = value
			.trim()
			.match(
				/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/
			);

		if ( ! m ) {
			return value;
		}

		const a =
			m[ 4 ] !== undefined
				? Math.round( parseFloat( m[ 4 ] ) * 255 )
				: 255;

		return (
			'#' +
			[ m[ 1 ], m[ 2 ], m[ 3 ], a ]
				.map( ( n ) =>
					parseInt( n, 10 ).toString( 16 ).padStart( 2, '0' )
				)
				.join( '' )
		);
	};

	// Wrap every "h4 + grid" group into a collapsible card, like the
	// Elementor sections (striped header, see generator.scss).
	const enhanceSections = () => {
		const side = root.querySelector( '.pixvisualize-generator-side' );

		if ( ! side || side.dataset.sectionsReady ) {
			return;
		}
		side.dataset.sectionsReady = '1';

		side.querySelectorAll( 'h4' ).forEach( ( heading ) => {
			const grid = heading.nextElementSibling;

			if (
				! grid ||
				! grid.classList ||
				! grid.classList.contains( 'pixvisualize-config-grid' )
			) {
				return;
			}

			const details = document.createElement( 'details' );
			details.className = 'pixvisualize-gen-section';
			details.open = true;

			const summary = document.createElement( 'summary' );
			summary.className = 'pixvisualize-gen-section__title';
			summary.textContent = heading.textContent;

			heading.replaceWith( details );
			details.append( summary, grid );
		} );
	};

	// Live preview (same markup/behavior as the Elementor editor preview).
	// Hover, auto-scan and magnifier interactivity is provided by the shared
	// pixvisualize-elementor-preview.js script through the data-* attributes.
	const updatePreview = () => {
		const box = previewBox();

		if ( ! box ) {
			return;
		}

		const orientation = fieldValue( 'orientation' ) || 'horizontal';
		const ratio = fieldValue( 'ratio' ) || '16/9';
		const height = numVal( 'height', 450 );
		const position = clamp( numVal( 'position', 50 ), 0, 100 );
		const handle = fieldValue( 'handle' ) || 'circle';
		// Preview with the cleaned SVG so back-office matches the front
		// (shortcode stores the cleaned version).
		const handleSvg = cleanSvg( fieldValue( 'svg' ) );
		const handleSize = clamp( numVal( 'handle_size', 24 ), 16, 64 );
		const arrowInner = Math.max( 12, Math.round( handleSize * 0.65 ) );
		const showLabels = fieldValue( 'show_labels' ) === '1';
		const sepColor = fieldValue( 'separator_color' ) || '#ffffff';
		const hasImage = !! ( state.before || state.after );
		// Caption visibility drives the card look: with a caption the
		// container border wraps slider + caption, without it the slider
		// keeps the full border (same rule as the front-end slider).
		const captionTitleValue = fieldValue( 'caption_title' ).trim();
		const captionTextValue = fieldValue( 'caption_text' ).trim();
		const captionVisible =
			fieldValue( 'show_caption' ) === '1' &&
			( captionTitleValue || captionTextValue );

		const isVertical = orientation === 'vertical';
		const ratioParts = ratio.split( '/' );
		const ratioW = parseFloat( ratioParts[ 0 ] ) || 16;
		const ratioH = parseFloat( ratioParts[ 1 ] ) || 9;
		const boxWidth = Math.round( ( height * ratioW ) / ratioH );

		// Geometry: width/margin live on the card wrapper so the slider
		// and the caption always share the same width whatever the ratio.
		// Container priority: fixed height wins, so the slider width shrinks
		// (centered) to respect the ratio — also in full width.
		const card = root.querySelector( '.pixvisualize-generator-card' );
		if ( card ) {
			card.style.width = 'min(100%, ' + boxWidth + 'px)';
			card.style.maxWidth = '100%';
			card.style.margin = '0 auto';
		}
		box.style.width = '100%';
		box.style.margin = '0';
		box.style.aspectRatio = ratio;
		// Real zone height (same rule as the front-end slider): it always
		// wins, including in full width (100% × fixed height). No fixed
		// height on the empty state so the placeholder keeps its compact size.
		box.style.height = hasImage ? clamp( height, 100, 2000 ) + 'px' : '';
		box.style.setProperty( '--pc-pos', position + '%' );
		setData( box, 'orientation', orientation );

		// Images.
		const beforeImg = box.querySelector(
			'.pixvisualize-elementor-preview__before'
		);
		const afterImg = box.querySelector(
			'.pixvisualize-elementor-preview__after'
		);

		if ( beforeImg ) {
			setSrc( beforeImg, state.before );
			beforeImg.style.display = state.before ? '' : 'none';
		}

		if ( afterImg ) {
			setSrc( afterImg, state.after );
			afterImg.style.display = state.after ? '' : 'none';
		}

		// Container styling (shadow, border, border-radius).
		const containerShadow = fieldValue( 'container_shadow' ) === '1';
		const shadowColor = fieldValue( 'container_shadow_color' ) || '#000000';
		const shadowOpacity =
			clamp( numVal( 'container_shadow_opacity', 35 ), 0, 100 ) / 100;
		const shadowHex = shadowColor
			.trim()
			.match( /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/ );
		let shadowRgb = 'rgba(0, 0, 0, ' + shadowOpacity + ')';
		if ( shadowHex ) {
			let hex = shadowHex[ 1 ];
			if ( 3 === hex.length ) {
				hex = hex
					.split( '' )
					.map( ( ch ) => ch + ch )
					.join( '' );
			}
			const hr = parseInt( hex.substr( 0, 2 ), 16 );
			const hg = parseInt( hex.substr( 2, 2 ), 16 );
			const hb = parseInt( hex.substr( 4, 2 ), 16 );
			shadowRgb =
				'rgba(' +
				hr +
				', ' +
				hg +
				', ' +
				hb +
				', ' +
				shadowOpacity +
				')';
		}
		const shadowValue = containerShadow
			? numVal( 'container_shadow_horizontal', 0 ) +
			  'px ' +
			  numVal( 'container_shadow_vertical', 8 ) +
			  'px ' +
			  numVal( 'container_shadow_blur', 20 ) +
			  'px ' +
			  numVal( 'container_shadow_spread', 0 ) +
			  'px ' +
			  shadowRgb
			: 'none';
		// Card look (shadow): with a caption the shadow wraps the whole
		// card, otherwise it stays on the slider alone (front-end rule).
		// Moving it avoids two stacked shadows (slider + card).
		const wrapCard = captionVisible && containerShadow;
		if ( card ) {
			card.style.boxShadow = wrapCard ? shadowValue : 'none';
		}
		box.style.boxShadow = wrapCard ? 'none' : shadowValue;

		const borderWidth = numVal( 'container_border_width', 0 );
		// FREE toggles (checkboxes): default 2px solid gray border, default
		// 16px radius. PRO customization (width/color/style, per-corner
		// radius) wins when set.
		const showBorder = fieldValue( 'container_border' ) === '1';
		const roundedCorners = fieldValue( 'container_rounded' ) === '1';
		let effBorderWidth = 0;
		let effBorderColor = '#E5E7EB';
		let effBorderStyle = 'solid';
		if ( borderWidth > 0 ) {
			effBorderWidth = borderWidth;
			effBorderColor =
				fieldValue( 'container_border_color' ) || '#000000';
			effBorderStyle = fieldValue( 'container_border_style' ) || 'solid';
		} else if ( showBorder ) {
			effBorderWidth = 2;
		}
		const borderValue = effBorderWidth
			? effBorderWidth + 'px ' + effBorderStyle + ' ' + effBorderColor
			: 'none';
		const resolveRadius = ( fieldName ) => {
			const raw = numVal( fieldName, 0 );
			if ( raw > 0 ) {
				return raw;
			}
			return roundedCorners ? 16 : 0;
		};
		const radiusTL = resolveRadius( 'container_border_radius_tl' );
		const radiusTR = resolveRadius( 'container_border_radius_tr' );
		const radiusBR = resolveRadius( 'container_border_radius_br' );
		const radiusBL = resolveRadius( 'container_border_radius_bl' );
		// Reset the shorthand first so stale per-side borders from a
		// previous refresh never linger when the caption toggles.
		box.style.border = 'none';
		if ( captionVisible ) {
			// Card look: top/side border on the slider, the caption
			// continues it at the bottom (no double line in between).
			if ( effBorderWidth ) {
				box.style.borderTop = borderValue;
				box.style.borderLeft = borderValue;
				box.style.borderRight = borderValue;
			}
			box.style.borderRadius = radiusTL + 'px ' + radiusTR + 'px 0 0';
		} else {
			box.style.border = borderValue;
			box.style.borderRadius =
				radiusTL +
				'px ' +
				radiusTR +
				'px ' +
				radiusBR +
				'px ' +
				radiusBL +
				'px';
		}

		// Interactions (read by the shared preview script).
		setData( box, 'hoverMode', fieldValue( 'hover' ) === '1' ? '1' : '0' );
		setData(
			box,
			'autoScan',
			fieldValue( 'autoscan' ) === '1' ? '1' : '0'
		);
		setData(
			box,
			'autoScanDuration',
			fieldValue( 'autoscan_duration' ) || '2500'
		);
		setData(
			box,
			'magnifier',
			fieldValue( 'magnifier' ) === '1' ? '1' : '0'
		);
		setData( box, 'magnifierZoom', fieldValue( 'magnifier_zoom' ) || '2' );

		// Handle.
		const sepShadow = fieldValue( 'separator_shadow' ) === '1';
		const handleEl = box.querySelector(
			'.pixvisualize-elementor-preview__handle'
		);

		if ( handleEl ) {
			const nextHandleClass =
				'pixvisualize-elementor-preview__handle pixvisualize-elementor-preview__handle--' +
				( isVertical ? 'vertical' : 'horizontal' );
			if ( handleEl.className !== nextHandleClass ) {
				handleEl.className = nextHandleClass;
			}
			handleEl.style.display = hasImage ? '' : 'none';
			// Grouped shadow on the parent (single element look, no seam).
			handleEl.style.filter = sepShadow
				? 'drop-shadow(0 0 6px rgba(0, 0, 0, 0.6))'
				: 'none';
		}

		const line = box.querySelector(
			'.pixvisualize-elementor-preview__handle-line'
		);

		if ( line ) {
			const thickness = fieldValue( 'separator_thickness' ) || '2';

			line.style.cssText =
				( isVertical ? 'height: ' : 'width: ' ) +
				thickness +
				'px; ' +
				( isVertical ? 'width: 100%;' : 'height: 100%;' ) +
				'background-color: ' +
				sepColor +
				'; box-shadow: none;';
		}

		const icon = box.querySelector(
			'.pixvisualize-elementor-preview__handle-icon'
		);

		if ( icon ) {
			if ( handle === 'line' ) {
				icon.style.display = 'none';
			} else {
				const isTransparent =
					handle === 'transparent' || handle === 'arrows';
				icon.style.display = '';
				icon.style.width = handleSize + 'px';
				icon.style.height = handleSize + 'px';
				// Self-contained disc geometry (same as the front-end
				// handle): never depend on the stylesheet for the shape.
				icon.style.borderRadius = '50%';
				icon.style.overflow = 'hidden';
				icon.style.boxSizing = 'border-box';
				icon.style.flexShrink = '0';
				// Single synchronized control: bar + disc share sepColor.
				// Legacy handle_bg still wins when set (old content).
				const legacyBg = root.querySelector(
					'[data-field="handle_bg"]'
				)
					? fieldValue( 'handle_bg' ).trim()
					: '';
				const discBg =
					legacyBg || ( isTransparent ? 'transparent' : sepColor );
				// Inner spacing between the glyph and the disc edge (custom
				// SVG was glued to the edge). Zero keeps history.
				const glyphPadding = clamp(
					numVal( 'handle_padding', 0 ),
					0,
					20
				);
				const arrowColor =
					fieldValue( 'handle_arrow_color' ).trim() || '#666666';
				icon.style.background = discBg;
				icon.style.color = arrowColor;
				// Local scope for fill="var(--svg-fill, currentColor)".
				icon.style.setProperty( '--svg-fill', arrowColor );
				// No box-shadow on children: grouped filter on the parent.
				icon.style.boxShadow = 'none';
				if ( handle === 'custom' && handleSvg ) {
					setHTML( icon, handleSvg );
					icon.style.padding = glyphPadding + 'px';
				} else {
					icon.style.padding = '0';
					const isArrows = handle === 'arrows';
					const arrowSize = isArrows ? handleSize + 10 : arrowInner;
					setHTML(
						icon,
						'<span class="pixvisualize-elementor-preview__arrows" style="width: ' +
							arrowSize +
							'px; height: ' +
							arrowSize +
							'px; padding: ' +
							glyphPadding +
							'px; box-sizing: border-box;">' +
							'<svg viewBox="0 0 24 24" aria-hidden="true">' +
							chevronPaths( isArrows, isVertical ) +
							'</svg></span>'
					);
				}
			}
		}

		// Labels.
		const labelStyle = [
			'background-color: ' + ( fieldValue( 'label_bg' ) || '#00000080' ),
			'color: ' + ( fieldValue( 'label_color' ) || '#ffffff' ),
			'font-size: ' + ( fieldValue( 'label_font_size' ) || '12' ) + 'px',
			'font-weight: ' + ( fieldValue( 'label_font_weight' ) || '600' ),
			'padding: ' +
				( fieldValue( 'label_padding_y' ) || '5' ) +
				'px ' +
				( fieldValue( 'label_padding_x' ) || '10' ) +
				'px',
			'border-radius: ' +
				( fieldValue( 'label_border_radius' ) || '4' ) +
				'px',
		].join( '; ' );

		const beforeLabelEl = box.querySelector(
			'.pixvisualize-elementor-preview__label-before'
		);
		const afterLabelEl = box.querySelector(
			'.pixvisualize-elementor-preview__label-after'
		);

		if ( beforeLabelEl ) {
			beforeLabelEl.style.cssText =
				labelStyle +
				'; ' +
				labelPositionCss(
					fieldValue( 'label_position_before' ),
					'top: 12px; left: 12px;'
				);
			setText( beforeLabelEl, fieldValue( 'before_label' ) || 'Before' );
			beforeLabelEl.style.display =
				showLabels && state.before ? '' : 'none';
		}

		if ( afterLabelEl ) {
			afterLabelEl.style.cssText =
				labelStyle +
				'; ' +
				labelPositionCss(
					fieldValue( 'label_position_after' ),
					'top: 12px; right: 12px;'
				);
			setText( afterLabelEl, fieldValue( 'after_label' ) || 'After' );
			afterLabelEl.style.display =
				showLabels && state.after ? '' : 'none';
		}

		// Caption below the slider (free display, Pro styling).
		const captionEl = root.querySelector(
			'.pixvisualize-generator-caption'
		);
		if ( captionEl ) {
			const captionTitle = captionTitleValue;
			const captionText = captionTextValue;

			captionEl.style.display = captionVisible ? '' : 'none';
			captionEl.style.width = '100%';
			captionEl.style.maxWidth = '100%';
			captionEl.style.margin = '0';
			// Border-box so padding/border stay inside the slider width
			// whatever the ratio (otherwise the caption overflows).
			captionEl.style.boxSizing = 'border-box';
			captionEl.style.backgroundColor =
				fieldValue( 'caption_bg' ) || '#ffffff';
			captionEl.style.textAlign =
				fieldValue( 'caption_alignment' ) || 'center';
			// Marges internes séparées : Y = hauteur du caption.
			// Repli legacy caption_padding pour les anciens shortcodes.
			const legacyCaptionPad = fieldValue( 'caption_padding' ) || '16';
			const captionPadX =
				fieldValue( 'caption_padding_x' ) || legacyCaptionPad;
			const captionPadY =
				fieldValue( 'caption_padding_y' ) || legacyCaptionPad;
			captionEl.style.padding = captionPadY + 'px ' + captionPadX + 'px';
			// Card look: with a caption the container bottom corners move
			// to the caption (the slider bottom stays square).
			const captionRadiusBR = radiusBR;
			const captionRadiusBL = radiusBL;
			captionEl.style.borderRadius =
				'0 0 ' + captionRadiusBR + 'px ' + captionRadiusBL + 'px';
			// The card follows the outer corners: top from the container
			// radius, bottom from the caption. Reset when no card shadow
			// so no stale radius lingers after toggling settings.
			if ( card ) {
				card.style.borderRadius = wrapCard
					? radiusTL +
					  'px ' +
					  radiusTR +
					  'px ' +
					  captionRadiusBR +
					  'px ' +
					  captionRadiusBL +
					  'px'
					: '0';
			}
			// Reset then continue the container border around the caption;
			// without a border keep the thin separator line on top.
			captionEl.style.border = 'none';
			if ( effBorderWidth ) {
				captionEl.style.borderLeft = borderValue;
				captionEl.style.borderRight = borderValue;
				captionEl.style.borderBottom = borderValue;
			} else {
				captionEl.style.borderTop = '1px solid #e5e7eb';
			}

			const captionTitleEl = captionEl.querySelector(
				'.pixvisualize-generator-caption-title'
			);
			if ( captionTitleEl ) {
				setText( captionTitleEl, captionTitle );
				captionTitleEl.style.display = captionTitle ? '' : 'none';
				captionTitleEl.style.color =
					fieldValue( 'caption_title_color' ) || '#1e293b';
				captionTitleEl.style.fontSize =
					( fieldValue( 'caption_title_size' ) || '22' ) + 'px';
				captionTitleEl.style.fontWeight = '700';
				captionTitleEl.style.lineHeight = '1.25';
				captionTitleEl.style.margin = captionText ? '0 0 4px' : '0';
			}

			const captionTextEl = captionEl.querySelector(
				'.pixvisualize-generator-caption-text'
			);
			if ( captionTextEl ) {
				setText( captionTextEl, captionText );
				captionTextEl.style.display = captionText ? '' : 'none';
				captionTextEl.style.color =
					fieldValue( 'caption_text_color' ) || '#64748b';
				captionTextEl.style.fontSize =
					( fieldValue( 'caption_text_size' ) || '15' ) + 'px';
				captionTextEl.style.lineHeight = '1.5';
				captionTextEl.style.margin = '0';
			}
		}

		// Empty state.
		box.classList.toggle(
			'pixvisualize-elementor-preview--empty',
			! hasImage
		);
		const empty = box.querySelector(
			'.pixvisualize-generator-preview__empty'
		);
		if ( empty ) {
			empty.style.display = hasImage ? 'none' : '';
		}
	};

	const refresh = () => {
		updateShortcode();
		updatePreview();
	};

	const setMedia = ( target, url, filename ) => {
		state[ target ] = url;

		const thumb = root.querySelector( '[data-preview="' + target + '"]' );
		if ( thumb ) {
			thumb.innerHTML = url
				? '<img src="' + url + '" alt="">'
				: '<span class="pixvisualize-media-placeholder">+</span>';
		}

		const name = root.querySelector( '[data-filename="' + target + '"]' );
		if ( name ) {
			name.textContent =
				filename || wp.i18n.__( 'No image selected', 'pixvisualize' );
		}

		refresh();
	};

	const openMedia = ( target ) => {
		const frame = wp.media( {
			title: wp.i18n.__( 'Select Image', 'pixvisualize' ),
			button: { text: wp.i18n.__( 'Use this image', 'pixvisualize' ) },
			multiple: false,
			library: { type: 'image' },
		} );

		frame.on( 'select', () => {
			const attachment = frame
				.state()
				.get( 'selection' )
				.first()
				.toJSON();

			setMedia(
				target,
				attachment.url,
				attachment.filename || attachment.title
			);
		} );

		frame.open();
	};

	root.querySelectorAll( '.pixvisualize-media-button' ).forEach(
		( button ) => {
			button.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				openMedia( button.getAttribute( 'data-target' ) );
			} );
		}
	);

	enhanceSections();

	// Typing in a text field fires `input` on every keystroke: the shortcode
	// string is cheap to rebuild, but the live preview rebuilds DOM (images,
	// handle SVG, labels, caption) and visibly jumped on each character.
	// Debounce the preview while typing; selects, checkboxes and committed
	// changes (`change`) stay immediate.
	let previewTimer = null;
	const schedulePreview = () => {
		if ( previewTimer !== null ) {
			clearTimeout( previewTimer );
		}
		previewTimer = setTimeout( () => {
			previewTimer = null;
			updatePreview();
		}, 150 );
	};
	const refreshSoon = () => {
		updateShortcode();
		schedulePreview();
	};
	const refreshNow = () => {
		if ( previewTimer !== null ) {
			clearTimeout( previewTimer );
			previewTimer = null;
		}
		refresh();
	};

	root.querySelectorAll( '[data-field]' ).forEach( ( field ) => {
		field.addEventListener( 'change', refreshNow );
		if ( field.tagName === 'SELECT' || field.type === 'checkbox' ) {
			field.addEventListener( 'input', refreshNow );
		} else {
			// text, textarea, number: live shortcode, deferred preview.
			field.addEventListener( 'input', refreshSoon );
		}
	} );

	const clearMedia = () => {
		setMedia( 'before', '', '' );
		setMedia( 'after', '', '' );
	};

	const resetAll = () => {
		Object.keys( DEFAULTS ).forEach( ( name ) => {
			const el = root.querySelector( '[data-field="' + name + '"]' );
			if ( ! el ) {
				return;
			}
			if ( el.type === 'checkbox' ) {
				el.checked = DEFAULTS[ name ] === '1';
			} else {
				el.value = DEFAULTS[ name ];
			}
		} );
		clearMedia();
	};

	const clearButton = root.querySelector( '.pixvisualize-clear-button' );
	if ( clearButton ) {
		clearButton.addEventListener( 'click', () => {
			clearMedia();
		} );
	}

	// Reload a previously copied shortcode into the generator so it can be
	// edited visually instead of rebuilt from scratch.
	const decodeAttrValue = ( value ) => {
		const area = document.createElement( 'textarea' );
		area.innerHTML = String( value );
		return area.value;
	};

	const basenameFromUrl = ( url ) => {
		try {
			const clean = String( url ).split( '?' )[ 0 ].split( '#' )[ 0 ];
			const base = clean.substring( clean.lastIndexOf( '/' ) + 1 );
			return decodeURIComponent( base ) || clean;
		} catch {
			return String( url );
		}
	};

	const setField = ( name, value ) => {
		const el = root.querySelector( '[data-field="' + name + '"]' );
		if ( ! el ) {
			return;
		}
		if ( el.type === 'checkbox' ) {
			el.checked = value === '1';
		} else if ( el.tagName === 'SELECT' ) {
			const hasOption = Array.from( el.options ).some(
				( option ) => option.value === value
			);
			el.value = hasOption ? value : DEFAULTS[ name ];
		} else {
			el.value = value;
		}
		try {
			if (
				window.jQuery &&
				el.classList.contains( 'pixvisualize-generator-colorpicker' ) &&
				jQuery( el ).wpColorPicker
			) {
				jQuery( el ).wpColorPicker( 'color', el.value );
			}
		} catch {
			// The input value is already set: only the picker swatch may
			// stay stale, which never blocks the import itself.
		}
	};

	const showImportMessage = ( ok, text ) => {
		const messageEl = root.querySelector( '.pixvisualize-import-message' );
		if ( ! messageEl ) {
			return;
		}
		messageEl.textContent = text;
		messageEl.style.color = ok ? '#15803d' : '#b91c1c';
	};

	const importShortcode = () => {
		const input = root.querySelector( '.pixvisualize-import-field' );
		const raw = input ? input.value : '';
		const match = raw.match( /\[pixvisualize\s+([^\]]*)\]/i );
		if ( ! match ) {
			showImportMessage(
				false,
				wp.i18n.__(
					'No valid [pixvisualize …] shortcode found.',
					'pixvisualize'
				)
			);
			return;
		}
		const attrs = {};
		const attrRe = /(\w+)\s*=\s*(?:"([^"]*)"|'([^']*)')/g;
		let attrMatch;
		while ( ( attrMatch = attrRe.exec( match[ 1 ] ) ) !== null ) {
			const attrValue =
				attrMatch[ 2 ] !== undefined ? attrMatch[ 2 ] : attrMatch[ 3 ];
			attrs[ attrMatch[ 1 ] ] = decodeAttrValue( attrValue );
		}
		if ( ! attrs.before || ! attrs.after ) {
			showImportMessage(
				false,
				wp.i18n.__(
					'The shortcode needs before="…" and after="…" images.',
					'pixvisualize'
				)
			);
			return;
		}
		// Start from defaults so omitted attributes never keep stale values.
		Object.keys( DEFAULTS ).forEach( ( name ) => {
			setField( name, DEFAULTS[ name ] );
		} );
		// Migration legacy : caption_padding uniforme -> X + Y.
		if (
			attrs.caption_padding !== undefined &&
			attrs.caption_padding_x === undefined &&
			attrs.caption_padding_y === undefined
		) {
			attrs.caption_padding_x = attrs.caption_padding;
			attrs.caption_padding_y = attrs.caption_padding;
		}
		Object.keys( attrs ).forEach( ( name ) => {
			if ( name === 'before' || name === 'after' ) {
				return;
			}
			if ( name in DEFAULTS ) {
				setField( name, attrs[ name ] );
			}
		} );
		setMedia( 'before', attrs.before, basenameFromUrl( attrs.before ) );
		setMedia( 'after', attrs.after, basenameFromUrl( attrs.after ) );
		refresh();
		showImportMessage(
			true,
			wp.i18n.__( 'Shortcode loaded into the generator.', 'pixvisualize' )
		);
	};

	const importButton = root.querySelector( '.pixvisualize-import-button' );
	if ( importButton ) {
		importButton.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			importShortcode();
		} );
	}

	const resetButton = root.querySelector( '.pixvisualize-reset-button' );
	if ( resetButton ) {
		resetButton.addEventListener( 'click', () => {
			if (
				// eslint-disable-next-line no-alert
				window.confirm(
					wp.i18n.__(
						'Reset the generator to its default values?',
						'pixvisualize'
					)
				)
			) {
				resetAll();
			}
		} );
	}

	// Color pickers with alpha support (same as the WooCommerce comparator).
	if ( window.jQuery && jQuery.fn.wpColorPicker ) {
		jQuery( '.pixvisualize-generator-colorpicker' ).each( function () {
			jQuery( this ).wpColorPicker( {
				alpha: true,
				// Iris notifie `change` AVANT d'écrire la valeur dans
				// l'input lors d'un clic sur une pastille (carrés) :
				// une lecture immédiate voit l'ANCIENNE couleur
				// (aperçu en retard d'un pick). On diffère au prochain
				// tick pour lire la valeur à jour. Idempotent
				// (guarded DOM writes dans updatePreview).
				change: () => {
					setTimeout( refresh, 0 );
				},
			} );
		} );
	}

	// Sub-tabs ("generator" / "list") + saved shortcode list persisted in
	// localStorage: the list keeps every generated shortcode with edit
	// (reloads the generator) and delete actions.
	const SAVED_KEY = 'pixvisualize_shortcodes_v1';

	const loadSaved = () => {
		try {
			const raw = window.localStorage.getItem( SAVED_KEY );
			const parsed = raw ? JSON.parse( raw ) : [];
			return Array.isArray( parsed ) ? parsed : [];
		} catch {
			return [];
		}
	};

	const storeSaved = ( items ) => {
		try {
			window.localStorage.setItem( SAVED_KEY, JSON.stringify( items ) );
		} catch {
			// Storage unavailable (private mode…): the list simply
			// does not persist, the generator keeps working.
		}
	};

	const switchSubtab = ( name ) => {
		root.querySelectorAll( '.pixvisualize-subnav-button' ).forEach(
			( button ) => {
				const active = button.getAttribute( 'data-subtab' ) === name;
				button.classList.toggle( 'is-active', active );
				button.setAttribute(
					'aria-selected',
					active ? 'true' : 'false'
				);
			}
		);
		root.querySelectorAll( '.pixvisualize-gen-pane' ).forEach( ( pane ) => {
			pane.hidden = pane.getAttribute( 'data-pane' ) !== name;
		} );
	};

	root.querySelectorAll( '.pixvisualize-subnav-button' ).forEach(
		( button ) => {
			button.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				switchSubtab( button.getAttribute( 'data-subtab' ) );
			} );
		}
	);

	const copyText = ( text, done ) => {
		if ( navigator.clipboard && window.isSecureContext ) {
			navigator.clipboard.writeText( text ).then( done, done );
		} else {
			const area = document.createElement( 'textarea' );
			area.value = text;
			area.style.position = 'fixed';
			area.style.opacity = '0';
			document.body.appendChild( area );
			area.focus();
			area.select();
			try {
				document.execCommand( 'copy' );
			} catch {
				// Copy unavailable: the code stays selectable in its field.
			}
			document.body.removeChild( area );
			done();
		}
	};

	const renderSavedList = () => {
		const list = root.querySelector( '.pixvisualize-saved-list' );
		if ( ! list ) {
			return;
		}
		const empty = root.querySelector( '.pixvisualize-saved-empty' );
		const count = root.querySelector( '.pixvisualize-subnav-count' );
		const items = loadSaved();
		list.innerHTML = '';
		if ( empty ) {
			empty.style.display = items.length ? 'none' : '';
		}
		if ( count ) {
			count.textContent = String( items.length );
		}
		const editLabel = wp.i18n.__( 'Edit', 'pixvisualize' );
		const copyLabel = wp.i18n.__( 'Copy Shortcode', 'pixvisualize' );
		const deleteLabel = wp.i18n.__( 'Delete', 'pixvisualize' );
		items.forEach( ( item, index ) => {
			const row = document.createElement( 'div' );
			row.className = 'pixvisualize-saved-item';

			if ( item.before ) {
				const thumb = document.createElement( 'img' );
				thumb.className = 'pixvisualize-saved-thumb';
				thumb.src = item.before;
				thumb.alt = '';
				row.appendChild( thumb );
			}

			const meta = document.createElement( 'div' );
			meta.className = 'pixvisualize-saved-meta';

			const head = document.createElement( 'div' );
			head.className = 'pixvisualize-saved-head';

			// Inline rename: the name persists to localStorage on change so
			// entries stay easy to find.
			const fallbackName =
				wp.i18n.__( 'Shortcode', 'pixvisualize' ) + ' ' + ( index + 1 );
			const name = document.createElement( 'input' );
			name.type = 'text';
			name.className = 'pixvisualize-saved-name';
			name.value = item.name || fallbackName;
			name.setAttribute(
				'aria-label',
				wp.i18n.__( 'Rename this shortcode', 'pixvisualize' )
			);
			name.addEventListener( 'change', () => {
				const next = loadSaved();
				const entry = next.find(
					( candidate ) => candidate.id === item.id
				);
				if ( entry ) {
					entry.name = name.value.trim() || fallbackName;
					name.value = entry.name;
					storeSaved( next );
				}
			} );
			head.appendChild( name );

			if ( item.createdAt ) {
				const date = document.createElement( 'span' );
				date.className = 'pixvisualize-saved-date';
				try {
					date.textContent = new Date(
						item.createdAt
					).toLocaleString();
				} catch {
					date.textContent = '';
				}
				head.appendChild( date );
			}
			meta.appendChild( head );

			const code = document.createElement( 'input' );
			code.type = 'text';
			code.className = 'pixvisualize-saved-code';
			code.readOnly = true;
			code.value = item.code;
			code.setAttribute( 'aria-label', name.value );
			meta.appendChild( code );
			row.appendChild( meta );

			const actions = document.createElement( 'div' );
			actions.className = 'pixvisualize-saved-actions';

			const editButton = document.createElement( 'button' );
			editButton.type = 'button';
			editButton.className =
				'button button-small pixvisualize-icon-button';
			editButton.setAttribute( 'data-tip', editLabel );
			editButton.setAttribute( 'aria-label', editLabel );
			editButton.innerHTML =
				'<span class="dashicons dashicons-edit" aria-hidden="true"></span>';
			editButton.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				const importField = root.querySelector(
					'.pixvisualize-import-field'
				);
				if ( importField ) {
					importField.value = item.code;
				}
				switchSubtab( 'generator' );
				importShortcode();
			} );
			actions.appendChild( editButton );

			const itemCopyButton = document.createElement( 'button' );
			itemCopyButton.type = 'button';
			itemCopyButton.className =
				'button button-small pixvisualize-icon-button';
			itemCopyButton.setAttribute( 'data-tip', copyLabel );
			itemCopyButton.setAttribute( 'aria-label', copyLabel );
			itemCopyButton.innerHTML =
				'<span class="dashicons dashicons-clipboard" aria-hidden="true"></span>';
			itemCopyButton.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				const icon = itemCopyButton.querySelector( '.dashicons' );
				copyText( item.code, () => {
					if ( icon ) {
						icon.classList.remove( 'dashicons-clipboard' );
						icon.classList.add( 'dashicons-yes' );
					}
					window.setTimeout( () => {
						if ( icon ) {
							icon.classList.remove( 'dashicons-yes' );
							icon.classList.add( 'dashicons-clipboard' );
						}
					}, 1500 );
				} );
			} );
			actions.appendChild( itemCopyButton );

			const deleteButton = document.createElement( 'button' );
			deleteButton.type = 'button';
			deleteButton.className =
				'button button-small pixvisualize-icon-button';
			deleteButton.setAttribute( 'data-tip', deleteLabel );
			deleteButton.setAttribute( 'aria-label', deleteLabel );
			deleteButton.innerHTML =
				'<span class="dashicons dashicons-trash" aria-hidden="true"></span>';
			deleteButton.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				if (
					// eslint-disable-next-line no-alert
					! window.confirm(
						wp.i18n.__(
							'Delete this shortcode from the list?',
							'pixvisualize'
						)
					)
				) {
					return;
				}
				const next = loadSaved().filter(
					( entry ) => entry.id !== item.id
				);
				storeSaved( next );
				renderSavedList();
			} );
			actions.appendChild( deleteButton );

			row.appendChild( actions );
			list.appendChild( row );
		} );
	};

	const saveCurrent = () => {
		const field = root.querySelector( '.pixvisualize-shortcode-field' );
		const code = field ? field.value.trim() : '';
		if ( ! code || ! state.before || ! state.after ) {
			return;
		}
		const items = loadSaved();
		if ( items.some( ( entry ) => entry.code === code ) ) {
			switchSubtab( 'list' );
			return;
		}
		// Default name ("Shortcode N"): counter kept increasing so a deleted
		// entry never makes a new one reuse its name.
		let nextNumber = items.length + 1;
		const taken = new Set(
			items.map( ( entry ) => entry.name ).filter( Boolean )
		);
		while (
			taken.has(
				wp.i18n.__( 'Shortcode', 'pixvisualize' ) + ' ' + nextNumber
			)
		) {
			nextNumber += 1;
		}
		items.unshift( {
			id:
				Date.now().toString( 36 ) +
				Math.random().toString( 36 ).slice( 2, 8 ),
			name: wp.i18n.__( 'Shortcode', 'pixvisualize' ) + ' ' + nextNumber,
			code,
			before: state.before,
			after: state.after,
			createdAt: Date.now(),
		} );
		storeSaved( items );
		renderSavedList();
		switchSubtab( 'list' );
	};

	const saveButtonTop = root.querySelector( '.pixvisualize-save-button' );
	if ( saveButtonTop ) {
		saveButtonTop.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			saveCurrent();
		} );
	}

	renderSavedList();

	root.querySelectorAll( '.pixvisualize-generator-colorpicker' ).forEach(
		( input ) => {
			input.addEventListener( 'change', () => {
				input.value = toHexAlpha( input.value );
				refresh();
			} );
		}
	);

	refresh();

	const copyButton = root.querySelector( '.pixvisualize-copy-button' );
	if ( copyButton ) {
		copyButton.addEventListener( 'click', () => {
			const field = root.querySelector( '.pixvisualize-shortcode-field' );
			if ( ! field ) {
				return;
			}

			field.focus();
			field.select();
			field.setSelectionRange( 0, 99999 );

			const flashCopy = () => {
				const icon = copyButton.querySelector( '.dashicons' );
				const copiedLabel = wp.i18n.__( 'Copied!', 'pixvisualize' );
				const originalTip = copyButton.getAttribute( 'data-tip' );

				if ( icon ) {
					icon.classList.remove( 'dashicons-clipboard' );
					icon.classList.add( 'dashicons-yes' );
				}
				copyButton.setAttribute( 'aria-label', copiedLabel );
				copyButton.setAttribute( 'data-tip', copiedLabel );

				window.setTimeout( () => {
					if ( icon ) {
						icon.classList.remove( 'dashicons-yes' );
						icon.classList.add( 'dashicons-clipboard' );
					}
					if ( originalTip ) {
						copyButton.setAttribute( 'aria-label', originalTip );
						copyButton.setAttribute( 'data-tip', originalTip );
					}
				}, 1500 );
			};

			if ( navigator.clipboard && window.isSecureContext ) {
				navigator.clipboard.writeText( field.value ).then( flashCopy );
			} else {
				try {
					document.execCommand( 'copy' );
					flashCopy();
				} catch {
					flashCopy();
				}
			}
		} );
	}
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', bootPixVisualizeGenerator );
} else {
	bootPixVisualizeGenerator();
}
