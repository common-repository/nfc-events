
import select2 from 'select2';

const searchProducts = () => {
    const select = jQuery('.nfc-product-search'); // Needs to be jQuery object so that select2 can work properly.

    if (!select) {
        return;
    }

    select.select2({
        theme: 'classic',
        ajax: {
            url: ajax.url,
            data: (params) => {
                return {
                    term: params.term,
                    action: 'woocommerce_json_search_products_and_variations',
                    security: select.attr('data-nonce'),
                };
            },
            processResults: (data) => {
                const terms = [];

                if (data) {
                    for (const term in data) {
                        terms.push({
                            id: term,
                            text: data[term],
                        });
                    }
                }

                return {
                    results: terms
                };
            },
            cache: true
        }
    });
}

export {
    searchProducts,
}
