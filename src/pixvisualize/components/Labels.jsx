export const LABEL_OFFSET = 20;

export const POSITIONS = {
	'top-left': { top: LABEL_OFFSET, left: LABEL_OFFSET },
	'top-right': { top: LABEL_OFFSET, right: LABEL_OFFSET },
	'bottom-left': { bottom: LABEL_OFFSET, left: LABEL_OFFSET },
	'bottom-right': { bottom: LABEL_OFFSET, right: LABEL_OFFSET },
};

export const getPositionStyle = ( position ) =>
	POSITIONS[ position ] || POSITIONS[ 'top-left' ];