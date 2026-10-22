import React from 'react';
import { __ } from '@wordpress/i18n';

const ImageLayerFree = ( {
	imageSrc,
	alt = __( 'Comparison Image', 'pixvisualize' ),
	position,
	isBefore = true,
	className = '',
	children,
} ) => {
	const clipPath = isBefore
		? `inset(0 ${ 100 - position }% 0 0)`
		: `inset(0 0 0 ${ position }%)`;

	return (
		<div
			className={ `pixvisualize-image-layer ${
				isBefore
					? 'pixvisualize-image-layer-before'
					: 'pixvisualize-image-layer-after'
			} ${ className }` }
			style={ {
				position: 'absolute',
				top: 0,
				left: 0,
				width: '100%',
				height: '100%',
				overflow: 'hidden',
				zIndex: isBefore ? 2 : 1,
				clipPath,
			} }
		>
			<img
				src={ imageSrc }
				alt={ alt }
				loading="lazy"
				draggable={ false }
				style={ {
					width: '100%',
					height: '100%',
					objectFit: 'cover',
					display: 'block',
				} }
			/>
			{ children }
		</div>
	);
};

export default ImageLayerFree;
