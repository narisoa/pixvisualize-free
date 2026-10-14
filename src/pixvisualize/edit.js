import { __ } from '@wordpress/i18n';
import { useRef, useState, useEffect } from '@wordpress/element';
import {
	useBlockProps,
	MediaUpload,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	Button,
	TextControl,
	ToggleControl,
	SelectControl,
	TextareaControl,
	ResizableBox,
	ColorPicker,
	BaseControl,
} from '@wordpress/components';
import './editor.scss';
import CompareSlider from './components/CompareSlider';
import { containSize } from './utils/contain';

const ALLOWED_MEDIA_TYPES = [ 'image' ];

const ImagePicker = ( { id, label, value, onChange } ) => {
	const hasImage = !! value;

	return (
		<div className="pixvisualize-editor-image-picker">
			<label htmlFor={ id }>{ label }</label>
			{ hasImage && (
				<img
					className="pixvisualize-editor-image-thumb"
					src={ value }
					alt={ label }
				/>
			) }
			<MediaUpload
				onSelect={ ( media ) => onChange( media.url ) }
				allowedTypes={ ALLOWED_MEDIA_TYPES }
				render={ ( { open } ) => (
					<Button variant="secondary" id={ id } onClick={ open }>
						{ hasImage
							? __( 'Change', 'pixvisualize' )
							: __( 'Select', 'pixvisualize' ) }
					</Button>
				) }
			/>
		</div>
	);
};

