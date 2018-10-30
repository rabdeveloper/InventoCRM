import Vue from 'vue';
import VueRouter from 'vue-router';
import store from './common/Store';

Vue.use(VueRouter);

const router = new VueRouter({
    mode:'history',
    routes: [
        {
            path: '/',
            redirect: '/dashboard',
        },
        {
            name: 'dashboard',
            path: '/dashboard',
            component: require('./components/dashboard/Home'),
        }
    ],
});

router.beforeEach((to, from, next) => {
    store.commit('showLoader');
    next();
});

router.afterEach((to, from) => {
    setTimeout(()=>{
        store.commit('hideLoader');
    },1000);
});

export default router;
