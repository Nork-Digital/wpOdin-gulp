//Serv
const mobileServ = document.querySelector(".mobile__serv");

const Header = document.querySelector(".header");
function servAtivo() {
	Header.classList.toggle("mobileServActive");
}
mobileServ.addEventListener("click", servAtivo);

//
const servItensDesktop = document.querySelector(".serv__itens");
const servWrapper = document.querySelector(".swiper-wrapper");
if (window.innerWidth > 1024) {
	servItensDesktop.classList.add("serv__itens_desktop");
} else {
	servItensDesktop.classList.remove("serv__itens_desktop");
	servItensDesktop.classList.remove("swiper");
	servWrapper.classList.remove("swiper-wrapper");
}

//
const services = document.querySelectorAll(".serv__item");
const servicesContent = document.querySelectorAll(".serv__content-item");
const servicesContentMobile = document.querySelectorAll(".serv__contentMobile");

services.forEach((service, index) => {
	service.addEventListener("click", () => {
		/*for (let i = 0; i < services.length; i++) {
      services[i].classList.remove("service-active");
      servicesContent[i].classList.remove("serviceContent-active");
    }*/
		services[index].classList.toggle("service-active");
		servicesContent[index].classList.toggle("serviceContent-active");
		servicesContentMobile[index].classList.toggle("serviceContent-active");
	});
});
//
const servContentNav = document.querySelectorAll(".serv__content__nav ul");

servContentNav.forEach((item) => {
	if (!item.firstElementChild) {
		item.parentElement.remove();
	}
});
