/**
 * Makes ajax request for deletion of a NFC resource file.
 */
const resourcesDelete = () => {
	const delClass = 'nfc-events-resources-delete';
	const del = document.querySelector(`.${delClass}`);

	if (!del) {
		return;
	}

	document.addEventListener('click', (e) => {
		if (e.target.classList.contains(delClass)) {
			const directory = e.target.getAttribute('data-directory');
			const file = e.target.getAttribute('data-file');

			if (!directory || !file) {
				return;
			}

			const parent = e.target.parentNode;
			parent.classList.add('--loading');

			if (confirm('Are you sure you want to delete this item?')) {
				jQuery.ajax({
					type: 'POST',
					url: ajax.url,
					data: {
						action: 'delete_resource',
						nonce: ajax.nonce,
						directory: directory,
						file: file,
					},
					success(response) {
						const responseData = jQuery.parseJSON(response);

						if (responseData.success) {
							parent.remove();
						} else {
							parent.classList.add('--error');
						}

						parent.classList.remove('--loading');
					},
					error(request, status, error) {
						/* eslint no-console: ["error", { allow: ["warn", "error", "log"] }] */
						console.error(`Response: ${request.responseText}`);
						console.error(`Status: ${status}`);
						console.error(`Error: ${error}`);
					},
				});
				console.log('deleted');
			} else {
				parent.classList.remove('--loading');
				// Do nothing!
				console.log('Delete cancled');
			}
		}
	});
};

export { resourcesDelete };
