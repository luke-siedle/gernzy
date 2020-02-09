import Vuex from 'vuex'
import session from './session'

export default () => new Vuex.Store({
  modules: {
    session
  }
})
