/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { archive } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import Edit from './_edit';
import Save from './_save';

registerBlockType( 'nfc-events/restricted-content', {
	title: 'NFC Restricted Content',
	category: 'media',
	icon: archive,
	supports: {
		align: [ 'wide', 'full' ],
	},
	attributes: {
		users: {},
	},
	edit: Edit,
	save: Save,
} );
