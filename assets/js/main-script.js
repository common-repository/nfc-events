import {productEvent} from "./modules/productEvent";
import {productNotice} from "./modules/productNotice";
import {searchProducts} from "./modules/searchProducts";

document.addEventListener('DOMContentLoaded', () => {
	productEvent();
	productNotice();
	searchProducts();
});
