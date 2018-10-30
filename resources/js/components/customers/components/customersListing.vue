<template>
    <div class="component-wrap">

        <!-- search -->
        <v-card dark class="pt-3">
            <v-layout row wrap>
                <v-flex xs12 sm4 class="px-2">
                    <v-btn @click="$router.push({name:'customers.create'})" class="blue lighten-1" dark>
                        Add Customer
                        <v-icon right dark>add</v-icon>
                    </v-btn>
                </v-flex>

                <v-flex xs12 class="my-2"><v-divider></v-divider></v-flex>
                <v-flex xs12 sm4 class="px-2">
                    <v-text-field prepend-icon="search" dark label="Filter By Name" v-model="filters.name"></v-text-field>
                </v-flex>
                <v-flex xs12 sm4 class="px-2">
                    <v-text-field prepend-icon="search" dark label="Filter By Email" v-model="filters.email"></v-text-field>
                </v-flex>

            </v-layout>
        </v-card>
        <!-- /search -->

        <!-- data table -->
        <v-data-table
                v-bind:headers="headers"
                v-bind:pagination.sync="pagination"
                :items="items"
                :total-items="totalItems"
                class="elevation-1">
            <template slot="headerCell" slot-scope="props">
                <span v-if="props.header.value=='name'">
                    <v-icon>person</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='email'">
                    <v-icon>email</v-icon> {{ props.header.text }}
                </span>

                <span v-else>{{ props.header.text }}</span>
            </template>
            <template slot="items" slot-scope="props">
                <td>
                    <v-menu>
                        <v-btn icon slot="activator" dark>
                            <v-icon>more_vert</v-icon>
                        </v-btn>
                        <v-list>
                            <v-list-tile @click="$router.push({name:'customers.edit',params:{id: props.item.id}})">
                                <v-list-tile-title>Edit</v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile @click="trash(props.item)">
                                <v-list-tile-title>Delete</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </td>
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.email }}</td>
                <td>
                    <v-btn small @click="showDialog('user_permissions',props.item.permissions)" outline round color="grey" dark>Show</v-btn>
                </td>
                <td>
                    <v-chip v-for="group in props.item.groups" :key="group.id" outline color="grey" text-color="grey">
                        {{group.name}}
                    </v-chip>
                </td>
                <td>{{ $appFormatters.formatDate(props.item.last_login) }}</td>
                <td>
                    <v-avatar outline>
                        <v-icon v-if="props.item.active!=null" class="green--text">check_circle</v-icon>
                        <v-icon class="grey--text" v-else>error_outline</v-icon>
                    </v-avatar>
                </td>
            </template>
        </v-data-table>



    </div>
</template>

<script>
    export default {
        data () {
            return {
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'Name', value: 'name', align: 'left', sortable: false },
                    { text: 'Company', value: 'company', align: 'left', sortable: false },
                    { text: 'Email', value: 'email', align: 'left', sortable: false },
                    { text: 'Work Phone', value: 'email', align: 'left', sortable: false },
                    { text: 'Receivables', value: 'email', align: 'left', sortable: false },
                    { text: 'Payables', value: 'email', align: 'left', sortable: false },

                ],
                items: [],
                totalItems: 0,
                pagination: {
                    rowsPerPage: 10
                },

                filters: {
                    name: '',
                    email: '',

                },

                dialogs: {
                    showPermissions: {
                        items: [],
                        show: false
                    }
                }
            }
        },
        mounted() {
            const self = this;

            self.$eventBus.$on(['USER_ADDED','USER_UPDATED','USER_DELETED','GROUP_ADDED'],()=>{
                self.loadUsers(()=>{});
            });

            self.$store.commit('setBreadcrumbs',[
                {label:'Customers',name:''},
            ]);
        },
        watch: {
            'pagination.page':function(){
                this.loadUsers(()=>{});
            },
            'pagination.rowsPerPage':function(){
                this.loadUsers(()=>{});
            },
            'filters.name':_.debounce(function(){
                const self = this;
                self.loadUsers(()=>{});
            },700),
            'filters.email':_.debounce(function(){
                const self = this;
                self.loadUsers(()=>{});
            },700),
            'filters.groupId':_.debounce(function(){
                const self = this;
                self.loadUsers(()=>{});
            },700)
        },
        methods: {
            trash(user) {
                const self = this;

                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: "Confirm Deletion",
                    message: "Are you sure you want to delete this user?",
                    okCb: ()=>{

                        axios.delete('/admin/customers/' + user.id).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.$eventBus.$emit('USER_DELETED');

                        }).catch(function (error) {

                            self.$store.commit('hideLoader');

                            if (error.response) {
                                self.$store.commit('showSnackbar',{
                                    message: error.response.data.message,
                                    color: 'error',
                                    duration: 3000
                                });
                            } else if (error.request) {
                                console.log(error.request);
                            } else {
                                console.log('Error', error.message);
                            }
                        });
                    },
                    cancelCb: ()=>{
                        console.log("CANCEL");
                    }
                });
            },
            showDialog(dialog, data) {

                const self = this;


            },
            loadUsers(cb) {

                const self = this;

                let params = {
                    name: self.filters.name,
                    email: self.filters.email,
                    company: self.filters.company,
                    page: self.pagination.page,
                    per_page: self.pagination.rowsPerPage
                };

                axios.get('/admin/customers',{params: params}).then(function(response) {
                    self.items = response.data.data.data;
                    self.totalItems = response.data.data.total;
                    self.pagination.totalItems = response.data.data.total;
                    (cb || Function)();
                });
            },

        }
    }
</script>
