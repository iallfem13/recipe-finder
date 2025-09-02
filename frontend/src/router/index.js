import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Detail from '../views/recipe/Detail.vue'
import Edit from '../views/recipe/Edit.vue'
import Results from '../views/recipe/Results.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/recipe/search',
      name: 'Search',
      component: Results,
      props: route => ({ 
        filters: route.query.filters ? JSON.parse(route.query.filters) : {}
      })
    },  
    {
      path: '/recipe/new',
      name: 'Edit',
      component: Edit
    },
    {
      path: '/recipe/:slug',
      name: 'Detail',
      component: Detail,
      props: true
    },
  ]
})

export default router
