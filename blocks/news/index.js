( function ( blocks, blockEditor, components, element, i18n, ServerSideRender ) {
	'use strict';

	const el = element.createElement;
	const Fragment = element.Fragment;
	const InspectorControls = blockEditor.InspectorControls;
	const PanelBody = components.PanelBody;
	const RangeControl = components.RangeControl;
	const SelectControl = components.SelectControl;
	const __ = i18n.__;
	const categories = [
		{ label: __( 'Russia', 'chris-command' ), value: 'russia' },
		{ label: __( 'China', 'chris-command' ), value: 'china' },
		{ label: __( 'North Korea', 'chris-command' ), value: 'north-korea' },
		{ label: __( 'Tech', 'chris-command' ), value: 'tech' },
		{ label: __( 'Economics', 'chris-command' ), value: 'economics' },
		{ label: __( 'United States', 'chris-command' ), value: 'united-states' },
		{ label: __( 'Philippines', 'chris-command' ), value: 'philippines' },
	];

	blocks.registerBlockType( 'chris-command/news', {
		edit: function ( props ) {
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'News settings', 'chris-command' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Category', 'chris-command' ),
							value: props.attributes.category,
							options: categories,
							onChange: function ( category ) {
								props.setAttributes( { category: category } );
							},
						} ),
						el( RangeControl, {
							label: __( 'Stories', 'chris-command' ),
							value: props.attributes.limit,
							min: 1,
							max: 10,
							onChange: function ( limit ) {
								props.setAttributes( { limit: limit } );
							},
						} )
					)
				),
				el( ServerSideRender, {
					block: 'chris-command/news',
					attributes: props.attributes,
				} )
			);
		},
		save: function () {
			return null;
		},
	} );
} )(
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.element,
	window.wp.i18n,
	window.wp.serverSideRender
);
