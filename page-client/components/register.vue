<template>
  <v-form ref="form" lazy-validation>
    <v-layout row>
        <v-container class="d-flex" justify-center>
          <v-btn color="primary" @click="$refs.inputUpload.click()">
            <div v-if="imgName == ''">
              Profile image
              <v-icon right dark>cloud_upload</v-icon>
            </div>
            <div v-else>
              {{ imgName }}
              <v-icon right dark>cloud_upload</v-icon>
            </div>
          </v-btn>
        </v-container>
      </v-layout>
      <input v-show="false" ref="inputUpload" type="file" @change="handleFile" >

      <v-text-field class="text-field-pad" v-model="name" :rules="[rules.required]" label="Name" required outline></v-text-field>
      <v-text-field class="text-field-pad" v-model="surname" :rules="[rules.required]" label="Surname" required outline></v-text-field>
      <v-text-field class="text-field-pad" v-model="email" :rules="[rules.required]" label="E-mail" required outline></v-text-field>
      <v-text-field class="text-field-pad" v-model="password" :rules="[rules.required, rules.min]" label="Password" required outline></v-text-field>
      <v-text-field class="text-field-pad" v-model="c_password" :rules="[rules.required, rules.min]" label="Confirm Password" required outline></v-text-field>

    <div class="text-xs-right">
        <v-btn depressed large @click="signUp">
            <span v-show="!isLoading">Sign up</span>
            <v-progress-circular v-show="isLoading" class="progress-fix" size="20" indeterminate/>
        </v-btn>
    </div>
    <v-alert v-show="success" :value="true" type="success">
      Please wait until the administrator active your account!
    </v-alert>
  </v-form>
</template>

<script>
  export default {
    data(){
      return {
        name: '',
        surname: '',
        email: '',
        password: '',
        c_password: '',
        profile_image: null,
        imgName: '',
        isLoading: false,
        success: false,
        rules: {
            required: value => !!value || 'Required',
            min: pass => pass.length >= 8 || 'Min 8 characters',
        },
      }
    },
    methods: {
      signUp(){
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

              this.$axios.post(process.env.apiUrl + 'register', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
              }).then((res) => {
                  if(res.data.success){
                    const user = res.data.data.user
                    localStorage.setItem('user', JSON.stringify(user))
                    this.success = true;
                    setTimeout(()=>{
                      this.success = false
                      this.$nuxt.$emit('disable-register-form');
                      this.name = ''
                      this.surname = ''
                      this.email = ''
                      this.password = ''
                      this.c_password = ''
                      this.profile_image = null
                    }, 5000);
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
                  this.$toast.show("An error was ocurred.", {
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
        console.log(this.profile_image)
        this.imgName = event.target.files[0].name.substring(0, 30)
      },
    }
  }
</script>
