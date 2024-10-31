/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, Disabled } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import styled from 'styled-components';
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

	const Description = styled.p`
		color: #999;
		font-size: 15px;
		font-style: italic;

		span {
			display: block;
			font-size: 12px;
			font-style: normal;
		}
	`;

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody
					title={ __( 'Allowed user roles', 'nfc-events' ) }
					initialOpen={ true }
				>
					<StyledSelect
						value={ users }
						isMulti
						options={ roles }
						onChange={ ( value ) =>
							setAttributes( { users: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<Disabled>
				<Description>
					{ __( 'Restricted content block:', 'nfc-events' ) }
					<span>
						{ __(
							'The content within this block will be available only for users you specify within block options.',
							'nfc-events'
						) }
					</span>
				</Description>
			</Disabled>

			<InnerBlocks />
		</div>
	);
};

export default Edit;
