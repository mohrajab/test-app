/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import axios from 'axios';
import {routes} from './routes';
import VueFlashMessage from 'vue-flash-message';

Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.use(VueFlashMessage);

const router = new VueRouter({
    mode: 'history',
    routes: routes
});

Vue.component('product-component', require('./components/Product/Products').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router: router,
});
