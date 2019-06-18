<template>
  <v-layout row justify-center pt-5>
    <v-flex xs12 sm8 md6>

      <v-form ref="form" lazy-validation>
        <v-text-field v-model="email" :rules="[rules.required]" label="E-mail" required outline></v-text-field>

        <v-text-field v-model="password" :rules="[rules.required, rules.min]" label="Password" type="password" required outline></v-text-field>

        <div class="text-xs-right">
            <v-btn depressed large @click="login">
                <span v-show="!isLoading">Login</span>
                <v-progress-circular v-show="isLoading" class="progress-fix" size="20" indeterminate/>
            </v-btn>
        </div>
      </v-form>

    </v-flex>
  </v-layout>
</template>

<script>
const Cookie = require('js-cookie')

export default {
    data(){
      return {
        email: '',
        password: '',
        isLoading: false,
        rules: {
            required: value => !!value || 'Required',
            min: pass => pass.length >= 8 || 'Min 8 characters',
        }
      }
    },
    methods: {
      login: function () {
          let isValid = this.$refs.form.validate();
          if(isValid){
              this.isLoading = true;
              let email = this.email
              let password = this.password
              this.$axios.post(process.env.apiUrl + 'login', {
                email, 
                password 
              }).then((res) => {
                this.isLoading = false
                if(res.data.success){
                    let auth = res.data.data
                    this.$store.commit('SET_AUTH', auth);
                    Cookie.set('auth', auth);
                    this.$router.push('/')
                } else {
                  this.$toast.show(res.data.message, {
                    theme: "bubble",
                    type: 'error',
                    position: "top-right",
                    duration : 5000
                  });
                }
                this.loading = false;
              }).catch(err => {
                this.loading = false;
                this.$toast.show("An error was ocurred.", {
                  theme: "bubble",
                  type: 'error',
                  position: "top-right",
                  duration : 5000
                });
              })
          }
      }
    }

}
</script>
