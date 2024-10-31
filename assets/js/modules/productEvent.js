/**
 * Event form submission. Makes a POST request with product ID, Event ID
 * and any additional form fields and gets in response a string message JSON.
 */
const productEvent = () => {
	const form = document.querySelector( '.nfc-product-event-form' );

	if ( ! form ) {
		return;
	}

	const openModal = document.querySelector(
		'.nfc-product-event-form-open-submit-modal'
	);
	const closeModal = document.querySelector(
		'.nfc-product-event-form-close-submit-modal'
	);
	const submit = form.querySelector( '.nfc-product-event-form-submit' );

	if ( ! openModal || ! closeModal || ! submit ) {
		return;
	}

	fileUpload();

	openModal.addEventListener( 'click', ( e ) => {
		e.preventDefault();

		form.classList.add( '--modal-active' );
	} );

	closeModal.addEventListener( 'click', ( e ) => {
		e.preventDefault();

		form.classList.remove( '--modal-active' );
	} );

	submit.addEventListener( 'click', ( e ) => {
		e.preventDefault();
		form.classList.add( '--loading' );
		form.classList.remove( '--error' );

		const responseContainer = form.querySelector(
			'.nfc-product-event-form-response'
		);
		responseContainer.innerHTML = '';

		const formData = jQuery(
			'.nfc-product-event-form input, .nfc-product-event-form textarea'
		).serializeArray();
		const data = new FormData();

		jQuery.each( formData, ( key, input ) => {
			data.append( `form_data[${ input.name }]`, input.value );
		} );

		const files = document.getElementById( 'nfc_events_attachment_images' )
			.files.length;

		for ( let x = 0; x < files; x++ ) {
			data.append(
				`nfc_events_attachment_images[]`,
				document.getElementById( 'nfc_events_attachment_images' ).files[
					x
				]
			);
		}

		data.append( 'action', 'set_product_event' );
		data.append( 'nonce', ajax.nonce );

		jQuery.ajax( {
			type: 'POST',
			url: ajax.url,
			data: data,
			contentType: false,
			processData: false,
			success( response ) {
				const responseData = jQuery.parseJSON( response );

				if ( responseData.success ) {
					form.classList.add( '--success' );
					responseContainer.insertAdjacentHTML(
						'beforeend',
						responseData.result
					);
				} else {
					form.classList.add( '--error' );

					responseContainer.insertAdjacentHTML(
						'beforeend',
						responseData.result
					);
				}

				form.classList.remove( '--loading' );
				form.classList.remove( '--modal-active' );
			},
			error( request, status, error ) {
				/* eslint no-console: ["error", { allow: ["warn", "error", "log"] }] */
				console.error( `Response: ${ request.responseText }` );
				console.error( `Status: ${ status }` );
				console.error( `Error: ${ error }` );
			},
		} );
	} );
};

/**
 * Multiple file upload, removal per file control.
 */
const fileUpload = () => {
	const dt = new DataTransfer();
	const uploadBtns = document.querySelectorAll( '.nfc-events-attachment' );

	uploadBtns.forEach( ( uploadBtn ) => {
		uploadBtn.addEventListener( 'change', function () {
			for ( let i = 0; i < this.files.length; i++ ) {
				const fileBlock = document.createElement( 'div' );
				const fileName = document.createElement( 'span' );
				const fileDelete = document.createElement( 'span' );
				fileName.classList.add( 'nfc-events-file-name' );
				fileDelete.classList.add( 'nfc-events-file-delete' );
				fileName.textContent = this.files.item( i ).name;
				fileDelete.textContent = '✕';

				// Check if it is image file type and create image element and read file source.
				const fileImage = document.createElement( 'span' );
				if ( this.files[ i ].type.match( 'image.*' ) ) {
					const reader = new FileReader();
					reader.onload = ( function ( theFile ) {
						return function ( e ) {
							fileImage.classList.add( 'nfc-events-file-img' );
							fileImage.innerHTML = [
								'<img class="thumb" src="',
								e.target.result,
								'" title="',
								escape( theFile.name ),
								'"/>',
							].join( '' );
						};
					} )( this.files[ i ] );

					// Read in the image file as a data URL.
					reader.readAsDataURL( this.files[ i ] );
				}

				fileBlock.append( fileImage );
				fileBlock.append( fileName );
				fileBlock.append( fileDelete );
				uploadBtn.parentNode
					.querySelector( '#nfc-events-files-names' )
					.append( fileBlock );
			}

			for ( const file of this.files ) {
				dt.items.add( file );
			}

			this.files = dt.files;

			const deleteBtns = uploadBtn.parentNode.querySelectorAll(
				'.nfc-events-file-delete'
			);
			deleteBtns.forEach( ( deleteBtn ) => {
				deleteBtn.addEventListener( 'click', ( ev ) => {
					const name = ev.target.previousSibling.textContent;
					ev.target.parentNode.remove();
					// eslint-disable-next-line no-plusplus
					for ( let i = 0; i < dt.items.length; i++ ) {
						if ( name === dt.items[ i ].getAsFile().name ) {
							dt.items.remove( i );
							// eslint-disable-next-line no-continue
							continue;
						}
					}
					// eslint-disable-next-line no-param-reassign
					uploadBtn.files = dt.files;
				} );
			} );
		} );
	} );
};

export { productEvent };
