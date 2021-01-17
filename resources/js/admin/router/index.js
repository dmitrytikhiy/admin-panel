import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

const routes = [
    {
        path: '',
        component: () => import(/* webpackChunkName: "dashboard" */ "../modules/dashboard/Dashboard.vue")
    },
    {
        path: '/user',
        component: () => import(/* webpackChunkName: "user" */ "../modules/user/UserList.vue")
    },
];

const router = new VueRouter({
    mode: "history",
    routes
});

export default router;