<template>
    <v-card>
        <v-card-title>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details/>
        </v-card-title>

        <v-data-table :headers="headers" :items="items" :search="search" disable-initial-sort>
            <template slot="items" slot-scope="props">
                <tr :key="props.item.id">

                    <td v-for="(header, i) in headers" :class="[headerLeft(header), header.value == 'check' ? 'pl-0' : '']" :key="i" >
                        <div v-if="header.value == 'media'">
                            <v-tooltip top>
                              <template v-slot:activator="{ on }">
                                  <div v-if="props.item.media.length" v-on="on">
                                      <img :src="props.item.media[0].full_url" height="24" width="24" />
                                  </div>
                                  <div v-else v-on="on">
                                      <img :src="storageUrl + 'thumb_default.png'" height="24" width="24" />
                                  </div>
                              </template>
                              <span>
                                  <div v-if="props.item.media.length">
                                      <img :src="props.item.media[0].full_url" height="96" width="96" />
                                  </div>
                                  <div v-else>
                                      <img :src="storageUrl + 'thumb_default.png'" height="96" width="96" />
                                  </div>
                              </span>
                            </v-tooltip>
                        </div>

                        <div v-else-if="header.value == 'active'">
                            <v-icon v-if="props.item.active">check_circle_outline</v-icon>
                            <v-icon v-else>highlight_off</v-icon>
                        </div>

                        <div v-else-if="header.value == 'actions'" class="actions">
                            <v-tooltip bottom>
                                <v-btn  color="primary" dark small icon flat slot="activator" @click="activeItem(props.item.id)">
                                    <v-icon small >
                                        done
                                    </v-icon>
                                </v-btn>
                                Active this
                            </v-tooltip>
                            <v-tooltip bottom>
                                <v-btn  color="primary" dark small icon flat slot="activator" @click="desactiveItem(props.item.id)">
                                    <v-icon small >
                                        close
                                    </v-icon>
                                </v-btn>
                                Desactive this
                            </v-tooltip>
                            <v-tooltip bottom>
                                <v-btn  color="primary" dark small icon flat slot="activator" @click="removeItem(props.item.id)">
                                    <v-icon small >
                                        delete
                                    </v-icon>
                                </v-btn>
                                Remove this
                            </v-tooltip>
                        </div>

                        <div v-else >
                            {{ props.item[header.value]}}
                        </div>
                    </td>
                </tr>
            </template>

            <v-alert slot="no-results" :value="true" color="error" icon="warning">
                Your search for "{{ search }}" found no results.
            </v-alert>
        </v-data-table>
    </v-card>
</template>

<script>
    const Cookie = require('js-cookie')

    export default {
        props: {
            headers: Array,
        },
        data(){
            return {
                search: '',
                items: [],
            }
        },
        created(){
            this.$axios.get(process.env.apiUrl + 'users',{ headers: { 'Authorization': this.auth.token }}).then((response) => {
                let res = response.data;
                if(res.success){
                    this.items = res.data['users'];
                }
            }).catch(err => {
                if(err.response.status == 401){
                    Cookie.remove('auth')
                    this.$store.commit('SET_AUTH', null)
                    this.$router.push('/login')
                }

            });
        },
        methods: {
            activeItem(id){
                this.$axios.put(process.env.apiUrl + 'users/toggle/' + id,{
                    toggle: 1
                },{ headers: { 'Authorization': this.auth.token }}).then((response) => {
                    let res = response.data;
                    if(res.success){
                        this.items.forEach((item) => {
                            if(item.id == id){
                                item.active = 1
                            }
                            
                        })
                    }
                });
            },
            desactiveItem(id){
                this.$axios.put(process.env.apiUrl + 'users/toggle/' + id,{
                    toggle: 0
                },{ headers: { 'Authorization': this.auth.token }}).then((response) => {
                    let res = response.data;
                    if(res.success){
                        this.items.forEach((item) => {
                            if(item.id == id){
                                item.active = 0
                            }
                            
                        })
                    }
                });
            },
            removeItem(id){
                this.$axios.delete(process.env.apiUrl + 'users/' + id,{ 
                    headers: { 'Authorization': this.auth.token }
                }).then((response) => {
                    let res = response.data;
                    let removed = false;
                    for(let i = 0; i < this.items.length && !removed; i++){
                        if(this.items[i].id == id){
                            removed = true;
                            this.items.splice(i, 1);
                        }
                    }
                });
            },
            headerLeft(header){
                if(header.value == 'id')
                    return 'text-xs-left'
                return ''
            },
        },
        computed:{
          auth(){
            return this.$store.getters.auth
          }
        }
    }

</script>

<style>
    .d-none-actions {
        opacity:0!important;
        -webkit-transition: opacity 350ms linear;
        transition: opacity 350ms linear;
    }
    .actions {
        display:flex;
    }
    .v-tooltip__content {
        padding: 5px 4px 0px 4px!important;
    }
</style>
