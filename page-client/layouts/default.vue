<template>
  <v-app dark>

    <v-toolbar v-show="this.$route.name != 'login' && this.$store.state.auth" dark color="primary">
      <v-toolbar-title class="white--text">Liveclicker</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-tooltip bottom>
        <template v-slot:activator="{ on }">
            <div v-on="on">
                <!-- <img :src="props.item.media[0].full_url" height="24" width="24" /> -->
              <v-btn icon @click="dialog = true">
                <v-icon>close</v-icon>
              </v-btn>
            </div>
        </template>
        <span>
            Delete User
        </span>
      </v-tooltip>
      <v-btn icon @click="logout">
        <v-icon>logout</v-icon>
      </v-btn>
    </v-toolbar>

    <v-content>
      <v-container>
        <nuxt />
      </v-container>
    </v-content>

    <v-dialog
      v-model="dialog"
      width="500"
    >
      <v-card>
        <!-- <v-card-title
          class="headline grey lighten-2"
          primary-title
        >
          Delete Account ?
        </v-card-title> -->

        <v-card-text justify-center align-center>
          <h3>You are about to delete your account. Please confirm your password and submit!</h3>
          <v-flex mt-3>
            <v-form ref="form" lazy-validation>

              <v-text-field v-model="password" :rules="[rules.required, rules.min]" label="Password" type="password" required outline></v-text-field>

            </v-form>
          </v-flex>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" flat @click="deleteUser">
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-app>
</template>

<script>
const Cookie = require('js-cookie')

export default {
  data(){
    return {
      dialog: false,
      password: '',
      rules: {
          required: value => !!value || 'Required',
          min: pass => pass.length >= 8 || 'Min 8 characters',
      },
    }
  },
  methods: {
    logout() {
      Cookie.remove('auth')
      this.$store.commit('SET_AUTH', null)
      this.$router.push('/login')
    },
    deleteUser(){
      this.$axios.post(process.env.apiUrl + 'delete/' + this.auth.user.id, { user: this.auth.user, password: this.password }, {
          headers: { 'Authorization': this.auth.token }
      }).then((res) => {
          if(res.data.success){
            Cookie.remove('auth')
            this.$store.commit('SET_AUTH', null)
            this.$router.push('/login')
          }
          this.dialog = false
          this.$toast.show(res.data.message, {
            theme: "bubble",
            type: 'success',
            position: "top-right",
            duration : 5000
          });
      }).catch(err => {
        this.dialog = false
        this.$toast.show('An error was ocurred.', {
          theme: "bubble",
          type: 'error',
          position: "top-right",
          duration : 5000
        });
      });
    }
  },
  computed: {
    auth(){
      return this.$store.getters.auth
    }
  }
}
</script>
