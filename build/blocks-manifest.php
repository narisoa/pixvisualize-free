<?php
// This file is generated. Do not modify it manually.
return array(
	'pixvisualize' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'create-block/pixvisualize',
		'version' => '0.1.0',
		'title' => 'PixVisualize',
		'category' => 'widgets',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><g transform="translate(0.391,0.716) scale(0.016259)"><path fill="currentColor" d="M1155.9,1345.6c-6.6,0-12.4-5.8-12.4-12.4V1308c0-6.6,5.8-11.4,12.4-11.4h36.9c83.4,0,150.8-68.4,150.8-151.8V1062c0-6.6,5.8-11.4,12.4-11.4h25.2c6.6,0,11.4,4.8,11.4,11.4v111.6c0,94.6-76.4,172-171,172H1155.9z"/><path fill="currentColor" d="M236,1345.6c-110.3,0-200.5-90.2-200.5-200.5V1062c0-6.6,5.8-11.4,12.4-11.4h25.2c6.6,0,11.4,4.8,11.4,11.4v82.9c0,83.4,68.4,151.8,151.8,151.8h96.9c6.6,0,11.4,4.8,11.4,11.4v25.2c0,6.6-4.8,12.4-11.4,12.4H236z"/><path fill="currentColor" d="M1355.9,304.6c-6.6,0-12.4-5.8-12.4-12.4v-49.9c0-83.4-67.4-150.8-150.8-150.8h-36.9c-6.6,0-12.4-5.8-12.4-12.4V54c0-6.6,5.8-11.4,12.4-11.4h36.8c110.4,0,199.8,89.4,199.8,199.8v49.8c0,6.6-4.8,12.4-11.4,12.4H1355.9z"/><path fill="currentColor" d="M47.9,304.6c-6.6,0-12.4-5.8-12.4-12.4v-78.7c0-94.5,77.3-170.9,171.6-170.9h126c6.6,0,11.4,4.8,11.4,11.4v25.2c0,6.6-4.8,12.4-11.4,12.4h-96.9c-83.4,0-151.8,67.4-151.8,150.8v49.9c0,6.6-4.8,12.4-11.4,12.4H47.9z"/><polygon fill="currentColor" points="748.1,782 825.6,697.4 747.8,613.1"/><polygon fill="currentColor" points="661.4,782 583.9,697.4 661.7,613.1"/><path fill="currentColor" d="M730.5,492.6v-224h-58v-30H348.7c-58.6,0-114.1,22.5-155.5,63.9c-41.4,41.4-64.7,96.1-64.7,154.6v477.8c0,58.6,23.3,114.3,64.7,155.7c41.4,41.4,96.9,64.9,155.5,64.9h323.8v-28h57v-1h1V904.6c95.8-12,171.3-89.1,180.7-185.7c0.7-6.7,1-13.5,1-20.4C912.2,592.7,832.9,505.4,730.5,492.6zM671.5,1097.6H348.7c-88.9,0-162.2-73.8-162.2-162.7V457.1c0-88.9,73.3-160.5,162.2-160.5h322.8v197c-99,15.8-174.7,101.7-174.7,205.2c0,70,34.6,131.8,87.7,169.8c0,0,0,0,0,0c0.8,0.6,1.6,1.1,2.4,1.7c1.2,0.8,2.4,1.6,3.6,2.4c1.2,0.8,2.4,1.6,3.7,2.4c0.7,0.4,1.3,0.8,2,1.2c1.4,0.8,2.7,1.6,4.1,2.4c19.1,11,40.1,18.9,62.4,23.3c0.9,0.2,1.8,0.4,2.8,0.6c2,0.4,4,0.7,6,1V1097.6zM703.5,853.3c-85.7,0-155.2-69.5-155.2-155.2s69.5-155.2,155.2-155.2s155.2,69.5,155.2,155.2S789.2,853.3,703.5,853.3z"/><path fill="currentColor" d="M1274.5,457.1v477.8c0,58.6-22.3,114.3-63.7,155.7c-41.4,41.4-95.9,64.9-154.5,64.9H730.5v-58h325.8c88.9,0,160.2-73.8,160.2-162.7V457.1c0-88.8-71.2-160.4-160-160.5h-326v-58h326c58.5,0,113,22.5,154.3,63.9C1252.2,343.9,1274.5,398.6,1274.5,457.1z"/></g></svg>',
		'description' => 'A modern image comparison slider block.',
		'attributes' => array(
			'beforeImage' => array(
				'type' => 'string',
				'default' => ''
			),
			'afterImage' => array(
				'type' => 'string',
				'default' => ''
			),
			'initialPosition' => array(
				'type' => 'number',
				'default' => 50
			),
			'beforeLabel' => array(
				'type' => 'string',
				'default' => 'Before'
			),
			'afterLabel' => array(
				'type' => 'string',
				'default' => 'After'
			),
			'showLabels' => array(
				'type' => 'boolean',
				'default' => true
			),
			'aspectRatio' => array(
				'type' => 'string',
				'default' => '16/9'
			),
			'height' => array(
				'type' => 'number',
				'default' => 450
			),
			'fullWidth' => array(
				'type' => 'boolean',
				'default' => false
			),
			'alignment' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'orientation' => array(
				'type' => 'string',
				'default' => 'horizontal'
			),
			'handleStyle' => array(
				'type' => 'string',
				'default' => 'circle'
			),
			'handleIconSvg' => array(
				'type' => 'string',
				'default' => ''
			),
			'handleSize' => array(
				'type' => 'number',
				'default' => 24
			),
			'handleBackground' => array(
				'type' => 'string',
				'default' => ''
			),
			'handleArrowColor' => array(
				'type' => 'string',
				'default' => '#666666'
			),
			'handlePadding' => array(
				'type' => 'number',
				'default' => 0
			),
			'labelPositionBefore' => array(
				'type' => 'string',
				'default' => 'top-left'
			),
			'labelPositionAfter' => array(
				'type' => 'string',
				'default' => 'top-right'
			),
			'labelBackground' => array(
				'type' => 'string',
				'default' => '#00000080'
			),
			'labelColor' => array(
				'type' => 'string',
				'default' => '#ffffff'
			),
			'labelFontSize' => array(
				'type' => 'number',
				'default' => 12
			),
			'labelFontWeight' => array(
				'type' => 'number',
				'default' => 600
			),
			'labelPaddingX' => array(
				'type' => 'number',
				'default' => 10
			),
			'labelPaddingY' => array(
				'type' => 'number',
				'default' => 5
			),
			'labelBorderRadius' => array(
				'type' => 'number',
				'default' => 4
			),
			'separatorThickness' => array(
				'type' => 'number',
				'default' => 2
			),
			'separatorColor' => array(
				'type' => 'string',
				'default' => '#ffffff'
			),
			'separatorShadow' => array(
				'type' => 'boolean',
				'default' => true
			),
			'containerShadow' => array(
				'type' => 'boolean',
				'default' => false
			),
			'containerShadowColor' => array(
				'type' => 'string',
				'default' => '#000000'
			),
			'containerShadowHorizontal' => array(
				'type' => 'number',
				'default' => 0
			),
			'containerShadowVertical' => array(
				'type' => 'number',
				'default' => 8
			),
			'containerShadowBlur' => array(
				'type' => 'number',
				'default' => 20
			),
			'containerShadowSpread' => array(
				'type' => 'number',
				'default' => 0
			),
			'containerShadowOpacity' => array(
				'type' => 'number',
				'default' => 35
			),
			'containerBorderWidth' => array(
				'type' => 'number',
				'default' => 0
			),
			'containerBorder' => array(
				'type' => 'boolean',
				'default' => false
			),
			'containerBorderColor' => array(
				'type' => 'string',
				'default' => '#000000'
			),
			'containerBorderStyle' => array(
				'type' => 'string',
				'default' => 'solid'
			),
			'containerBorderRadiusTL' => array(
				'type' => 'number',
				'default' => 0
			),
			'containerBorderRadiusTR' => array(
				'type' => 'number',
				'default' => 0
			),
			'containerBorderRadiusBR' => array(
				'type' => 'number',
				'default' => 0
			),
			'containerBorderRadiusBL' => array(
				'type' => 'number',
				'default' => 0
			),
			'containerRounded' => array(
				'type' => 'boolean',
				'default' => false
			),
			'hoverMode' => array(
				'type' => 'boolean',
				'default' => false
			),
			'autoScan' => array(
				'type' => 'boolean',
				'default' => false
			),
			'autoScanDuration' => array(
				'type' => 'number',
				'default' => 2500
			),
			'magnifier' => array(
				'type' => 'boolean',
				'default' => false
			),
			'magnifierZoom' => array(
				'type' => 'number',
				'default' => 2
			),
			'tabletPosition' => array(
				'type' => 'number',
				'default' => 50
			),
			'mobilePosition' => array(
				'type' => 'number',
				'default' => 50
			),
			'tabletRatio' => array(
				'type' => 'string',
				'default' => ''
			),
			'mobileRatio' => array(
				'type' => 'string',
				'default' => ''
			),
			'captionTitle' => array(
				'type' => 'string',
				'default' => ''
			),
			'captionText' => array(
				'type' => 'string',
				'default' => ''
			),
			'showCaption' => array(
				'type' => 'boolean',
				'default' => true
			),
			'captionBg' => array(
				'type' => 'string',
				'default' => '#ffffff'
			),
			'captionTitleColor' => array(
				'type' => 'string',
				'default' => '#1e293b'
			),
			'captionTextColor' => array(
				'type' => 'string',
				'default' => '#64748b'
			),
			'captionAlignment' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'captionTitleSize' => array(
				'type' => 'number',
				'default' => 22
			),
			'captionTextSize' => array(
				'type' => 'number',
				'default' => 15
			),
			'captionPadding' => array(
				'type' => 'number',
				'default' => 16
			),
			'captionPaddingX' => array(
				'type' => 'number',
				'default' => 5
			),
			'captionPaddingY' => array(
				'type' => 'number',
				'default' => 9
			)
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'pixvisualize',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScript' => 'file:./view.js'
	)
);
