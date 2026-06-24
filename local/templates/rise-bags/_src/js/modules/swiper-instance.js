import Swiper from "swiper";
// import "swiper/css";

// // Import styles for specific modules if you use them
// import "swiper/css/navigation";
// import "swiper/css/pagination";
import { Navigation, Pagination, Thumbs } from "swiper/modules";
// Регистрируем нужные модули глобально
Swiper.use([Navigation, Pagination, Thumbs]);
// Экспортируем для внешнего использования
window.Swiper = Swiper;
