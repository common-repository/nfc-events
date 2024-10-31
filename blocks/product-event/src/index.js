/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { archive } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import Edit from './_edit';

registerBlockType( 'nfc-events/product-event', {
	title: 'NFC Product Event',
	category: 'media',
	icon: archive,
	supports: {
		align: [ 'wide', 'full' ],
	},
	attributes: {
		users: {
			type: 'object',
			default: {},
		},
	},
	edit: Edit,
	save() {
		return null;
	},
} );
