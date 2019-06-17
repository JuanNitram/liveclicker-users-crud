<template>
    <dataTable v-if="auth" :headers="headers"/>
</template>

<script>
import dataTable from '../components/dataTable'
  export default {
    components: {
      dataTable,
    },
    data(){
      return {
        isLoading: false,
        headers: [
            { text: 'Id', align: 'left', value: 'id' , class:'clickable'},
            { text: '', align: 'middle', value: 'media', sortable: false},
            { text: 'Name', align:'left', value: 'name'  , class:'clickable'},
            { text: 'Surname', align:'left', value: 'surname'  , class:'clickable'},
            { text: 'Email', align:'left', value: 'email'  , class:'clickable'},
            { text: 'Activo', align:'left', value: 'active'  , class:'clickable'},
            { text: '', align:'middle', value: 'actions', sortable: false, width: '5%'},
        ],
      }
    },
    mounted(){
      if(!this.auth){
        this.$router.push('/login')
      }
    },
    methods:{
      
    },
    computed: {
      auth(){
        return this.$store.getters.auth
      }
    }
  }
</script>
<style>

</style>
