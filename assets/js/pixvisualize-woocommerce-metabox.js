// PixVisualize — WooCommerce product comparator admin script.
// 1. The image pickers live in two places (Product Data tab + sidebar metabox)
//    and share the same classes/data-* hooks. Any selection or removal updates
//    every instance of that image slot in real time, and both write the same
//    hidden inputs (pixvisualize_woo_before_id / pixvisualize_woo_after_id).
// 2. A live CSS preview (mirroring the Elementor editor preview) in the
//    Product Data panel is kept in sync with every field change.
/* global jQuery */
const bootPixVisualizeWoo = function () {
	if ( typeof wp === 'undefined' || typeof wp.media === 'undefined' ) {
		// Media scripts not ready yet: retry once on window load instead
		// of waiting for every page asset (faster live preview setup).
		if ( document.readyState !== 'complete' ) {
			window.addEventListener( 'load', bootPixVisualizeWoo, {
				once: true,
			} );
		}
		return;
	}

	// Color pickers (Separator Color, Label Background, Text Color) with
	// alpha support for the 8-digit hex values used by the comparator.
	if ( window.jQuery && jQuery.fn.wpColorPicker ) {
		jQuery( '.pixvisualize-woo-colorpicker' ).each( function () {
			jQuery( this ).wpColorPicker( {
				alpha: true,
			} );
		} );
	}

	// Normalize rgba() (output by the alpha picker) back to #RRGGBBAA so the
	// value matches the hex-only format expected on save.
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

	document
		.querySelectorAll( '.pixvisualize-woo-colorpicker' )
		.forEach( ( input ) => {
			input.addEventListener( 'change', () => {
				input.value = toHexAlpha( input.value );
			} );
		} );

	// ---------------------------------------------------------------------
	// Helpers
	// ---------------------------------------------------------------------

	const panel = () => document.getElementById( 'pixvisualizeduct-data' );
	const livePreview = () =>
		document.querySelector( '.pixvisualize-woo-live-preview' );

	const readVal = ( id, fallback ) => {
		const el = document.getElementById( id );
		return el ? el.value : fallback;
	};

	const readCheck = ( id ) => {
		const el = document.getElementById( id );
		return el ? el.checked : false;
	};

	const clamp = ( value, min, max ) =>
		Math.min( max, Math.max( min, value ) );

	// Write a data-* attribute only when it changes: untouched attributes
	// don't wake the shared preview MutationObserver on every keystroke.
	const setData = ( el, name, value ) => {
		if ( el.dataset[ name ] !== value ) {
			el.dataset[ name ] = value;
		}
	};

	const attachmentUrl = ( attachment ) => {
		if ( ! attachment ) {
			return '';
		}

		return attachment.sizes && attachment.sizes.medium
			? attachment.sizes.medium.url
			: attachment.url;
	};

	// ---------------------------------------------------------------------
	// Image pickers (shared by Product Data tab + sidebar metabox)
	// ---------------------------------------------------------------------

	const setMedia = ( target, attachment ) => {
		const url = attachmentUrl( attachment );

		document
			.querySelectorAll(
				'.pixvisualize-woo-id[data-pixvisualize="' + target + '"]'
			)
			.forEach( ( input ) => {
				input.value = attachment ? attachment.id : '';
			} );

		document
			.querySelectorAll(
				'.pixvisualize-woo-preview[data-preview="' + target + '"]'
			)
			.forEach( ( preview ) => {
				preview.innerHTML = url ? '<img src="' + url + '" alt="">' : '';
			} );

		document
			.querySelectorAll(
				'.pixvisualize-woo-remove[data-remove="' + target + '"]'
			)
			.forEach( ( button ) => {
				button.disabled = ! attachment;
			} );

		syncImages();
		syncLivePreview();
	};

	document
		.querySelectorAll( '.pixvisualize-woo-select' )
		.forEach( ( button ) => {
			button.addEventListener( 'click', ( event ) => {
				event.preventDefault();

				const target = button.getAttribute( 'data-target' );

				const frame = wp.media( {
					title: wp.i18n.__( 'Select Image', 'pixvisualize' ),
					button: {
						text: wp.i18n.__( 'Use this image', 'pixvisualize' ),
					},
					multiple: false,
					library: { type: 'image' },
				} );

				frame.on( 'select', () => {
					const attachment = frame
						.state()
						.get( 'selection' )
						.first()
						.toJSON();
					setMedia( target, attachment );
				} );

				frame.open();
			} );
		} );

	document
		.querySelectorAll( '.pixvisualize-woo-remove' )
		.forEach( ( button ) => {
			button.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				setMedia( button.getAttribute( 'data-remove' ), null );
			} );
		} );

	// Keep the previews in sync when the hidden inputs are changed by any
	// other source (and reflect it across both locations).
	document.querySelectorAll( '.pixvisualize-woo-id' ).forEach( ( input ) => {
		input.addEventListener( 'change', () => {
			const target = input.getAttribute( 'data-pixvisualize' );
			const id = parseInt( input.value, 10 );

			if ( id ) {
				wp.media
					.attachment( id )
					.fetch()
					.then( ( attachment ) => {
						setMedia( target, attachment );
					} );
			} else {
				setMedia( target, null );
			}
		} );
	} );

	// ---------------------------------------------------------------------
	// Live CSS preview
	// ---------------------------------------------------------------------

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

	const syncImages = () => {
		const box = livePreview();

		if ( ! box ) {
			return;
		}

		[ 'before', 'after' ].forEach( ( slot ) => {
			const input = document.querySelector(
				'.pixvisualize-woo-id[data-pixvisualize="' + slot + '"]'
			);
			const img = box.querySelector(
				'.pixvisualize-elementor-preview__' + slot
			);

			if ( ! input || ! img ) {
				return;
			}

			const id = parseInt( input.value, 10 );

			if ( ! id ) {
				img.removeAttribute( 'src' );
				img.dataset.attachmentId = '';
				img.style.display = 'none';
				return;
			}

			// Server-rendered preview already holds the right image.
			if ( ! img.dataset.attachmentId && img.getAttribute( 'src' ) ) {
				img.dataset.attachmentId = id;
				img.style.display = '';
				return;
			}

			if ( img.dataset.attachmentId === String( id ) ) {
				img.style.display = '';
				return;
			}

			img.style.display = '';
			wp.media
				.attachment( id )
				.fetch()
				.then( ( attachment ) => {
					if ( parseInt( input.value, 10 ) !== id ) {
						return;
					}

					const src =
						attachment.sizes && attachment.sizes.large
							? attachment.sizes.large.url
							: attachment.url;

					img.dataset.attachmentId = id;
					img.src = src || '';
					img.style.display = src ? '' : 'none';
					updateEmptyState();
				} );
		} );
	};

	const updateEmptyState = () => {
		const box = livePreview();

		if ( ! box ) {
			return;
		}

		const before = box.querySelector(
			'.pixvisualize-elementor-preview__before'
		);
		const after = box.querySelector(
			'.pixvisualize-elementor-preview__after'
		);
		const hasImage =
			( before && before.getAttribute( 'src' ) ) ||
			( after && after.getAttribute( 'src' ) );
		const handle = box.querySelector(
			'.pixvisualize-elementor-preview__handle'
		);
		const empty = box.querySelector( '.pixvisualize-woo-live-empty' );

		box.classList.toggle(
			'pixvisualize-elementor-preview--empty',
			! hasImage
		);

		if ( handle ) {
			handle.style.display = hasImage ? '' : 'none';
		}

		if ( empty ) {
			empty.style.display = hasImage ? 'none' : '';
		}
	};

	const syncLivePreview = () => {
		const box = livePreview();

		if ( ! box ) {
			return;
		}

		const orientation = readVal(
			'pixvisualize_woo_orientation',
			'horizontal'
		);
		const ratio = readVal( 'pixvisualize_woo_ratio', '16/9' );
		const height =
			parseInt( readVal( 'pixvisualize_woo_height', '450' ), 10 ) || 450;
		const position = parseInt(
			readVal( 'pixvisualize_woo_position', '50' ),
			10
		);
		const handle = readVal( 'pixvisualize_woo_handle', 'circle' );
		const handleSvg = readVal( 'pixvisualize_woo_handle_svg', '' );
		const handleSize = clamp(
			parseInt( readVal( 'pixvisualize_woo_handle_size', '24' ), 10 ) ||
				24,
			16,
			64
		);
		const handleInner = Math.max( 12, Math.round( handleSize * 0.65 ) );
		const showLabels = readCheck( 'pixvisualize_woo_show_labels' );
		const sepColor = readVal(
			'pixvisualize_woo_separator_color',
			'#ffffff'
		);
		// Caption drives the card look (same rule as the front-end
		// slider): with a caption the container border/shadow wrap the
		// whole card, without it they stay on the slider alone.
		const captionTitleValue = readVal(
			'pixvisualize_woo_caption_title',
			''
		).trim();
		const captionTextValue = readVal(
			'pixvisualize_woo_caption_text',
			''
		).trim();
		const captionVisible =
			readCheck( 'pixvisualize_woo_show_caption' ) &&
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
		const card = document.querySelector( '.pixvisualize-woo-live-card' );
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
		// height on the empty state so the placeholder keeps its size.
		const beforeImgCheck = box.querySelector(
			'.pixvisualize-elementor-preview__before'
		);
		const afterImgCheck = box.querySelector(
			'.pixvisualize-elementor-preview__after'
		);
		const hasPreviewImage =
			( beforeImgCheck && beforeImgCheck.getAttribute( 'src' ) ) ||
			( afterImgCheck && afterImgCheck.getAttribute( 'src' ) );
		box.style.height = hasPreviewImage
			? clamp( height, 100, 2000 ) + 'px'
			: '';
		box.style.setProperty( '--pc-pos', position + '%' );
		setData( box, 'orientation', orientation );
		// Keep the shared contain script (pixvisualize-elementor-preview.js)
		// in sync: without these it falls back to 16/9 x 450 and crushes
		// tall ratios like 9/21 right after load.
		setData( box, 'aspectRatio', ratio );
		setData( box, 'height', String( height ) );
		box.style.cursor = isVertical ? 'ns-resize' : 'ew-resize';

		// Container styling (shadow, border, border-radius).
		const containerShadow = readCheck(
			'pixvisualize_woo_container_shadow'
		);
		const shadowColor = readVal(
			'pixvisualize_woo_container_shadow_color',
			'#000000'
		);
		const shadowOpacity = clamp(
			parseInt(
				readVal( 'pixvisualize_woo_container_shadow_opacity', '35' ),
				10
			) || 35,
			0,
			100
		);
		const shadowAlpha = shadowOpacity / 100;
		const shadowHex = shadowColor
			.trim()
			.match( /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/ );
		let shadowRgb = 'rgba(0, 0, 0, ' + shadowAlpha + ')';
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
				'rgba(' + hr + ', ' + hg + ', ' + hb + ', ' + shadowAlpha + ')';
		}
		const shadowH =
			parseInt(
				readVal( 'pixvisualize_woo_container_shadow_horizontal', '0' ),
				10
			) || 0;
		const shadowV =
			parseInt(
				readVal( 'pixvisualize_woo_container_shadow_vertical', '8' ),
				10
			) || 0;
		const shadowBlur =
			parseInt(
				readVal( 'pixvisualize_woo_container_shadow_blur', '20' ),
				10
			) || 0;
		const shadowSpread =
			parseInt(
				readVal( 'pixvisualize_woo_container_shadow_spread', '0' ),
				10
			) || 0;
		const shadowValue = containerShadow
			? shadowH +
			  'px ' +
			  shadowV +
			  'px ' +
			  shadowBlur +
			  'px ' +
			  shadowSpread +
			  'px ' +
			  shadowRgb
			: 'none';
		// Card look (shadow): with a caption the shadow wraps the whole
		// card, otherwise it stays on the slider alone.
		const wrapCard = captionVisible && containerShadow;
		if ( card ) {
			card.style.boxShadow = wrapCard ? shadowValue : 'none';
		}
		box.style.boxShadow = wrapCard ? 'none' : shadowValue;

		const borderWidth =
			parseInt(
				readVal( 'pixvisualize_woo_container_border_width', '0' ),
				10
			) || 0;
		// FREE toggles (checkboxes): default 2px solid gray border, default
		// 16px radius. PRO customization wins when set.
		const showBorder = readCheck( 'pixvisualize_woo_container_border' );
		const roundedCorners = readCheck(
			'pixvisualize_woo_container_rounded'
		);
		let effBorderWidth = 0;
		let effBorderColor = '#E5E7EB';
		let effBorderStyle = 'solid';
		if ( borderWidth > 0 ) {
			effBorderWidth = borderWidth;
			effBorderColor = readVal(
				'pixvisualize_woo_container_border_color',
				'#000000'
			);
			effBorderStyle = readVal(
				'pixvisualize_woo_container_border_style',
				'solid'
			);
		} else if ( showBorder ) {
			effBorderWidth = 2;
		}
		const borderValue = effBorderWidth
			? effBorderWidth + 'px ' + effBorderStyle + ' ' + effBorderColor
			: 'none';
		const resolveRadius = ( id ) => {
			const raw = parseInt( readVal( id, '0' ), 10 ) || 0;
			if ( raw > 0 ) {
				return raw;
			}
			return roundedCorners ? 16 : 0;
		};
		const radiusTL = resolveRadius(
			'pixvisualize_woo_container_border_radius_tl'
		);
		const radiusTR = resolveRadius(
			'pixvisualize_woo_container_border_radius_tr'
		);
		const radiusBR = resolveRadius(
			'pixvisualize_woo_container_border_radius_br'
		);
		const radiusBL = resolveRadius(
			'pixvisualize_woo_container_border_radius_bl'
		);
		// Reset the shorthand first so stale per-side borders from a
		// previous sync never linger when the caption toggles.
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
		setData(
			box,
			'hoverMode',
			readCheck( 'pixvisualize_woo_hover_mode' ) ? '1' : '0'
		);
		setData(
			box,
			'autoScan',
			readCheck( 'pixvisualize_woo_auto_scan' ) ? '1' : '0'
		);
		setData(
			box,
			'autoScanDuration',
			readVal( 'pixvisualize_woo_auto_scan_duration', '2500' )
		);
		setData(
			box,
			'magnifier',
			readCheck( 'pixvisualize_woo_magnifier' ) ? '1' : '0'
		);
		setData(
			box,
			'magnifierZoom',
			readVal( 'pixvisualize_woo_magnifier_zoom', '2' )
		);

		// Handle.
		const sepShadow = readCheck( 'pixvisualize_woo_separator_shadow' );
		const handleEl = box.querySelector(
			'.pixvisualize-elementor-preview__handle'
		);

		if ( handleEl ) {
			handleEl.className =
				'pixvisualize-elementor-preview__handle pixvisualize-elementor-preview__handle--' +
				( isVertical ? 'vertical' : 'horizontal' );
			// Grouped shadow on the parent (single element look, no seam).
			handleEl.style.filter = sepShadow
				? 'drop-shadow(0 0 6px rgba(0, 0, 0, 0.6))'
				: 'none';
		}

		const line = box.querySelector(
			'.pixvisualize-elementor-preview__handle-line'
		);

		if ( line ) {
			const thickness = readVal(
				'pixvisualize_woo_separator_thickness',
				'2'
			);

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
				const handleBg = (
					readVal( 'pixvisualize_woo_handle_bg', '' ) || ''
				).trim();
				const discBg =
					handleBg || ( isTransparent ? 'transparent' : sepColor );
				// Inner spacing between the glyph and the disc edge
				// (custom SVG was glued to the edge). Zero keeps history.
				const glyphPadding = clamp(
					parseInt(
						readVal( 'pixvisualize_woo_handle_padding', '0' ),
						10
					) || 0,
					0,
					20
				);
				const arrowColor = (
					readVal(
						'pixvisualize_woo_handle_arrow_color',
						'#666666'
					) || '#666666'
				).trim();
				icon.style.background = discBg;
				icon.style.color = arrowColor;
				// Local scope for fill="var(--svg-fill, currentColor)".
				icon.style.setProperty( '--svg-fill', arrowColor );
				// No box-shadow on children: grouped filter on the parent.
				icon.style.boxShadow = 'none';
				if ( handle === 'custom' && handleSvg ) {
					icon.innerHTML = handleSvg;
					icon.style.padding = glyphPadding + 'px';
				} else {
					icon.style.padding = '0';
					const isArrows = handle === 'arrows';
					const arrowSize = isArrows ? handleSize + 10 : handleInner;
					icon.innerHTML =
						'<span class="pixvisualize-elementor-preview__arrows" style="width: ' +
						arrowSize +
						'px; height: ' +
						arrowSize +
						'px; padding: ' +
						glyphPadding +
						'px; box-sizing: border-box;">' +
						'<svg viewBox="0 0 24 24" aria-hidden="true">' +
						chevronPaths( isArrows, isVertical ) +
						'</svg></span>';
				}
			}
		}

		// Labels.
		const labelStyle = [
			'background-color: ' +
				readVal( 'pixvisualize_woo_label_background', '#00000080' ),
			'color: ' + readVal( 'pixvisualize_woo_label_color', '#ffffff' ),
			'font-size: ' +
				readVal( 'pixvisualize_woo_label_font_size', '12' ) +
				'px',
			'font-weight: ' +
				readVal( 'pixvisualize_woo_label_font_weight', '600' ),
			'padding: ' +
				readVal( 'pixvisualize_woo_label_padding_y', '5' ) +
				'px ' +
				readVal( 'pixvisualize_woo_label_padding_x', '10' ) +
				'px',
			'border-radius: ' +
				readVal( 'pixvisualize_woo_label_border_radius', '4' ) +
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
					readVal(
						'pixvisualize_woo_label_position_before',
						'top-left'
					),
					'top: 12px; left: 12px;'
				);
			beforeLabelEl.textContent = readVal(
				'pixvisualize_woo_before_label',
				'Before'
			);
			beforeLabelEl.style.display = showLabels ? '' : 'none';
		}

		if ( afterLabelEl ) {
			afterLabelEl.style.cssText =
				labelStyle +
				'; ' +
				labelPositionCss(
					readVal(
						'pixvisualize_woo_label_position_after',
						'top-right'
					),
					'top: 12px; right: 12px;'
				);
			afterLabelEl.textContent = readVal(
				'pixvisualize_woo_after_label',
				'After'
			);
			afterLabelEl.style.display = showLabels ? '' : 'none';
		}

		// Caption below the slider (same card rule as the front-end).
		const captionEl = document.querySelector(
			'.pixvisualize-woo-live-caption'
		);
		if ( captionEl ) {
			captionEl.style.display = captionVisible ? '' : 'none';
			captionEl.style.width = '100%';
			captionEl.style.maxWidth = '100%';
			captionEl.style.margin = '0';
			// Border-box so padding/border stay inside the slider width
			// whatever the ratio (otherwise the caption overflows).
			captionEl.style.boxSizing = 'border-box';
			captionEl.style.backgroundColor = readVal(
				'pixvisualize_woo_caption_bg',
				'#ffffff'
			);
			captionEl.style.textAlign = readVal(
				'pixvisualize_woo_caption_alignment',
				'center'
			);
			// Marges internes séparées : Y = hauteur du caption.
			// Repli legacy pour les produits enregistrés avant la séparation.
			const legacyCaptionPad = readVal(
				'pixvisualize_woo_caption_padding',
				'16'
			);
			const captionPadX = readVal(
				'pixvisualize_woo_caption_padding_x',
				legacyCaptionPad
			);
			const captionPadY = readVal(
				'pixvisualize_woo_caption_padding_y',
				legacyCaptionPad
			);
			captionEl.style.padding = captionPadY + 'px ' + captionPadX + 'px';
			// Card look: with a caption the container bottom corners move
			// to the caption (the slider bottom stays square).
			const captionRadiusBR = radiusBR;
			const captionRadiusBL = radiusBL;
			captionEl.style.borderRadius =
				'0 0 ' + captionRadiusBR + 'px ' + captionRadiusBL + 'px';
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
			// The card follows the outer corners: top from the container
			// radius, bottom from the caption.
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

			const captionTitleEl = captionEl.querySelector(
				'.pixvisualize-woo-live-caption-title'
			);
			if ( captionTitleEl ) {
				captionTitleEl.textContent = captionTitleValue;
				captionTitleEl.style.display = captionTitleValue ? '' : 'none';
				captionTitleEl.style.color = readVal(
					'pixvisualize_woo_caption_title_color',
					'#1e293b'
				);
				captionTitleEl.style.fontSize =
					readVal( 'pixvisualize_woo_caption_title_size', '22' ) +
					'px';
				captionTitleEl.style.fontWeight = '700';
				captionTitleEl.style.lineHeight = '1.25';
				captionTitleEl.style.margin = captionTextValue
					? '0 0 4px'
					: '0';
			}

			const captionTextEl = captionEl.querySelector(
				'.pixvisualize-woo-live-caption-text'
			);
			if ( captionTextEl ) {
				captionTextEl.textContent = captionTextValue;
				captionTextEl.style.display = captionTextValue ? '' : 'none';
				captionTextEl.style.color = readVal(
					'pixvisualize_woo_caption_text_color',
					'#64748b'
				);
				captionTextEl.style.fontSize =
					readVal( 'pixvisualize_woo_caption_text_size', '15' ) +
					'px';
				captionTextEl.style.lineHeight = '1.5';
				captionTextEl.style.margin = '0';
			}
		}

		updateEmptyState();
	};

	const toggleLabelFields = () => {
		const box = panel();

		if ( ! box ) {
			return;
		}

		const show = readCheck( 'pixvisualize_woo_show_labels' );

		box.querySelectorAll( '.pixvisualize-woo-label-field' ).forEach(
			( field ) => {
				field.style.display = show ? '' : 'none';
			}
		);
	};

	const toggleContainerShadowFields = () => {
		const box = panel();

		if ( ! box ) {
			return;
		}

		const show = readCheck( 'pixvisualize_woo_container_shadow' );

		box.querySelectorAll(
			'.pixvisualize-woo-container-shadow-field'
		).forEach( ( field ) => {
			field.style.display = show ? '' : 'none';
		} );
	};

	// Show the product-tab fields only when the "Onglet produit" placement
	// option is selected.
	const toggleTabFields = () => {
		const box = panel();

		if ( ! box ) {
			return;
		}

		const container = box.querySelector( '.pixvisualize-woo-tab-fields' );

		if ( ! container ) {
			return;
		}

		const show =
			readVal( 'pixvisualize_woo_placement', 'above_gallery' ) ===
			'product_tab';

		container.style.display = show ? '' : 'none';
	};

	// Draggable handle → live position + sync the "Initial Position" input.
	const enableDrag = () => {
		const box = livePreview();

		if ( ! box || box.dataset.dragReady ) {
			return;
		}

		box.dataset.dragReady = '1';
		const posInput = document.getElementById( 'pixvisualize_woo_position' );
		let dragging = false;

		const move = ( event ) => {
			const rect = box.getBoundingClientRect();

			if ( ! rect.width || ! rect.height ) {
				return;
			}

			const isVertical = box.dataset.orientation === 'vertical';
			const pct =
				( ( isVertical
					? event.clientY - rect.top
					: event.clientX - rect.left ) /
					( isVertical ? rect.height : rect.width ) ) *
				100;
			const clamped = Math.round( clamp( pct, 0, 100 ) );

			box.style.setProperty( '--pc-pos', clamped + '%' );

			if ( posInput ) {
				posInput.value = clamped;
			}
		};

		box.addEventListener( 'pointerdown', ( event ) => {
			dragging = true;
			move( event );
			event.preventDefault();
		} );

		window.addEventListener( 'pointermove', ( event ) => {
			if ( dragging ) {
				move( event );
			}
		} );

		window.addEventListener( 'pointerup', () => {
			dragging = false;
		} );
	};

	// Floating shortcuts (fixed bottom-right): jump to the live preview
	// and back to the section being configured. The Preview button only
	// shows while the preview is out of view; Back appears after jumping
	// and restores the saved scroll position.
	const enableFloatBar = () => {
		const bar = document.querySelector( '.pixvisualize-woo-floatbar' );
		const previewWrap = document.querySelector(
			'.pixvisualize-woo-live-preview-wrap'
		);

		if ( ! bar || ! previewWrap ) {
			return;
		}

		const previewButton = bar.querySelector(
			'.pixvisualize-woo-float-preview'
		);
		const backButton = bar.querySelector( '.pixvisualize-woo-float-back' );
		let savedScrollY = null;

		if ( previewButton ) {
			previewButton.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				savedScrollY = window.scrollY || window.pageYOffset || 0;
				const rect = previewWrap.getBoundingClientRect();
				const y = rect.top + ( window.scrollY || 0 ) - 48;
				window.scrollTo( {
					top: Math.max( 0, y ),
					behavior: 'smooth',
				} );
				if ( backButton ) {
					backButton.style.display = '';
				}
			} );

			// Only offer the jump while the preview is out of view.
			if (
				typeof window !== 'undefined' &&
				typeof window.IntersectionObserver !== 'undefined'
			) {
				const observer = new window.IntersectionObserver(
					( entries ) => {
						const visible = entries.some(
							( entry ) => entry.isIntersecting
						);
						previewButton.style.display = visible ? 'none' : '';
					},
					{ threshold: 0.2 }
				);
				observer.observe( previewWrap );
			}
		}

		if ( backButton ) {
			backButton.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				if ( savedScrollY !== null ) {
					window.scrollTo( {
						top: savedScrollY,
						behavior: 'smooth',
					} );
					savedScrollY = null;
				}
				backButton.style.display = 'none';
			} );
		}
	};

	// Rebuild the live preview whenever any panel field changes.
	// Typing in text inputs / textareas (caption, labels, description,
	// custom SVG) must NOT rebuild the geometry on every keystroke: the
	// preview sits above the fields, so growing/shrinking it per keystroke
	// pushes the fields down and the screen jumps while typing. Text fields
	// are debounced, the description (front-end only, absent from the live
	// preview) skips the preview sync entirely, and scroll/focus are
	// preserved across the rebuild.
	const debounce = ( fn, wait ) => {
		let timer = null;
		return ( ...args ) => {
			window.clearTimeout( timer );
			timer = window.setTimeout( () => fn( ...args ), wait );
		};
	};
	const isTextField = ( target ) => {
		if ( ! target || ! target.matches ) {
			return false;
		}
		return target.matches(
			'input[type="text"], input:not([type]), textarea'
		);
	};
	const syncLivePreviewPreservingView = () => {
		const ownerDoc = panel() ? panel().ownerDocument : document;
		const active = ownerDoc.activeElement;
		const activeId = active && active.id ? active.id : null;
		const selStart =
			active && typeof active.selectionStart === 'number'
				? active.selectionStart
				: null;
		const selEnd =
			active && typeof active.selectionEnd === 'number'
				? active.selectionEnd
				: null;
		const scrollY = window.scrollY || window.pageYOffset || 0;
		syncLivePreview();
		// Restoring scroll neutralizes the push-down effect when the
		// preview grows (first caption character, empty -> visible).
		const afterY = window.scrollY || window.pageYOffset || 0;
		if ( activeId && Math.abs( afterY - scrollY ) > 2 ) {
			window.scrollTo( 0, scrollY );
		}
		if ( activeId ) {
			const refocus = document.getElementById( activeId );
			if ( refocus && ownerDoc.activeElement !== refocus ) {
				refocus.focus( { preventScroll: true } );
				if (
					selStart !== null &&
					typeof refocus.setSelectionRange === 'function'
				) {
					try {
						refocus.setSelectionRange( selStart, selEnd );
					} catch {
						// Non-text inputs (number) ignore caret restore.
					}
				}
			}
		}
	};
	const debouncedSync = debounce( syncLivePreviewPreservingView, 250 );
	if ( panel() ) {
		panel().addEventListener( 'input', ( event ) => {
			// Description is front-end only: no live-preview impact.
			if ( event.target && event.target.id === 'pixvisualize_woo_desc' ) {
				toggleTabFields();
				return;
			}
			toggleLabelFields();
			toggleTabFields();
			toggleContainerShadowFields();
			if ( isTextField( event.target ) ) {
				debouncedSync();
			} else {
				syncLivePreview();
			}
		} );
		panel().addEventListener( 'change', () => {
			syncLivePreview();
			toggleLabelFields();
			toggleTabFields();
			toggleContainerShadowFields();
		} );

		enableDrag();
		enableFloatBar();
		toggleLabelFields();
		toggleTabFields();
		toggleContainerShadowFields();
		syncImages();
		syncLivePreview();
	}
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', bootPixVisualizeWoo );
} else {
	bootPixVisualizeWoo();
}
