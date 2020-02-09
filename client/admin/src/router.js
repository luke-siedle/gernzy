import routes from './routes'
import VueRouter from "vue-router"
export default ( App ) => new VueRouter({
  mode: "abstract",
  routes,
  components: [App]
})
