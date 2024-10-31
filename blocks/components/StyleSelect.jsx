import Select from 'react-select';
import { __ } from '@wordpress/i18n';

const selectStyles = {
	multiValue: ( styles ) => ( {
		...styles,
		margin: '5px',
	} ),
};

const StyledSelect = ( {
	value,
	options,
	onChange,
	isLoading,
	fetchOptions,
	isDisabled,
	label,
	isMulti,
} ) => (
	<div>
		<>
			{ label ? <label>{ label }</label> : '' }
			<Select
				styles={ selectStyles }
				value={ value }
				isMulti={ isMulti }
				options={ options }
				onChange={ onChange }
				onFocus={ fetchOptions }
				isLoading={ isLoading }
				isDisabled={ isDisabled }
				isClearable
				noOptionsMessage={ () => __( 'No results', 'nfc-events' ) }
			/>
		</>
	</div>
);

export default StyledSelect;
