/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { archive } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import Edit from './_edit';

registerBlockType( 'nfc-events/product-image', {
	title: 'NFC Product Image',
	category: 'media',
	icon: archive,
	supports: {
		align: [ 'wide', 'full' ],
	},
	edit: Edit,
	save() {
		return null;
	},
} );
