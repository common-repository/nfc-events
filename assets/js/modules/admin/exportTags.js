import axios from 'axios';

/**
 * Extracting all chosen post url's, adding a nfc suffix
 * and creating the CSV file out of data.
 */
const exportTags = () => {
	const form = document.querySelector('.nfc-tags-export');

	if (!form) {
		return;
	}

	const submit = form.querySelector('.nfc-export-submit');

	if (!submit) {
		return;
	}

	submit.addEventListener('click', (e) => {
		e.preventDefault();

		form.classList.add('--loading');
		form.classList.remove('--error');
		form.classList.remove('--select-type');

		const postType = form.querySelector(
			'select[name="nfc_export_post_type"]'
		);

		if (!postType || !postType.value) {
			setTimeout(() => {
				form.classList.add('--select-type');
				form.classList.remove('--loading');
			}, 500);

			return;
		}

		const queryParam = postType.getAttribute('data-query-param'),
			origin = `${document.location.origin}/`,
			token = postType.getAttribute('data-token');

		let totalPosts = 0,
			postsDone = 0,
			state = {
				posts: [],
				baseUrl: `${origin}/wp-json/wp/v3/${postType.value}/?token=${token}`,
				perPage: '?per_page=100',
				wpFetchHeaders: {
					headers: {
						'Access-Control-Allow-Origin': '*',
						'Access-Control-Expose-Headers': 'x-wp-total',
					},
				},
			};

		async function getNumPosts() {
			const { headers } = await axios(
				`${state.baseUrl}${state.perPage}`,
				state.wpFetchHeaders
			);

			totalPosts = headers['x-wp-total'];

			return headers['x-wp-totalpages'];
		}

		async function fetchPosts(numPages) {
			const posts = [];

			for (let page = 1; page <= numPages; page += 1) {
				const post = axios.get(
					`${state.baseUrl}${state.perPage}&page=${page}&token=${token}`,
					state.wpFetchHeaders
				);

				posts.push(post);
			}

			await axios
				.all(posts)
				.then((response) => {
					const postData = response.map((res) => res.data);

					state.posts = postData.flat();
				})
				.catch((error) =>
					console.log(`Error fetching posts: `, error)
				);

			return true;
		}

		axios.interceptors.response.use((response) => {
			postsDone = postsDone + Object.keys(response.data).length;

			let total = Math.ceil((100 * postsDone) / totalPosts);

			document.querySelector('.nfc-export-response').innerText = `${total === Infinity ? 0 : total > 100 ? 100 : total
				}%`;

			return response;
		});

		getNumPosts()
			.then((numPosts) => fetchPosts(numPosts))
			.then(() => {
				let csv = [];

				if (postType.value === 'product') {
					const csvHeader = [
						'Product Name',
						'Product ID',
						'Product Unique',
						'NFC URL',
						'Brand',
					];

					csv.push(csvHeader.join(';'));

					state.posts.forEach((post) => {
						const meta = post.nfc_product_meta;

						if (meta) {
							const totalStock = meta.total_stock;
							const variations = meta.variations;

							if (variations) {
								variations.forEach((variation) => {
									for (
										let i = 1;
										i <= variation.total_stock;
										i++
									) {
										const row = [
											variation.name.replace(
												'&#8211;',
												'-'
											),
											variation.id,
											i,
											`${origin +
											queryParam +
											variation.id
											}&unique=${i}`,
											variation.attributes?.pa_brand
												? variation.attributes.pa_brand.toString()
												: '',
										];

										csv.push(row.join(';'));
									}
								});
							} else if (totalStock) {
								for (let i = 1; i <= totalStock; i++) {
									const row = [
										post.title.rendered,
										post.id,
										i,
										`${origin + queryParam + post.id
										}&unique=${i}`,
										meta.attributes?.pa_brand
											? meta.attributes.pa_brand.toString()
											: '',
									];

									csv.push(row.join(';'));
								}
							}
						}
					});
				} else {
					const csvHeader = ['Name', 'ID', 'NFC URL'];

					csv.push(csvHeader.join(';'));

					state.posts.forEach((post) => {
						const row = [
							post.title.rendered,
							post.id,
							origin + queryParam + post.id,
						];

						csv.push(row.join(';'));
					});
				}

				const downloadLink = document.createElement('a'),
					blob = new Blob([csv.join('\n')], {
						type: 'application/csv',
					}),
					URL = window.URL || window.webkitURL,
					downloadUrl = URL.createObjectURL(blob);

				downloadLink.target = '_blank';
				downloadLink.download = 'nfc-tag-links.csv';
				downloadLink.href = downloadUrl;

				document.body.appendChild(downloadLink);

				downloadLink.click();

				document.body.removeChild(downloadLink);
				URL.revokeObjectURL(downloadUrl);

				form.classList.remove('--loading');
				form.classList.add('--success');
			});
	});
};

export { exportTags };
