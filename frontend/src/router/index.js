import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: () => import('../views/LoginView.vue'),
        },
        {
            path: '/',
            name: 'dashboard',
            component: () => import('../views/DashboardView.vue'),
            meta:{requiresAuth: true}
        },
        {
            path: '/order/place',
            name: 'place-order',
            component: () => import('../views/OrderFormView.vue'),
            meta:{requiresAuth: true}
        },
        {
            path: '/:pathMatch(.*)*',
            redirect: '/login'
        }
    ],
})

router.beforeEach((to) => {
    const token = localStorage.getItem('token')
    if (to.meta.requiresAuth && !token) {
        return { name: 'login' }
    }
    if (to.name === 'login' && token) {
        return { name: 'dashboard' }
    }
})

export default router