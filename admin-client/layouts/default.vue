<template>
  <v-app dark>

    <v-toolbar v-show="this.$route.name != 'login'" dark color="primary">
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
export default {
  async created(){
    return new Promise((resolve, reject) => {
      this.$store.commit('SET_LOADING', true);

        const token = localStorage.getItem("token");

        if(token) {
          this.$axios.defaults.headers.common["Authorization"] = token;
        }

      this.$axios.get(process.env.apiUrl + "check").then(res => {
          this.$store.commit('SET_LOADING', false);
      }).catch(ex => {
          this.$router.push('/login')
      });
      resolve()
    })
  },
  methods: {
    logout(){
      this.$store.dispatch('logout').then(() => {
        console.log(this.$router);
        this.$router.push('/login')
      })
    }
  },
}
</script>
