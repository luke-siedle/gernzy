import Vue from "vue"
import App from "./components/App.vue"
import VueRouter from "vue-router"
import Vuex from "vuex"
import createRouter from './router'
import '@/assets/scss/app.scss'
import createStore from './store/store'
import { createProvider } from './vue-apollo'

// Use 3rd party libraries
Vue.use(VueRouter)
Vue.use(Vuex)

// Configure development tools
Vue.config.devtools = process.env.NODE_ENV === 'development'

// Initialize router with first path
const router = createRouter()
router.push({ path: "/login" })

// Initialize the store
const store = createStore();

// Instantiate and render the app
const app = new Vue({
  el: '#app',
  router,
  render: ce => ce(App),
  apolloProvider: createProvider(),
  store
})

// Configure development tools
window.__VUE_DEVTOOLS_GLOBAL_HOOK__.Vue = app.constructor;
