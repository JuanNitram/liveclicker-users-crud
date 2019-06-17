<template>
  <v-app dark>

    <v-toolbar v-show="this.$route.name != 'login' && this.$store.state.auth" dark color="primary">
      <v-toolbar-title class="white--text">Liveclicker</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon @click="logout">
        <v-icon>logout</v-icon>
      </v-btn>
    </v-toolbar>

    <v-content>
      <v-container>
        <nuxt />
      </v-container>
    </v-content>

  </v-app>
</template>

<script>
const Cookie = require('js-cookie')

export default {
  methods: {
    logout() {
      Cookie.remove('auth')
      this.$store.commit('SET_AUTH', null)
      this.$router.push('/login')
    }
  }
}
</script>
