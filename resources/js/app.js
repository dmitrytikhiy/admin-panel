import Vue from 'vue';
import VueRouter from 'vue-router';
import router from './admin/router';

import App from './admin/App.vue';

Vue.use(VueRouter)

const app = new Vue({
    router,
    render: h => h(App)
});

app.$mount("#app");