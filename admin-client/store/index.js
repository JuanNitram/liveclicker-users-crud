const cookieparser = require('cookieparser')

export const state = () => {
  return {
    auth: null
  }
}

export const mutations = {
  SET_AUTH(state, auth) {
    state.auth = auth
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
  auth: state => state.auth
}

