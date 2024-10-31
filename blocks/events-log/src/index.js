/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { archive } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import Edit from './_edit';

registerBlockType( 'nfc-events/events-log', {
	title: 'NFC Events Log',
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
