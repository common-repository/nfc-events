/**
 * Triggers download of csv file based on element data attribute.
 */
const eventsBulkExport = () => {
	const downloadAnchor = document.querySelector('.nfc-admin-bulk-download');

	if (!downloadAnchor) {
		return;
	}

    const csvData = downloadAnchor.getAttribute('data-events-data');

    if (csvData) {
        const downloadLink = document.createElement('a'),
            blob = new Blob([csvData], { type: 'application/csv' }),
            URL = window.URL || window.webkitURL,
            downloadUrl = URL.createObjectURL(blob);

        downloadLink.textContent = downloadAnchor.textContent;
        downloadLink.target = '_blank';
        downloadLink.download = 'nfc-events.csv';
        downloadLink.href = downloadUrl;

        downloadAnchor.innerHTML = '';
        downloadAnchor.appendChild(downloadLink);
        downloadLink.click();

        history.replaceState && history.replaceState(
            null, '', location.pathname + location.search.replace(/[\?&]nfc_export=[^&]+/, '').replace(/^&/, '?')
        );
    }
}

export {
    eventsBulkExport
}