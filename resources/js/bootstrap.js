import axios from 'axios';
window.axios = axios;

// esto deja axios ya listo para que el resto del JS no tenga que repetir la misma cabecera una y otra vez.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
