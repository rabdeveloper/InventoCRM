<template>
    <div>
        <v-card dark>
            <v-card-title>
                <i aria-hidden="true" class="icon fas fa-tags"></i> Edit Category
            </v-card-title>
            <v-divider class="mb-2"></v-divider>
            <v-form v-model="valid" ref="brandFormEdit" lazy-validation>
                <v-container grid-list-md>
                <v-layout row wrap>

                    <v-flex xs12 sm12>
                        <v-text-field box dark label="Category Name" v-model="name" :rules="nameRules" sm6></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm12>
                        <v-select
                                label="Category Status"
                                v-bind:items="options.categoryStatus"
                                v-model="selectedCategory"
                                dark
                                item-text="label"
                                item-value="value"
                                sm6
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
            propCategoryId: {
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

                selectedCategory: {},

                options: {
                    status: [],
                    categoryStatus:[
                        {label:'Active', value:1},
                        {label:'In Active', value:2},
                    ],
                },
            }
        },
        mounted() {
            this.loadCategory();
        },
        watch: {


        },
        methods: {
            save() {

                const self = this;

                let payload = {
                    name: self.name,
                    status: self.selectedCategory,
                };

                self.isLoading = true;

                axios.put('/admin/categories/' + self.propCategoryId,payload).then(function(response) {

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
            loadCategory(cb) {

                const self = this;

                axios.get('/admin/categories/' + self.propCategoryId).then(function(response) {

                    let category = response.data.data.category;

                    self.name = category.name;

                    selectedCategory: category.status;

                    self.$store.commit('setBreadcrumbs',[
                        {label:'Category',name:'categories.list'},
                        {label:'Edit',name:'category.name'},

                    ]);

                    //cb();
                });
            }
        }
    }
</script>
