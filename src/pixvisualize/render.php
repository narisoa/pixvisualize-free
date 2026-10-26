<?php

defined( 'ABSPATH' ) || exit;
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 */
$beforeImage = $attributes['beforeImage'] ?? '';
$afterImage = $attributes['afterImage'] ?? '';
$initialPosition = $attributes['initialPosition'] ?? 50;
$beforeLabel = ( $attributes['beforeLabel'] ?: 'Before' );
$afterLabel = ( $attributes['afterLabel'] ?: 'After' );
$showLabels = $attributes['showLabels'] ?? true;
$aspectRatio = $attributes['aspectRatio'] ?? '16/9';
$height = $attributes['height'] ?? 450;
$fullWidth = $attributes['fullWidth'] ?? false;
$alignment = $attributes['alignment'] ?? 'center';
$orientation = 'horizontal';
$handleStyle = $attributes['handleStyle'] ?? 'circle';
$handleIconSvg = $attributes['handleIconSvg'] ?? '';
$handleSize = $attributes['handleSize'] ?? 24;
$handleBackground = $attributes['handleBackground'] ?? '';
$handleArrowColor = $attributes['handleArrowColor'] ?? '#666666';
$handlePadding = $attributes['handlePadding'] ?? 0;
$labelPositionBefore = $attributes['labelPositionBefore'] ?? 'top-left';
$labelPositionAfter = $attributes['labelPositionAfter'] ?? 'top-right';
$labelBackground = $attributes['labelBackground'] ?? '#00000080';
$labelColor = $attributes['labelColor'] ?? '#ffffff';
$labelFontSize = $attributes['labelFontSize'] ?? 12;
$labelFontWeight = $attributes['labelFontWeight'] ?? 600;
$labelPaddingX = $attributes['labelPaddingX'] ?? 10;
$labelPaddingY = $attributes['labelPaddingY'] ?? 5;
$labelBorderRadius = $attributes['labelBorderRadius'] ?? 4;
$separatorThickness = $attributes['separatorThickness'] ?? 2;
$separatorColor = $attributes['separatorColor'] ?? '#ffffff';
$separatorShadow = $attributes['separatorShadow'] ?? true;
$containerShadow = $attributes['containerShadow'] ?? false;
$containerShadowColor = $attributes['containerShadowColor'] ?? '#000000';
$containerShadowHorizontal = $attributes['containerShadowHorizontal'] ?? 0;
$containerShadowVertical = $attributes['containerShadowVertical'] ?? 8;
$containerShadowBlur = $attributes['containerShadowBlur'] ?? 20;
$containerShadowSpread = $attributes['containerShadowSpread'] ?? 0;
$containerShadowOpacity = $attributes['containerShadowOpacity'] ?? 35;
$containerBorderWidth = $attributes['containerBorderWidth'] ?? 0;
$containerBorder = $attributes['containerBorder'] ?? false;
$containerBorderColor = $attributes['containerBorderColor'] ?? '#000000';
$containerBorderStyle = $attributes['containerBorderStyle'] ?? 'solid';
$containerBorderRadiusTL = $attributes['containerBorderRadiusTL'] ?? 0;
$containerBorderRadiusTR = $attributes['containerBorderRadiusTR'] ?? 0;
$containerBorderRadiusBR = $attributes['containerBorderRadiusBR'] ?? 0;
$containerBorderRadiusBL = $attributes['containerBorderRadiusBL'] ?? 0;
$containerRounded = $attributes['containerRounded'] ?? false;
$hoverMode = $attributes['hoverMode'] ?? false;
$autoScan = $attributes['autoScan'] ?? false;
$autoScanDuration = $attributes['autoScanDuration'] ?? 2500;
$magnifier = $attributes['magnifier'] ?? false;
$magnifierZoom = $attributes['magnifierZoom'] ?? 2;
$tabletPosition = $attributes['tabletPosition'] ?? 50;
$mobilePosition = $attributes['mobilePosition'] ?? 50;
$tabletRatio = $attributes['tabletRatio'] ?? '';
$mobileRatio = $attributes['mobileRatio'] ?? '';
$captionTitle = $attributes['captionTitle'] ?? '';
$captionText = $attributes['captionText'] ?? '';
$showCaption = $attributes['showCaption'] ?? true;
$captionBg = $attributes['captionBg'] ?? '#ffffff';
$captionTitleColor = $attributes['captionTitleColor'] ?? '#1e293b';
$captionTextColor = $attributes['captionTextColor'] ?? '#64748b';
$captionAlignment = $attributes['captionAlignment'] ?? 'center';
if ( !in_array( $captionAlignment, array('left', 'center', 'right'), true ) ) {
    $captionAlignment = 'center';
}
$captionTitleSize = $attributes['captionTitleSize'] ?? 22;
$captionTextSize = $attributes['captionTextSize'] ?? 15;
$captionPadding = $attributes['captionPadding'] ?? 16;
// Split X/Y (contrôle la hauteur via Y). Legacy captionPadding = repli
// pour les blocs/shortcodes enregistrés avant la séparation.
// Nouveaux défauts : X = 5 (gauche/droite), Y = 9 (haut/bas).
$captionPaddingX = $attributes['captionPaddingX'] ?? $captionPadding;
$captionPaddingY = $attributes['captionPaddingY'] ?? $captionPadding;
// Generate a unique ID for the block instance to avoid conflicts if multiple blocks are present
$blockId = 'pixvisualize-block-' . uniqid();
?>
<div
	<?php 
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() returns core-escaped markup; block form survives the Freemius formatter.
echo get_block_wrapper_attributes( array(
    'class' => 'pixvisualize-block-wrapper',
) );
// phpcs:enable
?>
	data-before-image="<?php 
echo esc_attr( $beforeImage );
?>"
	data-after-image="<?php 
echo esc_attr( $afterImage );
?>"
	data-initial-position="<?php 
echo esc_attr( $initialPosition );
?>"
	data-before-label="<?php 
echo esc_attr( $beforeLabel );
?>"
	data-after-label="<?php 
echo esc_attr( $afterLabel );
?>"
	data-show-labels="<?php 
echo ( $showLabels ? '1' : '0' );
?>"
	data-aspect-ratio="<?php 
echo esc_attr( $aspectRatio );
?>"
	data-height="<?php 
echo esc_attr( $height );
?>"
	data-full-width="<?php 
echo ( $fullWidth ? '1' : '0' );
?>"
	data-alignment="<?php 
echo esc_attr( $alignment );
?>"
	data-orientation="<?php 
echo esc_attr( $orientation );
?>"
	<?php 
?>
	data-container-shadow="<?php 
echo ( $containerShadow ? '1' : '0' );
?>"
	<?php 
?>
	data-container-border="<?php 
echo ( $containerBorder ? '1' : '0' );
?>"
	<?php 
?>
	data-container-rounded="<?php 
echo ( $containerRounded ? '1' : '0' );
?>"
	<?php 
?>
	data-caption-title="<?php 
echo esc_attr( $captionTitle );
?>"
	data-caption-text="<?php 
echo esc_attr( $captionText );
?>"
	data-show-caption="<?php 
echo ( $showCaption ? '1' : '0' );
?>"
	<?php 
?>
	id="<?php 
echo esc_attr( $blockId );
?>"
>
	<div class="pixvisualize-react-root"></div>
</div>
