( function ( blocks, element, i18n ) {
	'use strict';

	const el = element.createElement;
	const __ = i18n.__;

	blocks.registerBlockType( 'chris-command/dashboard', {
		edit: function () {
			return el(
				'div',
				{ className: 'chris-command-dashboard-editor' },
				el( 'strong', null, __( 'Chris Command Dashboard', 'chris-command' ) ),
				el( 'p', null, __( 'The complete public command-center interface renders on the frontend.', 'chris-command' ) )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.i18n );
