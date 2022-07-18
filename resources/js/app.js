import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'
import Permissions from './mixins/Permissions';
import { Form, HasError, AlertError } from 'vform';
import moment from 'moment';
import '~/plugins'
import '~/components'
import VueProgressBar from 'vue-progressbar'
import swal from 'sweetalert2'
import Echo from 'laravel-echo'
window.io = require('socket.io-client');

window.Echo = new Echo({
  broadcaster: 'socket.io',
  host: window.location.hostname + ':6001'
});

/*import Echo from 'laravel-echo'

window.Pusher = require('pusher-js')

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  wsHost: window.location.hostname,
  wsPort: 6001,
  disableStats: true
})*/






Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '3px'
})

Vue.mixin(Permissions);

//Globalne varijable
Vue.prototype.$pageOptions = [50, 100, 150,200, { value: 500, text: "Prikazi sve" }]


import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)

window.Form = Form;
window.Fire=new Vue();
window.axios=require('axios')
window.moment=moment;
window.swal=swal;
window._ = require('lodash');

const toast = swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});

window.toast = toast;


Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)
Vue.component('pulse-loader', require('vue-spinner/src/PulseLoader.vue'));



Vue.config.productionTip = false

/* eslint-disable no-new */

new Vue({
  i18n,
  store,
  router,
  ...App
})
