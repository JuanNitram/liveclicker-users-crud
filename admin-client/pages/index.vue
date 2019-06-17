<template>
  <div v-show="!loading">
    <dataTable :headers="headers"></dataTable>
  </div>
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
            // { text: '', align: 'middle', value: 'check', sortable: false},
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
      const user = JSON.parse(localStorage.getItem("user"));
    },
    methods:{
      
    },
    computed: {
      loading(){
        return this.$store.getters.loading
      },
    }
  }
</script>
<style>

</style>
