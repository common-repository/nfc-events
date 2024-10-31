/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import StyledSelect from '../../components/StyleSelect';

/**
 * Edit structure of the block in the context of the editor.
 */
const Edit = ( { attributes, setAttributes } ) => {
	const blockProps = useBlockProps( { className: 'wp-block-group' } ),
		{ users } = attributes,
		[ roles, setRoles ] = useState( [] );

	useEffect( () => {
		wp.apiRequest( { path: 'nfc/v1/roles', data: { per_page: 50 } } )
			.done( ( data ) => {
				const roleNames = [];

				for ( const [ key, value ] of Object.entries( data ) ) {
					roleNames.push( { value: key, label: value.name } );
				}

				setRoles( roleNames );
			} )
			.fail( ( xhr ) => console.error( xhr.responseText ) );
	}, [] );

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Preview the form for a user role',
						'nfc-events'
					) }
					initialOpen={ true }
				>
					<StyledSelect
						value={ users }
						options={ roles }
						onChange={ ( value ) =>
							setAttributes( { users: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<ServerSideRender
				attributes={ attributes }
				block="nfc-events/product-event"
			></ServerSideRender>
		</div>
	);
};

export default Edit;
