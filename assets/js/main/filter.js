const pageContent = document.querySelector("#content");
const footer = document.querySelector(".footer");
const products = document.querySelector(".products");
const header = document.querySelector(".header");
const filter = document.querySelector(".filter");
const filterPreview = document.querySelectorAll(".filter__preview");
const filterContent = document.querySelectorAll(".filter__content");

if (filter) {
	for (let i = 0; i < filterPreview.length; i++) {
		filterPreview[i].addEventListener("click", () => {
			filterContent[i].classList.toggle("filter-active");
			filterPreview[i].classList.toggle("preview-active");
		});
	}

	let pageContentHeight = pageContent.offsetHeight;
	let headerHeight = header.offsetHeight;
	let filterPosition = filter.offsetTop;

	if (window.innerWidth > 1024) {
		if (products) {
			let productsHeight = products.offsetHeight * 1.2;
			filter.style.height = `${pageContentHeight - productsHeight}px`;
		} else {
			filter.style.height = `${
				pageContentHeight - (filterPosition - headerHeight)
			}px`;
		}
	}
	if (window.innerWidth > 1280) {
		if (products) {
			let productsHeight = products.offsetHeight * 1.2;
			filter.style.height = `${pageContentHeight - productsHeight}px`;
		}
	}
}
