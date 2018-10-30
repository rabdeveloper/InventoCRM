<template>
    <div>
        <v-card dark>
            <v-card-title>
                <i aria-hidden="true" class="icon fas fa-tags"></i> Edit Brand
            </v-card-title>
            <v-divider class="mb-2"></v-divider>
            <v-form v-model="valid" ref="brandFormEdit" lazy-validation>
                <v-container grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12 sm6>
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
                        <v-btn @click="save()" :disabled="!valid" color="primary" dark>Update</v-btn>
                    </v-flex>
                </v-layout>
            </v-container>
            </v-form>
        </v-card>
    </div>
</template>

<script>
    export default {
        props: {
            propBrandId: {
                required: true
            }
        },
        data() {
            return {
                valid: false,
                isLoading: false,
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
            this.loadBrand();
        },
        watch: {


        },
        methods: {
            save() {

                const self = this;

                let payload = {
                    name: self.name,

                    status: self.selectedBrandStatus,
                };

                self.isLoading = true;

                axios.put('/admin/brands/' + self.propBrandId,payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                        message: response.data.message,
                        color: 'success',
                        duration: 3000
                    });

                    // reset

                    self.isLoading = false;

                }).catch(function (error) {

                    self.isLoading = false;
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
            loadBrand(cb) {

                const self = this;

                axios.get('/admin/brands/' + self.propBrandId).then(function(response) {

                    let brand = response.data.data;

                    self.name = brand.name;

                    selectedBrandStatus: brand.status;

                    self.$store.commit('setBreadcrumbs',[
                        {label:'Brands',name:'brands.list'},
                        {label:'Edit',name:''},

                    ]);

                    cb();
                });
            }
        }
    }
</script>
