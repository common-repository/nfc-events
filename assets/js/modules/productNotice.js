/**
 * Product notice close click event. Notice is present only for
 * not logged in user when redirected to post/product page.
 */
const productNotice = () => {
    const notice = document.querySelector('.nfc-product-notice');

    if (!notice) {
        return;
    }

    const close = notice.querySelector('.nfc-product-notice-close');

    if (close) {
        close.addEventListener('click', () => {
            notice.classList.add('--hide');
        });
    }
}

export {
	productNotice,
}
