export const calculatePercentage = ( current, total ) => {
	if ( ! total || total === 0 ) {
		return 50;
	}
	return Math.round( ( current / total ) * 100 );
};
