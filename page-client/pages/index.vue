<template>
  <div v-show="auth">
    <v-layout justify-center align-center>
      <v-flex xs12 sm8 md6 pt-5>
          <v-form ref="form" lazy-validation>
            <v-layout row>
            <v-container class="d-flex" justify-center>
              <v-img
                :src="imgUrl"
                :lazy-src="imgUrl"
                class="grey lighten-2"
                height="250"
                max-width="250"
              >
                <template v-slot:placeholder>
                  <v-layout fill-height align-center justify-center ma-0>
                    <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                  </v-layout>
                </template>
              </v-img>

            </v-container>
            </v-layout>
            <v-layout row>
              <v-container class="d-flex" justify-center style="max-width: 250px">
                <v-btn color="primary" @click="$refs.inputUpload.click()">
                  Profile image
                  <v-icon right dark>cloud_upload</v-icon>
                </v-btn>
              </v-container>
            </v-layout>
            <input v-show="false" ref="inputUpload" type="file" @change="handleFile" >

            <v-text-field class="text-field-pad" v-model="name" :rules="[rules.required]" label="Name" required outline></v-text-field>
            <v-text-field class="text-field-pad" v-model="surname" :rules="[rules.required]" label="Surname" required outline></v-text-field>
            <v-text-field class="text-field-pad" v-model="email" :rules="[rules.required]" label="E-mail" required outline readonly></v-text-field>
            <v-text-field class="text-field-pad" v-model="password" :rules="[rules.required, rules.min]" label="Password" type="password" required outline></v-text-field>
            <v-text-field class="text-field-pad" v-model="c_password" :rules="[rules.required, rules.min]" label="Confirm Password" type="password" required outline></v-text-field>
            <div class="text-xs-right">
                <v-btn depressed large @click="saveChanges">
                    <span v-show="!isLoading">Save Changes</span>
                    <v-progress-circular v-show="isLoading" class="progress-fix" size="20" indeterminate/>
                </v-btn>
            </div>
          </v-form>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
  const Cookie = require('js-cookie')

  export default {
    data(){
      return {
        name: '',
        surname: '',
        email: '',
        password: '',
        c_password: '',
        imgUrl: '',
        profile_image: null,
        isLoading: false,
        rules: {
            required: value => !!value || 'Required',
            min: pass => pass.length >= 8 || 'Min 8 characters',
        }
      }
    },
    mounted(){
      if(!this.auth){
        this.$router.push('/login')
      }

      if(this.auth.user){
        this.name = this.auth.user.name
        this.surname = this.auth.user.surname
        this.email = this.auth.user.email
        this.imgUrl = this.auth.user.media[0] ? this.auth.user.media[0].full_url : ''
      }
    },
    methods:{
      saveChanges(){
        let isValid = this.$refs.form.validate();
          if(isValid){
              this.isLoading = true;

              let formData = new FormData();

              formData.append('name', this.name);
              formData.append('surname', this.surname);
              formData.append('email', this.email);
              formData.append('password', this.password);
              formData.append('c_password', this.c_password);
              if(this.profile_image) formData.append('profile_image', this.profile_image);

              this.$axios.post(process.env.apiUrl + 'update', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': this.auth.token
                }
              }).then((res) => {
                if(res.data.success){
                  const user = res.data.data.user
                  this.$store.commit('SET_USER', user)
                  Cookie.remove('auth')
                  Cookie.set('auth', {token:this.auth.token, user:user})
                  this.imgUrl = user.media[0].full_url
                  this.$toast.show(res.data.message, {
                    theme: "bubble",
                    type: 'success',
                    position: "top-right",
                    duration : 5000
                  });
                } else {
                  this.$toast.show(res.data.message, {
                    theme: "bubble",
                    type: 'error',
                    position: "top-right",
                    duration : 5000
                  });
                }
                this.isLoading = false;
              }).catch(err => {
                  console.log(err)
                  this.$toast.show('An error was ocurred', {
                    theme: "bubble",
                    type: 'error',
                    position: "top-right",
                    duration : 5000
                  });
                  this.isLoading = false;
              })
          }
      },
      handleFile(event){
        this.profile_image = event.target.files[0]
        this.imgUrl = URL.createObjectURL(this.profile_image)
      },
    },
    computed: {
      loading(){
        return this.$store.getters.loading
      },
      auth(){
        return this.$store.getters.auth
      }
    }
  }
</script>
<style>
  .text-field-pad{
    padding-left: 5px;
    padding-right: 5px;
  }
</style>
