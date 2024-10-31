/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { Disabled } from '@wordpress/components';

const Edit = () => {
	const blockProps = useBlockProps( { className: 'wp-block-group' } );

	return (
		<div { ...blockProps }>
			<Disabled>
				<ServerSideRender block="nfc-events/product-image" />
			</Disabled>
		</div>
	);
};

export default Edit;
