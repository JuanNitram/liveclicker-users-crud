const cookieparser = require('cookieparser')

export const state = () => {
  return {
    auth: null,
    loading: false,
  }
}

export const mutations = {
  SET_AUTH(state, auth) {
    state.auth = auth
  },
  SET_LOADING(state, val){
    state.loading = val
  },
  SET_USER(state, user){
    state.auth.user = user
  }
}

export const actions = {
  async nuxtServerInit({ commit }, {app ,req }) {
    let auth = null
    if (req.headers.cookie) {
      const parsed = cookieparser.parse(req.headers.cookie)
      try {
        auth = JSON.parse(parsed.auth)
        app.$axios.get(process.env.apiUrl + 'check',{ headers: { 'Authorization': auth.token }}).then((res) => {
          console.log(res.data);
        }).catch(err => {
          auth = null
        });
      } catch (err) {
        auth = null
      }
    }
    commit('SET_AUTH', auth)
  }
}

export const getters = {
  auth: state => state.auth,
  loading: state => state.loading
}


// import Vuex from 'vuex'

// const createStore = () => {
//   return new Vuex.Store({
//     state: {
//       loading: false,
//       user: {},
//       token: ''
//     },
//     mutations: {
//       AUTH_SUCCESS(state, data){
//         state.token = data.token
//         state.user = data.user
//       },
//       LOGOUT(state){
//         state.token = ''
//         state.user = {}
//       },
//       SET_LOADING(state, val){
//         state.loading = val
//       }
//     },
//     actions: {
//       login({commit}, user){
//         return new Promise((resolve, reject) => {
//           this.$axios({url: process.env.apiUrl + 'login', data: user, method: 'POST' }).then(resp => {
//             if(resp.data.success){
//               const token = resp.data.data.token
//               const user = resp.data.data.user

//               localStorage.setItem('token', token)
//               localStorage.setItem('user', JSON.stringify(user))
//               this.$axios.defaults.headers.common['Authorization'] = token
//               commit('AUTH_SUCCESS', {token: token, user:user})
//               resolve(resp)
//             } else {
//               resolve(resp)
//             }
//           })
//           .catch(err => {
//             localStorage.clear()
//             reject(err)
//           })
//         })
//       },
//       logout({commit}){
//         return new Promise((resolve, reject) => {
//           commit('LOGOUT')
//           localStorage.clear()
//           delete this.$axios.defaults.headers.common['Authorization']
//           resolve()
//         })
//       },
//     },
//     getters: {
//       loading: state => state.loading,
//       user: state => state.user
//     }
//   })
// }

// export default createStore
