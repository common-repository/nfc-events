import axios from 'axios';

/**
 * Extracting all (specific post type) posts data
 * and creating an CSV file out of it.
 */
const exportEvents = () => {
	const form = document.querySelector('.nfc-file-export-events');

	if (!form) {
		return;
	}

	const submit = form.querySelector('.nfc-export-events-submit');

	if (!submit) {
		return;
	}

	submit.addEventListener('click', (e) => {
		e.preventDefault();

		form.classList.add('--loading');
		form.classList.remove('--error');

		const postType = submit.getAttribute('data-post-type');

		if (!postType) {
			setTimeout(() => {
				form.classList.remove('--loading');
			}, 500);

			return;
		}

		const origin = `${document.location.origin}/`,
			token = submit.getAttribute('data-token');

		let totalPosts = 0,
			postsDone = 0,
			state = {
				posts: [],
				baseUrl: `${origin}/wp-json/wp/v3/${postType}/?token=${token}`,
				perPage: '&per_page=100',
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

			document.querySelector(
				'.nfc-export-events-response'
			).innerText = `${total === Infinity ? 0 : total > 100 ? 100 : total
				}%`;

			return response;
		});

		getNumPosts()
			.then((numPosts) => fetchPosts(numPosts))
			.then(() => {
				let csv = [];

				const csvHeader = [
					'Event ID',
					'Event Date',
					'Event Status',
					'Event Status ID',
					'Product ID',
					'Product Name',
					'Product Unique',
					'User Email',
					'User Full Name',
					'User ID',
					'User Name',
					'User Role',
				];

				csv.push(csvHeader.join(';'));

				state.posts.forEach((post) => {
					const meta = post.event_meta;
					const row = [
						post.id,
						post.date,
						meta.event_status,
						meta.event_status_id,
						meta.product_id,
						meta.product_name,
						meta.unique,
						meta.user_email,
						meta.user_full_name,
						meta.user_id,
						meta.user_name,
						meta.user_role,
					];

					csv.push(row.join(';'));
				});

				const downloadLink = document.createElement('a'),
					blob = new Blob([csv.join('\n')], {
						type: 'application/csv',
					}),
					URL = window.URL || window.webkitURL,
					downloadUrl = URL.createObjectURL(blob);

				downloadLink.target = '_blank';
				downloadLink.download = 'nfc-events.csv';
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

export { exportEvents };
