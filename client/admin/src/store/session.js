export default {
  state: {
    name: null,
    email: null,
    has_active_session: false
  },

  mutations: {
    logIn( state ){
      state.has_active_session = true
    },
    clearSession( state ){
      state.has_active_session = false
      state.token = null;
      state.user = null;
    }
  },
  actions: {
    logIn( { commit }, { errors, data } ){
      if( !errors && data.logIn.user ){
        const { user, token } = data.logIn;
        commit('logIn', {
          user,
          token
        });
        return {
          success: true,
          error: null
        }
      }

      return {
        success: false,
        error: {
          msg: errors ? errors[0].message : 'Unknown failure',
          code: '403'
        }
      }
    },
    clearSession( {commit} ){
      commit('clearSession');
    }
  },
  getters: { }
}
