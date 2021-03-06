<template>
    <div class="component-wrap">
        <!-- search -->
        <v-card dark class="pt-3">
            <v-layout row wrap>
                <v-flex xs12 sm4 class="px-2">
                    <v-btn @click="showDialog('brands_form')" class="blue lighten-1" dark>
                        Add Brand
                        <v-icon right dark>add</v-icon>
                    </v-btn>
                </v-flex>

                <v-flex xs12 class="my-2"><v-divider></v-divider></v-flex>
                <v-flex xs12 sm4 class="px-2">
                    <v-text-field prepend-icon="search" dark label="Filter By Name" v-model="filters.name"></v-text-field>
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


                <span >{{ props.header.text }}</span>


            </template>
            <template slot="items" slot-scope="props">
                <td>
                    <v-menu>
                        <v-btn icon slot="activator" dark>
                            <v-icon>more_vert</v-icon>
                        </v-btn>
                        <v-list>
                            <v-list-tile @click="$router.push({name:'brands.edit',params:{id: props.item.id}})">
                                <v-list-tile-title>Edit</v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile @click="trash(props.item)">
                                <v-list-tile-title>Delete</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </td>
                <td>{{ props.item.name }}</td>

                <td>
                    <v-avatar outline>
                        <v-icon v-if="props.item.status==1" class="green--text">check_circle</v-icon>
                        <v-icon class="grey--text" v-else>error_outline</v-icon>
                    </v-avatar>
                </td>
            </template>
        </v-data-table>




        <!-- dialog for show showBrandsForm -->
        <v-dialog v-model="dialogs.showBrandsForm.show" lazy absolute max-width="600px">
            <v-card>
                <v-form v-model="valid" ref="brandForm" lazy-validation>
                <v-card-title>
                    <div class="headline"> Add Brand</div>
                </v-card-title>
                <v-card-text>
                    <v-layout row wrap>
                        <v-flex xs12 sm12>
                            <v-text-field box dark label="Brand Name" v-model="name" :rules="nameRules"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12>
                            <v-select
                                    label="Brand Status"
                                    v-bind:items="options.brandStatus"
                                    v-model="selectedBrandStatus"
                                    dark
                                    item-text="label"
                                    item-value="value"
                            ></v-select>
                        </v-flex>

                        <v-flex xs12>
                            <v-btn @click="saveBrands()"  color="primary" dark>Save Changes</v-btn>
                        </v-flex>

                    </v-layout>
                </v-card-text>
                </v-form>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    export default {

        data () {
            return {
                valid: false,
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'Name', value: 'name', align: 'left', sortable: false },
                    { text: 'Status', value: 'active', align: 'left', sortable: false },

                ],
                items: [],
                totalItems: 0,
                pagination: {
                    rowsPerPage: 10
                },

                filters: {
                    name: '',
                    status: '',
                },

                dialogs: {
                    showBrandsForm: {
                        items: [],
                        show: false
                    }
                },

                name: '',
                nameRules: [
                    (v) => !!v || 'Name is required',
                ],
                selectedBrandStatus: {},
                brandStatus: 1,
                options: {
                    status: [],
                    brandStatus:[
                        {label:'Active', value:1},
                        {label:'In Active', value:2},
                    ],
                },
            }
        },
        mounted() {
            const self = this;

            self.$eventBus.$on(['USER_ADDED','USER_UPDATED','USER_DELETED','GROUP_ADDED'],()=>{
                self.loadBrands(()=>{});
            });

            self.$store.commit('setBreadcrumbs',[
                {label:'Brands',name:''},
            ]);
        },
        watch: {
            'pagination.page':function(){
                this.loadBrands(()=>{});
            },
            'pagination.rowsPerPage':function(){
                this.loadBrands(()=>{});
            },
            'filters.name':_.debounce(function(){
                const self = this;
                self.loadBrands(()=>{});
            },700),

        },
        methods: {
            trash(brand) {
                const self = this;
                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: "Confirm Deletion",
                    message: "Are you sure you want to delete this user?",
                    okCb: ()=>{

                        axios.delete('/admin/brands/' + brand.id).then(function(response) {

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
            showDialog(dialog) {

                const self = this;

                switch (dialog){
                    case 'brands_form':
                        setTimeout(()=>{
                            self.dialogs.showBrandsForm.show = true;
                        },500);
                    break;
                }
            },
            loadBrands(cb) {

                const self = this;

                let params = {
                    name: self.filters.name,
                    status: self.filters.status,
                    page: self.pagination.page,
                    per_page: self.pagination.rowsPerPage
                };

                axios.get('/admin/brands',{params: params}).then(function(response) {
                    self.items = response.data.data.data;
                    self.totalItems = response.data.data.total;
                    self.pagination.totalItems = response.data.data.total;
                    (cb || Function)();
                });
            },

            saveBrands() {
                const self = this;

                let payload = {
                    name: self.name,
                    status: self.selectedBrandStatus,

                };

                self.$store.commit('showLoader');

                axios.post('/admin/brands',payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                       message: response.data.message,
                       color: 'success',
                       duration: 3000
                    });

                    self.$eventBus.$emit('USER_UPDATED');
                    self.$store.commit('hideLoader');

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
            }

        }
    }
</script>