export default function Edit( { attributes, setAttributes } ) {
	const {
		beforeImage,
		afterImage,
		initialPosition,
		beforeLabel,
		afterLabel,
		showLabels,
		aspectRatio,
		height,
		fullWidth,
		alignment,
		orientation,
		handleStyle,
		handleIconSvg,
		handleSize,
		handleBackground,
		handleArrowColor,
		handlePadding,
		labelPositionBefore,
		labelPositionAfter,
		labelBackground,
		labelColor,
		labelFontSize,
		labelFontWeight,
		labelPaddingX,
		labelPaddingY,
		labelBorderRadius,
		separatorThickness,
		separatorColor,
		separatorShadow,
		containerShadow,
		containerShadowColor,
		containerShadowHorizontal,
		containerShadowVertical,
		containerShadowBlur,
		containerShadowSpread,
		containerShadowOpacity,
		containerBorderWidth,
		// FREE toggle (checkbox): default 2px solid gray border.
		containerBorder,
		containerBorderColor,
		containerBorderStyle,
		containerBorderRadiusTL,
		containerBorderRadiusTR,
		containerBorderRadiusBR,
		containerBorderRadiusBL,
		// FREE toggle (checkbox): default 16px radius.
		containerRounded,
		hoverMode,
		autoScan,
		autoScanDuration,
		magnifier,
		magnifierZoom,
		tabletPosition,
		mobilePosition,
		tabletRatio,
		mobileRatio,
		captionTitle,
		captionText,
		showCaption,
		captionBg,
		captionTitleColor,
		captionTextColor,
		captionAlignment,
		captionTitleSize,
		captionTextSize,
		captionPadding,
		captionPaddingX,
		captionPaddingY,
	} = attributes;

	// Plan Freemius (runtime) : 'free' | 'pro'. Injecté par PHP
	// (PixVisualizeController::injectEditorPlan), 'free' par défaut.
	// 'pro' = licence + code premium (pas de faux Pro sur code Free).
	const plan =
		( typeof window !== 'undefined' && window.pixvisualizePlan ) || 'free';
	const isPro = plan !== 'free';
	const needsInstall =
		typeof window !== 'undefined' &&
		window.pixvisualizeNeedsInstall === true;
	const upgradeUrl =
		( typeof window !== 'undefined' && window.pixvisualizeUpgradeUrl ) ||
		'';
	const demoUrl =
		( typeof window !== 'undefined' && window.pixvisualizeDemoUrl ) ||
		'https://i-naridigit.com/demo/pixvisualize/';

	const PRO_FEATURES = [
		__(
			'Advanced Elementor widget with full styling & live preview',
			'pixvisualize'
		),
		__( 'Advanced Gutenberg controls', 'pixvisualize' ),
		__( 'WooCommerce product gallery integration', 'pixvisualize' ),
		__( 'Generate, save, edit & reuse shortcodes', 'pixvisualize' ),
		__( 'Hover, Auto-Scan & Magnifier interactions', 'pixvisualize' ),
		__( 'Vertical orientation', 'pixvisualize' ),
		__( 'Responsive controls for every breakpoint', 'pixvisualize' ),
		__( 'Custom handles & SVG icons', 'pixvisualize' ),
		__( 'Advanced separator & handle styling', 'pixvisualize' ),
		__( 'Fully customizable Before/After labels', 'pixvisualize' ),
		__( 'Custom captions with advanced styling', 'pixvisualize' ),
		__(
			'Advanced borders, shadows & corner radius controls',
			'pixvisualize'
		),
	];

	// Preview dégradée : sans licence, les réglages Pro éventuellement
	// stockés (contenu pré-existant) sont remplacés par les défauts.
	// Étalé APRÈS les props explicites des <CompareSlider/> ci-dessous.
	const previewFreeDefaults = {
		orientation: 'horizontal',
		handleStyle: 'circle',
		handleIconSvg: '',
		handleSize: 24,
		handleBackground: '',
		handleArrowColor: '#666666',
		handlePadding: 0,
		labelPositionBefore: 'top-left',
		labelPositionAfter: 'top-right',
		labelBackground: '#00000080',
		labelColor: '#ffffff',
		labelFontSize: 12,
		labelFontWeight: 600,
		labelPaddingX: 10,
		labelPaddingY: 5,
		labelBorderRadius: 4,
		separatorThickness: 2,
		separatorColor: '#ffffff',
		separatorShadow: true,
		containerShadowColor: '#000000',
		containerShadowHorizontal: 0,
		containerShadowVertical: 8,
		containerShadowBlur: 20,
		containerShadowSpread: 0,
		containerShadowOpacity: 35,
		containerBorderWidth: 0,
		containerBorderColor: '#000000',
		containerBorderStyle: 'solid',
		containerBorderRadiusTL: 0,
		containerBorderRadiusTR: 0,
		containerBorderRadiusBR: 0,
		containerBorderRadiusBL: 0,
		hoverMode: false,
		autoScan: false,
		autoScanDuration: 2500,
		magnifier: false,
		magnifierZoom: 2,
		tabletPosition: 50,
		mobilePosition: 50,
		tabletRatio: '',
		mobileRatio: '',
		captionBg: '#ffffff',
		captionTitleColor: '#1e293b',
		captionTextColor: '#64748b',
		captionAlignment: 'center',
		captionTitleSize: 22,
		captionTextSize: 15,
		captionPadding: 16,
		captionPaddingX: 5,
		captionPaddingY: 9,
	};

	// Contain éditeur (miroir du front view.js) : la largeur fixe
	// (800px par défaut) débordait des colonnes étroites. On mesure la
	// largeur réelle du conteneur et on applique containSize — sans
	// mesure (1er rendu), on garde les dimensions désirées.
	const previewRef = useRef( null );
	const [ availWidth, setAvailWidth ] = useState( 0 );
	useEffect( () => {
		const el = previewRef.current;
		if (
			! el ||
			typeof window === 'undefined' ||
			typeof window.ResizeObserver === 'undefined'
		) {
			return undefined;
		}
		// Mesurer le PARENT (largeur de colonne, stable) plutôt que
		// le preview lui-même (dont la largeur dérive de la mesure :
		// boucle de rétroaction sinon).
		const target = el.parentElement || el;
		const ro = new window.ResizeObserver( ( entries ) => {
			const w = entries[ 0 ]?.contentRect?.width;
			if ( w ) {
				setAvailWidth( Math.floor( w ) );
			}
		} );
		ro.observe( target );
		return () => ro.disconnect();
	}, [] );
	const contained = containSize(
		height,
		aspectRatio,
		availWidth > 0 ? availWidth : null,
		Infinity
	);
	const effWidth = contained.width;
	const effHeight = contained.height;

	// Same margin logic as view.js getAlignmentMargin so the editor
	// preview reflects the front-end alignment.
	const alignmentMargins = {
		left: '0 auto 0 0',
		right: '0 0 0 auto',
		center: '0 auto',
	};
	const alignmentMargin = alignmentMargins[ alignment ] || '0 auto';

	const onResizeStop = ( event, direction, elt, delta ) => {
		const currentWidth = effWidth;
		const currentHeight = effHeight;
		const scale = direction.includes( 'right' )
			? ( currentWidth + delta.width ) / currentWidth
			: ( currentHeight + delta.height ) / currentHeight;
		const newHeight = Math.round( currentHeight * scale );
		setAttributes( {
			height: Math.max( 100, Math.min( 2000, newHeight ) ),
		} );
	};

	const aspectRatioOptions = [
		{ label: '21/9', value: '21/9' },
		{ label: '2/1', value: '2/1' },
		{ label: '16/9', value: '16/9' },
		{ label: '3/2', value: '3/2' },
		{ label: '4/3', value: '4/3' },
		{ label: __( '1/1 (Square)', 'pixvisualize' ), value: '1/1' },
		{ label: '3/4', value: '3/4' },
		{ label: '2/3', value: '2/3' },
		{ label: '9/16', value: '9/16' },
		{ label: '1/2', value: '1/2' },
		{ label: '9/21', value: '9/21' },
	];

	const alignmentOptions = [
		{ label: __( 'Left', 'pixvisualize' ), value: 'left' },
		{ label: __( 'Center', 'pixvisualize' ), value: 'center' },
		{ label: __( 'Right', 'pixvisualize' ), value: 'right' },
	];

	const labelPositionOptions = [
		{ label: __( 'Top Left', 'pixvisualize' ), value: 'top-left' },
		{ label: __( 'Top Right', 'pixvisualize' ), value: 'top-right' },
		{ label: __( 'Bottom Left', 'pixvisualize' ), value: 'bottom-left' },
		{
			label: __( 'Bottom Right', 'pixvisualize' ),
			value: 'bottom-right',
		},
	];

	const responsiveRatioOptions = [
		{ label: __( 'Inherit (desktop)', 'pixvisualize' ), value: '' },
		...aspectRatioOptions,
	];

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Images & Content', 'pixvisualize' ) }
					initialOpen={ true }
				>
					<ImagePicker
						id="pixvisualize-before-image"
						label={ __( 'Before Image', 'pixvisualize' ) }
						value={ beforeImage }
						onChange={ ( url ) =>
							setAttributes( { beforeImage: url } )
						}
					/>
					<ImagePicker
						id="pixvisualize-after-image"
						label={ __( 'After Image', 'pixvisualize' ) }
						value={ afterImage }
						onChange={ ( url ) =>
							setAttributes( { afterImage: url } )
						}
					/>
					<TextControl
						label={ __( 'Before Label', 'pixvisualize' ) }
						value={ beforeLabel }
						onChange={ ( val ) =>
							setAttributes( { beforeLabel: val } )
						}
					/>
					<TextControl
						label={ __( 'After Label', 'pixvisualize' ) }
						value={ afterLabel }
						onChange={ ( val ) =>
							setAttributes( { afterLabel: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Labels', 'pixvisualize' ) }
						help={
							showLabels
								? __(
										'The labels are visible on the slider.',
										'pixvisualize'
								  )
								: __(
										'The labels are hidden on the slider.',
										'pixvisualize'
								  )
						}
						checked={ showLabels }
						onChange={ ( val ) =>
							setAttributes( { showLabels: val } )
						}
					/>
					{ isPro && (
						<>
							<SelectControl
								label={ __(
									'Before Label Position',
									'pixvisualize'
								) }
								value={ labelPositionBefore }
								options={ labelPositionOptions }
								onChange={ ( val ) =>
									setAttributes( {
										labelPositionBefore: val,
									} )
								}
							/>
							<SelectControl
								label={ __(
									'After Label Position',
									'pixvisualize'
								) }
								value={ labelPositionAfter }
								options={ labelPositionOptions }
								onChange={ ( val ) =>
									setAttributes( {
										labelPositionAfter: val,
									} )
								}
							/>
						</>
					) }
				</PanelBody>
				<PanelBody
					title={ __( 'Caption', 'pixvisualize' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Caption', 'pixvisualize' ) }
						help={
							showCaption
								? __(
										'The title / text block is visible below the slider.',
										'pixvisualize'
								  )
								: __(
										'The title / text block below the slider is hidden.',
										'pixvisualize'
								  )
						}
						checked={ showCaption }
						onChange={ ( val ) =>
							setAttributes( { showCaption: val } )
						}
					/>
					<TextControl
						label={ __( 'Caption Title', 'pixvisualize' ) }
						help={ __(
							'Bold title displayed below the slider.',
							'pixvisualize'
						) }
						value={ captionTitle }
						onChange={ ( val ) =>
							setAttributes( { captionTitle: val } )
						}
					/>
					<TextControl
						label={ __( 'Caption Text', 'pixvisualize' ) }
						help={ __(
							'Small text displayed under the caption title.',
							'pixvisualize'
						) }
						value={ captionText }
						onChange={ ( val ) =>
							setAttributes( { captionText: val } )
						}
					/>
					{ isPro && (
						<>
							<BaseControl
								id="pixvisualize-caption-background"
								label={ __(
									'Caption Background',
									'pixvisualize'
								) }
							>
								<ColorPicker
									color={ captionBg }
									enableAlpha
									onChange={ ( val ) =>
										setAttributes( { captionBg: val } )
									}
								/>
							</BaseControl>
							<BaseControl
								id="pixvisualize-caption-title-color"
								label={ __(
									'Caption Title Color',
									'pixvisualize'
								) }
							>
								<ColorPicker
									color={ captionTitleColor }
									enableAlpha
									onChange={ ( val ) =>
										setAttributes( {
											captionTitleColor: val,
										} )
									}
								/>
							</BaseControl>
							<BaseControl
								id="pixvisualize-caption-text-color"
								label={ __(
									'Caption Text Color',
									'pixvisualize'
								) }
							>
								<ColorPicker
									color={ captionTextColor }
									enableAlpha
									onChange={ ( val ) =>
										setAttributes( {
											captionTextColor: val,
										} )
									}
								/>
							</BaseControl>
							<SelectControl
								label={ __(
									'Caption Alignment',
									'pixvisualize'
								) }
								value={ captionAlignment }
								options={ alignmentOptions }
								onChange={ ( val ) =>
									setAttributes( { captionAlignment: val } )
								}
							/>
							<RangeControl
								label={ __(
									'Caption Title Size (px)',
									'pixvisualize'
								) }
								value={ captionTitleSize }
								onChange={ ( val ) =>
									setAttributes( { captionTitleSize: val } )
								}
								min={ 12 }
								max={ 48 }
							/>
							<RangeControl
								label={ __(
									'Caption Text Size (px)',
									'pixvisualize'
								) }
								value={ captionTextSize }
								onChange={ ( val ) =>
									setAttributes( { captionTextSize: val } )
								}
								min={ 10 }
								max={ 32 }
							/>
							<RangeControl
								label={ __(
									'Caption Padding X (px)',
									'pixvisualize'
								) }
								help={ __(
									'Left/right inner spacing of the caption.',
									'pixvisualize'
								) }
								value={ captionPaddingX ?? captionPadding ?? 5 }
								onChange={ ( val ) =>
									setAttributes( { captionPaddingX: val } )
								}
								min={ 0 }
								max={ 60 }
							/>
							<RangeControl
								label={ __(
									'Caption Padding Y (px)',
									'pixvisualize'
								) }
								help={ __(
									'Top/bottom inner spacing of the caption: controls its height.',
									'pixvisualize'
								) }
								value={ captionPaddingY ?? captionPadding ?? 9 }
								onChange={ ( val ) =>
									setAttributes( { captionPaddingY: val } )
								}
								min={ 0 }
								max={ 60 }
							/>
						</>
					) }
				</PanelBody>
				<PanelBody
					title={ __( 'Layout & Orientation', 'pixvisualize' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Orientation', 'pixvisualize' ) }
						value={ isPro ? orientation : 'horizontal' }
						options={
							isPro
								? [
										{
											label: __(
												'Horizontal',
												'pixvisualize'
											),
											value: 'horizontal',
										},
										{
											label: __(
												'Vertical',
												'pixvisualize'
											),
											value: 'vertical',
										},
								  ]
								: [
										{
											label: __(
												'Horizontal',
												'pixvisualize'
											),
											value: 'horizontal',
										},
								  ]
						}
						onChange={ ( val ) =>
							setAttributes( { orientation: val } )
						}
					/>
					<SelectControl
						label={ __( 'Aspect Ratio', 'pixvisualize' ) }
						help={ __(
							'Width / height proportion, always respected (contain): 21/9 fills the width, 9/21 fills the height.',
							'pixvisualize'
						) }
						value={ aspectRatio }
						options={ aspectRatioOptions }
						onChange={ ( val ) =>
							setAttributes( { aspectRatio: val } )
						}
					/>
					<RangeControl
						label={ __( 'Height', 'pixvisualize' ) }
						help={ __(
							'Desired zone height, capped by a fixed parent height (contain).',
							'pixvisualize'
						) }
						value={ height }
						onChange={ ( val ) => setAttributes( { height: val } ) }
						min={ 100 }
						max={ 2000 }
					/>
					<ToggleControl
						label={ __( 'Full Width', 'pixvisualize' ) }
						help={
							fullWidth
								? __(
										'Spans the content width (ratio kept, centered). Useless with a fixed parent height — contain already caps it.',
										'pixvisualize'
								  )
								: __(
										'The block keeps a custom size (width matched to the ratio).',
										'pixvisualize'
								  )
						}
						checked={ fullWidth }
						onChange={ ( val ) =>
							setAttributes( { fullWidth: val } )
						}
					/>
					<SelectControl
						label={ __( 'Alignment', 'pixvisualize' ) }
						value={ alignment }
						options={ alignmentOptions }
						onChange={ ( val ) =>
							setAttributes( { alignment: val } )
						}
					/>
					<RangeControl
						label={ __( 'Initial Position', 'pixvisualize' ) }
						help={ __(
							'Starting position of the divider: 0% = fully "Before", 100% = fully "After".',
							'pixvisualize'
						) }
						value={ initialPosition }
						onChange={ ( val ) =>
							setAttributes( { initialPosition: val } )
						}
						min={ 0 }
						max={ 100 }
					/>
					{ isPro && (
						<>
							<div className="pixvisualize-editor-section">
								{ __( 'Responsive', 'pixvisualize' ) }
							</div>
							<RangeControl
								label={ __(
									'Tablet Position',
									'pixvisualize'
								) }
								help={ __(
									'Divider position on tablets (768px–1024px): 0% = fully "Before", 100% = fully "After".',
									'pixvisualize'
								) }
								value={ tabletPosition }
								onChange={ ( val ) =>
									setAttributes( { tabletPosition: val } )
								}
								min={ 0 }
								max={ 100 }
							/>
							<RangeControl
								label={ __(
									'Mobile Position',
									'pixvisualize'
								) }
								help={ __(
									'Divider position on phones (below 768px): 0% = fully "Before", 100% = fully "After".',
									'pixvisualize'
								) }
								value={ mobilePosition }
								onChange={ ( val ) =>
									setAttributes( { mobilePosition: val } )
								}
								min={ 0 }
								max={ 100 }
							/>
							<SelectControl
								label={ __( 'Tablet Ratio', 'pixvisualize' ) }
								help={ __(
									'Leave empty to inherit the desktop ratio.',
									'pixvisualize'
								) }
								value={ tabletRatio }
								options={ responsiveRatioOptions }
								onChange={ ( val ) =>
									setAttributes( { tabletRatio: val } )
								}
							/>
							<SelectControl
								label={ __( 'Mobile Ratio', 'pixvisualize' ) }
								help={ __(
									'Leave empty to inherit the desktop ratio.',
									'pixvisualize'
								) }
								value={ mobileRatio }
								options={ responsiveRatioOptions }
								onChange={ ( val ) =>
									setAttributes( { mobileRatio: val } )
								}
							/>
						</>
					) }
				</PanelBody>
				<PanelBody
					title={ __( 'Appearance', 'pixvisualize' ) }
					initialOpen={ false }
				>
					{ isPro && (
						<>
							<div className="pixvisualize-editor-section">
								{ __( 'Handle Style', 'pixvisualize' ) }
							</div>
							<SelectControl
								label={ __( 'Handle Style', 'pixvisualize' ) }
								help={ __(
									'Shape of the cursor that separates the two images.',
									'pixvisualize'
								) }
								value={ handleStyle }
								options={ [
									{
										label: __( 'Circle', 'pixvisualize' ),
										value: 'circle',
									},
									{
										label: __(
											'Transparent',
											'pixvisualize'
										),
										value: 'transparent',
									},
									{
										label: __( 'Line', 'pixvisualize' ),
										value: 'line',
									},
									{
										label: __( 'Arrows', 'pixvisualize' ),
										value: 'arrows',
									},
									{
										label: __(
											'Custom SVG',
											'pixvisualize'
										),
										value: 'custom',
									},
								] }
								onChange={ ( val ) =>
									setAttributes( { handleStyle: val } )
								}
							/>
							{ handleStyle === 'custom' && (
								<TextareaControl
									label={ __(
										'Custom SVG Icon (<svg> tag…)',
										'pixvisualize'
									) }
									help={ __(
										'Paste <svg viewBox="…">…</svg> only: XML prolog, DOCTYPE, metadata and width/height are removed automatically. Use fill="currentColor" so the icon inherits Handle Arrows Color. Without an SVG, the Circle style is used.',
										'pixvisualize'
									) }
									value={ handleIconSvg }
									onChange={ ( val ) =>
										setAttributes( { handleIconSvg: val } )
									}
									rows={ 6 }
								/>
							) }
							<RangeControl
								label={ __(
									'Handle Size (px)',
									'pixvisualize'
								) }
								help={ __(
									'Diameter of the handle circle: independent size control.',
									'pixvisualize'
								) }
								value={ handleSize }
								onChange={ ( val ) =>
									setAttributes( { handleSize: val } )
								}
								min={ 16 }
								max={ 64 }
							/>
							<BaseControl
								id="pixvisualize-handle-arrow-color"
								label={ __(
									'Handle Arrows Color',
									'pixvisualize'
								) }
								help={ __(
									'Color of the arrows inside the handle (independent control).',
									'pixvisualize'
								) }
							>
								<ColorPicker
									color={ handleArrowColor || '#666666' }
									enableAlpha
									onChange={ ( val ) =>
										setAttributes( {
											handleArrowColor: val,
										} )
									}
								/>
							</BaseControl>
							<RangeControl
								label={ __(
									'Handle Padding (px)',
									'pixvisualize'
								) }
								help={ __(
									'Inner spacing between the icon and the edge of the handle background, in px.',
									'pixvisualize'
								) }
								value={ handlePadding }
								onChange={ ( val ) =>
									setAttributes( { handlePadding: val } )
								}
								min={ 0 }
								max={ 20 }
							/>
							<div className="pixvisualize-editor-section">
								{ __( 'Colors', 'pixvisualize' ) }
							</div>
							<RangeControl
								label={ __(
									'Separator Thickness',
									'pixvisualize'
								) }
								help={ __(
									'Thickness of the vertical bar: independent control.',
									'pixvisualize'
								) }
								value={ separatorThickness }
								onChange={ ( val ) =>
									setAttributes( { separatorThickness: val } )
								}
								min={ 1 }
								max={ 10 }
							/>
							<BaseControl
								id="pixvisualize-separator-color"
								label={ __(
									'Cursor & Handle Color',
									'pixvisualize'
								) }
								help={ __(
									'Single synchronized control: background of the vertical bar and the handle circle.',
									'pixvisualize'
								) }
							>
								<ColorPicker
									color={ separatorColor }
									enableAlpha
									onChange={ ( val ) =>
										setAttributes( { separatorColor: val } )
									}
								/>
							</BaseControl>
							<ToggleControl
								label={ __( 'Drop Shadow', 'pixvisualize' ) }
								help={
									separatorShadow
										? __(
												'Grouped shadow on the whole cursor + handle (filter: drop-shadow, no seam at the junction).',
												'pixvisualize'
										  )
										: __(
												'No shadow is applied to the cursor.',
												'pixvisualize'
										  )
								}
								checked={ separatorShadow }
								onChange={ ( val ) =>
									setAttributes( { separatorShadow: val } )
								}
							/>
						</>
					) }
					<div className="pixvisualize-editor-section">
						{ __( 'Container Shadow', 'pixvisualize' ) }
					</div>
					<ToggleControl
						label={ __( 'Container Shadow', 'pixvisualize' ) }
						help={
							containerShadow
								? __(
										'A soft shadow is applied to the whole slider.',
										'pixvisualize'
								  )
								: __(
										'No shadow is applied to the slider.',
										'pixvisualize'
								  )
						}
						checked={ containerShadow }
						onChange={ ( val ) =>
							setAttributes( { containerShadow: val } )
						}
					/>
					{ isPro && containerShadow && (
						<>
							<BaseControl
								id="pixvisualize-container-shadow-color"
								label={ __(
									'Container Shadow Color',
									'pixvisualize'
								) }
							>
								<ColorPicker
									color={ containerShadowColor }
									onChange={ ( val ) =>
										setAttributes( {
											containerShadowColor: val,
										} )
									}
								/>
							</BaseControl>
							<RangeControl
								label={ __(
									'Container Shadow Horizontal Offset',
									'pixvisualize'
								) }
								help={ __(
									'Horizontal distance of the shadow, in px.',
									'pixvisualize'
								) }
								value={ containerShadowHorizontal }
								onChange={ ( val ) =>
									setAttributes( {
										containerShadowHorizontal: val,
									} )
								}
								min={ -50 }
								max={ 50 }
							/>
							<RangeControl
								label={ __(
									'Container Shadow Vertical Offset',
									'pixvisualize'
								) }
								help={ __(
									'Vertical distance of the shadow, in px.',
									'pixvisualize'
								) }
								value={ containerShadowVertical }
								onChange={ ( val ) =>
									setAttributes( {
										containerShadowVertical: val,
									} )
								}
								min={ -50 }
								max={ 50 }
							/>
							<RangeControl
								label={ __(
									'Container Shadow Blur Radius',
									'pixvisualize'
								) }
								help={ __(
									'Blur softness of the shadow, in px.',
									'pixvisualize'
								) }
								value={ containerShadowBlur }
								onChange={ ( val ) =>
									setAttributes( {
										containerShadowBlur: val,
									} )
								}
								min={ 0 }
								max={ 60 }
							/>
							<RangeControl
								label={ __(
									'Container Shadow Spread Radius',
									'pixvisualize'
								) }
								help={ __(
									'How far the shadow spreads, in px.',
									'pixvisualize'
								) }
								value={ containerShadowSpread }
								onChange={ ( val ) =>
									setAttributes( {
										containerShadowSpread: val,
									} )
								}
								min={ -20 }
								max={ 40 }
							/>
							<RangeControl
								label={ __(
									'Container Shadow Opacity',
									'pixvisualize'
								) }
								help={ __(
									'Shadow opacity, from 0 (transparent) to 100 (opaque).',
									'pixvisualize'
								) }
								value={ containerShadowOpacity }
								onChange={ ( val ) =>
									setAttributes( {
										containerShadowOpacity: val,
									} )
								}
								min={ 0 }
								max={ 100 }
							/>
						</>
					) }
					<div className="pixvisualize-editor-section">
						{ __( 'Container Border', 'pixvisualize' ) }
					</div>
					<ToggleControl
						label={ __( 'Show Border', 'pixvisualize' ) }
						help={
							containerBorder
								? __(
										'A 2px solid gray border is applied to the container.',
										'pixvisualize'
								  )
								: __(
										'No border is applied to the container.',
										'pixvisualize'
								  )
						}
						checked={ containerBorder }
						onChange={ ( val ) =>
							setAttributes( { containerBorder: val } )
						}
					/>
					{ isPro && (
						<>
							<RangeControl
								label={ __( 'Border Width', 'pixvisualize' ) }
								help={ __(
									'Thickness of the container border, in px.',
									'pixvisualize'
								) }
								value={ containerBorderWidth }
								onChange={ ( val ) =>
									setAttributes( {
										containerBorderWidth: val,
									} )
								}
								min={ 0 }
								max={ 20 }
							/>
							<SelectControl
								label={ __( 'Border Style', 'pixvisualize' ) }
								value={ containerBorderStyle }
								options={ [
									{
										label: __( 'Solid', 'pixvisualize' ),
										value: 'solid',
									},
									{
										label: __( 'Dashed', 'pixvisualize' ),
										value: 'dashed',
									},
									{
										label: __( 'Dotted', 'pixvisualize' ),
										value: 'dotted',
									},
									{
										label: __( 'Double', 'pixvisualize' ),
										value: 'double',
									},
								] }
								onChange={ ( val ) =>
									setAttributes( {
										containerBorderStyle: val,
									} )
								}
							/>
							<BaseControl
								id="pixvisualize-container-border-color"
								label={ __( 'Border Color', 'pixvisualize' ) }
							>
								<ColorPicker
									color={ containerBorderColor }
									enableAlpha
									onChange={ ( val ) =>
										setAttributes( {
											containerBorderColor: val,
										} )
									}
								/>
							</BaseControl>
						</>
					) }
					<div className="pixvisualize-editor-section">
						{ __( 'Border Radius', 'pixvisualize' ) }
					</div>
					<ToggleControl
						label={ __( 'Rounded Corners', 'pixvisualize' ) }
						help={
							containerRounded
								? __(
										'A 16px radius is applied to all corners.',
										'pixvisualize'
								  )
								: __(
										'The container corners stay square.',
										'pixvisualize'
								  )
						}
						checked={ containerRounded }
						onChange={ ( val ) =>
							setAttributes( { containerRounded: val } )
						}
					/>
					{ isPro && (
						<>
							<RangeControl
								label={ __(
									'Top Left Radius',
									'pixvisualize'
								) }
								help={ __(
									'Corner radius of the container, in px.',
									'pixvisualize'
								) }
								value={ containerBorderRadiusTL }
								onChange={ ( val ) =>
									setAttributes( {
										containerBorderRadiusTL: val,
									} )
								}
								min={ 0 }
								max={ 100 }
							/>
							<RangeControl
								label={ __(
									'Top Right Radius',
									'pixvisualize'
								) }
								value={ containerBorderRadiusTR }
								onChange={ ( val ) =>
									setAttributes( {
										containerBorderRadiusTR: val,
									} )
								}
								min={ 0 }
								max={ 100 }
							/>
							<RangeControl
								label={ __(
									'Bottom Right Radius',
									'pixvisualize'
								) }
								value={ containerBorderRadiusBR }
								onChange={ ( val ) =>
									setAttributes( {
										containerBorderRadiusBR: val,
									} )
								}
								min={ 0 }
								max={ 100 }
							/>
							<RangeControl
								label={ __(
									'Bottom Left Radius',
									'pixvisualize'
								) }
								value={ containerBorderRadiusBL }
								onChange={ ( val ) =>
									setAttributes( {
										containerBorderRadiusBL: val,
									} )
								}
								min={ 0 }
								max={ 100 }
							/>
						</>
					) }
					{ isPro && (
						<>
							<div className="pixvisualize-editor-section">
								{ __(
									'Label Typography & Style',
									'pixvisualize'
								) }
							</div>
							<BaseControl
								id="pixvisualize-label-background"
								label={ __(
									'Label Background',
									'pixvisualize'
								) }
							>
								<ColorPicker
									color={ labelBackground }
									enableAlpha
									onChange={ ( val ) =>
										setAttributes( {
											labelBackground: val,
										} )
									}
								/>
							</BaseControl>
							<BaseControl
								id="pixvisualize-label-color"
								label={ __( 'Text Color', 'pixvisualize' ) }
							>
								<ColorPicker
									color={ labelColor }
									enableAlpha
									onChange={ ( val ) =>
										setAttributes( { labelColor: val } )
									}
								/>
							</BaseControl>
							<RangeControl
								label={ __(
									'Label Font Size',
									'pixvisualize'
								) }
								help={ __(
									'Font size of the "Before" / "After" captions, in px.',
									'pixvisualize'
								) }
								value={ labelFontSize }
								onChange={ ( val ) =>
									setAttributes( { labelFontSize: val } )
								}
								min={ 10 }
								max={ 24 }
							/>
							<RangeControl
								label={ __(
									'Label Font Weight',
									'pixvisualize'
								) }
								help={ __(
									'Thickness of the label text (400 = normal, 700 = bold).',
									'pixvisualize'
								) }
								value={ labelFontWeight }
								onChange={ ( val ) =>
									setAttributes( { labelFontWeight: val } )
								}
								min={ 400 }
								max={ 900 }
								step={ 100 }
							/>
							<RangeControl
								label={ __(
									'Label Padding (horizontal)',
									'pixvisualize'
								) }
								help={ __(
									'Left/right inner spacing of the label, in px.',
									'pixvisualize'
								) }
								value={ labelPaddingX }
								onChange={ ( val ) =>
									setAttributes( { labelPaddingX: val } )
								}
								min={ 0 }
								max={ 30 }
							/>
							<RangeControl
								label={ __(
									'Label Padding (vertical)',
									'pixvisualize'
								) }
								help={ __(
									'Top/bottom inner spacing of the label, in px.',
									'pixvisualize'
								) }
								value={ labelPaddingY }
								onChange={ ( val ) =>
									setAttributes( { labelPaddingY: val } )
								}
								min={ 0 }
								max={ 30 }
							/>
							<RangeControl
								label={ __(
									'Label Border Radius',
									'pixvisualize'
								) }
								value={ labelBorderRadius }
								onChange={ ( val ) =>
									setAttributes( { labelBorderRadius: val } )
								}
								min={ 0 }
								max={ 20 }
							/>
						</>
					) }
				</PanelBody>
				{ isPro && (
					<PanelBody
						title={ __( 'Interactions', 'pixvisualize' ) }
						initialOpen={ false }
					>
						<div className="pixvisualize-editor-section">
							{ __( 'Move on Hover', 'pixvisualize' ) }
						</div>
						<ToggleControl
							label={ __( 'Move on Hover', 'pixvisualize' ) }
							help={
								hoverMode
									? __(
											'On hover, the cursor moves under the mouse, without clicking.',
											'pixvisualize'
									  )
									: __(
											'The cursor only moves by clicking and dragging.',
											'pixvisualize'
									  )
							}
							checked={ hoverMode }
							onChange={ ( val ) =>
								setAttributes( { hoverMode: val } )
							}
						/>
						<div className="pixvisualize-editor-section">
							{ __( 'Auto-Slide', 'pixvisualize' ) }
						</div>
						<ToggleControl
							label={ __(
								'Auto-Slide Animation',
								'pixvisualize'
							) }
							help={
								autoScan
									? __(
											'The cursor automatically sweeps the images on load.',
											'pixvisualize'
									  )
									: __(
											'No animation on load.',
											'pixvisualize'
									  )
							}
							checked={ autoScan }
							onChange={ ( val ) =>
								setAttributes( { autoScan: val } )
							}
						/>
						{ autoScan && (
							<RangeControl
								label={ __(
									'Auto-Slide Duration (ms)',
									'pixvisualize'
								) }
								help={ __(
									'How long the auto-slide takes to sweep from 100% to 0% then back to the initial position.',
									'pixvisualize'
								) }
								value={ autoScanDuration }
								onChange={ ( val ) =>
									setAttributes( { autoScanDuration: val } )
								}
								min={ 1000 }
								max={ 10000 }
								step={ 100 }
							/>
						) }
						<div className="pixvisualize-editor-section">
							{ __( 'Magnifier', 'pixvisualize' ) }
						</div>
						<ToggleControl
							label={ __( 'Magnifier', 'pixvisualize' ) }
							help={
								magnifier
									? __(
											'A magnifier enlarges the hovered area of the image.',
											'pixvisualize'
									  )
									: __(
											'No magnifier effect on hover.',
											'pixvisualize'
									  )
							}
							checked={ magnifier }
							onChange={ ( val ) =>
								setAttributes( { magnifier: val } )
							}
						/>
						{ magnifier && (
							<RangeControl
								label={ __(
									'Magnifier Zoom (x)',
									'pixvisualize'
								) }
								help={ __(
									'Zoom factor of the magnifier lens.',
									'pixvisualize'
								) }
								value={ magnifierZoom }
								onChange={ ( val ) =>
									setAttributes( { magnifierZoom: val } )
								}
								min={ 1.5 }
								max={ 4 }
								step={ 0.5 }
							/>
						) }
					</PanelBody>
				) }
				{ ! isPro && ! needsInstall && (
					<PanelBody
						title={ __( 'PixVisualize Pro', 'pixvisualize' ) }
						initialOpen={ true }
						className="pixvisualize-editor-pro-panel"
					>
						<div className="pixvisualize-editor-pro-panel__card">
							<p className="pixvisualize-editor-pro-panel__title">
								{ __(
									'Unlock advanced features to take your comparisons further:',
									'pixvisualize'
								) }
							</p>
							<ul className="pixvisualize-editor-pro-panel__list">
								{ PRO_FEATURES.map( ( feature ) => (
									<li key={ feature }>
										<span
											className="pixvisualize-editor-pro-panel__dot"
											aria-hidden="true"
										>
											•
										</span>
										{ feature }
									</li>
								) ) }
							</ul>
							<div className="pixvisualize-editor-pro-panel__actions">
								{ upgradeUrl && (
									<a
										className="pixvisualize-editor-pro-panel__cta"
										href={ upgradeUrl }
										target="_blank"
										rel="noopener noreferrer"
									>
										{ __( 'Go Pro', 'pixvisualize' ) }
									</a>
								) }
								<a
									className="pixvisualize-editor-pro-panel__cta pixvisualize-editor-pro-panel__cta--secondary"
									href={ demoUrl }
									target="_blank"
									rel="noopener noreferrer"
								>
									{ __( 'View Pro demo', 'pixvisualize' ) }
								</a>
							</div>
						</div>
					</PanelBody>
				) }
				{ needsInstall && (
					<PanelBody
						title={ __(
							'Pro license active — install Pro',
							'pixvisualize'
						) }
						initialOpen={ true }
						className="pixvisualize-editor-pro-panel"
					>
						<div className="pixvisualize-editor-pro-panel__card">
							<p className="pixvisualize-editor-pro-panel__title">
								{ __(
									'Your Pro license is active on the Free code. Install pixvisualize-pro to unlock Hover, Auto-Scan and Magnifier on the site.',
									'pixvisualize'
								) }
							</p>
						</div>
					</PanelBody>
				) }
			</InspectorControls>

			<div { ...useBlockProps() }>
				<div
					className="pixvisualize-editor-preview"
					ref={ previewRef }
					style={ {
						// Miroir du front (view.js) : la marge d'alignement
						// n'agit que sur une boîte à largeur contrainte.
						// Sans width, le div remplit le parent et les
						// marges auto sont sans effet visible.
						width: `min(100%, ${ effWidth }px)`,
						margin: fullWidth ? '0 auto' : alignmentMargin,
					} }
				>
					{ fullWidth ? (
						<div
							style={ {
								width: `min(100%, ${ effWidth }px)`,
								margin: '0 auto',
							} }
						>
							<CompareSlider
								beforeImage={ beforeImage }
								afterImage={ afterImage }
								initialPosition={ initialPosition }
								beforeLabel={ beforeLabel }
								afterLabel={ afterLabel }
								showLabels={ showLabels }
								aspectRatio={ aspectRatio }
								height={ effHeight }
								captionTitle={ captionTitle }
								captionText={ captionText }
								showCaption={ showCaption }
								captionBg={ captionBg }
								captionTitleColor={ captionTitleColor }
								captionTextColor={ captionTextColor }
								captionAlignment={ captionAlignment }
								captionTitleSize={ captionTitleSize }
								captionTextSize={ captionTextSize }
								captionPadding={ captionPadding }
								captionPaddingX={
									captionPaddingX ?? captionPadding
								}
								captionPaddingY={
									captionPaddingY ?? captionPadding
								}
								orientation={ orientation }
								handleStyle={ handleStyle }
								handleIconSvg={ handleIconSvg }
								handleSize={ handleSize }
								handleBackground={ handleBackground }
								handleArrowColor={ handleArrowColor }
								handlePadding={ handlePadding }
								labelPositionBefore={ labelPositionBefore }
								labelPositionAfter={ labelPositionAfter }
								labelBackground={ labelBackground }
								labelColor={ labelColor }
								labelFontSize={ labelFontSize }
								labelFontWeight={ labelFontWeight }
								labelPaddingX={ labelPaddingX }
								labelPaddingY={ labelPaddingY }
								labelBorderRadius={ labelBorderRadius }
								separatorThickness={ separatorThickness }
								separatorColor={ separatorColor }
								separatorShadow={ separatorShadow }
								containerShadow={ containerShadow }
								containerShadowColor={ containerShadowColor }
								containerShadowHorizontal={
									containerShadowHorizontal
								}
								containerShadowVertical={
									containerShadowVertical
								}
								containerShadowBlur={ containerShadowBlur }
								containerShadowSpread={ containerShadowSpread }
								containerShadowOpacity={
									containerShadowOpacity
								}
								containerBorderWidth={ containerBorderWidth }
								containerBorder={ containerBorder }
								containerBorderColor={ containerBorderColor }
								containerBorderStyle={ containerBorderStyle }
								containerBorderRadiusTL={
									containerBorderRadiusTL
								}
								containerBorderRadiusTR={
									containerBorderRadiusTR
								}
								containerBorderRadiusBR={
									containerBorderRadiusBR
								}
								containerBorderRadiusBL={
									containerBorderRadiusBL
								}
								containerRounded={ containerRounded }
								hoverMode={ hoverMode }
								autoScan={ autoScan }
								autoScanDuration={ autoScanDuration }
								magnifier={ magnifier }
								magnifierZoom={ magnifierZoom }
								tabletPosition={ tabletPosition }
								mobilePosition={ mobilePosition }
								tabletRatio={ tabletRatio }
								mobileRatio={ mobileRatio }
								{ ...( ! isPro && previewFreeDefaults ) }
							/>
						</div>
					) : (
						<ResizableBox
							size={ {
								width: effWidth,
								height: effHeight,
							} }
							minWidth={ 100 }
							minHeight={ 100 }
							maxWidth={ 2000 }
							maxHeight={ 2000 }
							enable={ {
								top: false,
								right: true,
								bottom: true,
								left: false,
								topRight: false,
								bottomRight: true,
								bottomLeft: false,
								topLeft: false,
							} }
							showHandle
							onResizeStop={ onResizeStop }
						>
							<CompareSlider
								beforeImage={ beforeImage }
								afterImage={ afterImage }
								initialPosition={ initialPosition }
								beforeLabel={ beforeLabel }
								afterLabel={ afterLabel }
								showLabels={ showLabels }
								aspectRatio={ aspectRatio }
								height={ effHeight }
								captionTitle={ captionTitle }
								captionText={ captionText }
								showCaption={ showCaption }
								captionBg={ captionBg }
								captionTitleColor={ captionTitleColor }
								captionTextColor={ captionTextColor }
								captionAlignment={ captionAlignment }
								captionTitleSize={ captionTitleSize }
								captionTextSize={ captionTextSize }
								captionPadding={ captionPadding }
								captionPaddingX={
									captionPaddingX ?? captionPadding
								}
								captionPaddingY={
									captionPaddingY ?? captionPadding
								}
								orientation={ orientation }
								handleStyle={ handleStyle }
								handleIconSvg={ handleIconSvg }
								handleSize={ handleSize }
								handleBackground={ handleBackground }
								handleArrowColor={ handleArrowColor }
								handlePadding={ handlePadding }
								labelPositionBefore={ labelPositionBefore }
								labelPositionAfter={ labelPositionAfter }
								labelBackground={ labelBackground }
								labelColor={ labelColor }
								labelFontSize={ labelFontSize }
								labelFontWeight={ labelFontWeight }
								labelPaddingX={ labelPaddingX }
								labelPaddingY={ labelPaddingY }
								labelBorderRadius={ labelBorderRadius }
								separatorThickness={ separatorThickness }
								separatorColor={ separatorColor }
								separatorShadow={ separatorShadow }
								containerShadow={ containerShadow }
								containerShadowColor={ containerShadowColor }
								containerShadowHorizontal={
									containerShadowHorizontal
								}
								containerShadowVertical={
									containerShadowVertical
								}
								containerShadowBlur={ containerShadowBlur }
								containerShadowSpread={ containerShadowSpread }
								containerShadowOpacity={
									containerShadowOpacity
								}
								containerBorderWidth={ containerBorderWidth }
								containerBorder={ containerBorder }
								containerBorderColor={ containerBorderColor }
								containerBorderStyle={ containerBorderStyle }
								containerBorderRadiusTL={
									containerBorderRadiusTL
								}
								containerBorderRadiusTR={
									containerBorderRadiusTR
								}
								containerBorderRadiusBR={
									containerBorderRadiusBR
								}
								containerBorderRadiusBL={
									containerBorderRadiusBL
								}
								containerRounded={ containerRounded }
								hoverMode={ hoverMode }
								autoScan={ autoScan }
								autoScanDuration={ autoScanDuration }
								magnifier={ magnifier }
								magnifierZoom={ magnifierZoom }
								tabletPosition={ tabletPosition }
								mobilePosition={ mobilePosition }
								tabletRatio={ tabletRatio }
								mobileRatio={ mobileRatio }
								{ ...( ! isPro && previewFreeDefaults ) }
							/>
						</ResizableBox>
					) }
				</div>
			</div>
		</>
	);
}
