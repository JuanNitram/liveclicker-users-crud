import Vuex from 'vuex'

const createStore = () => {
  return new Vuex.Store({
    state: {
      loading: false,
    },
    mutations: {
      AUTH_SUCCESS(state, data){
        state.token = data.token
        state.user = data.user
      },
      LOGOUT(state){
        state.token = ''
        state.user = {}
      },
      SET_LOADING(state, val){
        state.loading = val
      }
    },
    actions: {
      login({commit}, user){
        return new Promise((resolve, reject) => {
          this.$axios({url: 'http://localhost/liveclicker-users-crud/api/public/api/page/' + 'login', data: user, method: 'POST' }).then(resp => {
            if(resp.data.success){
              const token = resp.data.data.token
              const user = resp.data.data.admin

              localStorage.setItem('token', token)
              localStorage.setItem('user', JSON.stringify(user))
              this.$axios.defaults.headers.common['Authorization'] = token
              commit('AUTH_SUCCESS', {token: token, user:user})
              console.log("STORE OK");
              resolve(resp)
            } else {
              resolve(resp)
            }
          })
          .catch(err => {
            localStorage.clear()
            reject(err)
          })
        })
      },
      logout({commit}){
        return new Promise((resolve, reject) => {
          commit('LOGOUT')
          localStorage.clear()
          delete axios.defaults.headers.common['Authorization']
          resolve()
        })
      }
    },
    getters: {

    }
  })
}

export default createStore
