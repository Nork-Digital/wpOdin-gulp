//Services Header
var swiper = new Swiper(".serv__itens_desktop", {
	navigation: {
		nextEl: ".glider-next-itens-serv",
		prevEl: ".glider-prev-itens-serv",
	},
	breakpoints: {
		1024: {
			slidesPerView: 4,
		},
		1360: {
			slidesPerView: 6,
		},
	},
	allowTouchMove: false,
});