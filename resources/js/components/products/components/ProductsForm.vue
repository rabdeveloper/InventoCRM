<template>
    <div>
        <v-form v-model="valid" ref="userFormAdd" lazy-validation>
        <v-card dark>
            <v-card-title>
                Add Product
            </v-card-title>
            <v-divider></v-divider>

            <v-container grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12 sm6>
                        <v-flex xs12 sm12>
                            <v-text-field box dark label="Product Name" v-model="product_name" :rules="requiredRules"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12>
                            <v-text-field box dark label="SKU" v-model="part_number" :rules="requiredRules"></v-text-field>
                        </v-flex>


                        <v-flex xs12 sm12>
                            <v-text-field box dark label="Quantity" v-model="starting_inventory" :rules="requiredRules"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm12>
                            <v-text-field box multi-line dark label="Description" v-model="product_label" :rules="requiredRules"></v-text-field>
                        </v-flex>
                    </v-flex>

                    <v-flex xs12 sm6>
                        <v-flex xs12 class="text-xs-center text-sm-center text-md-center text-lg-center">
                            <img :src="imageUrl" height="150" v-if="imageUrl"/>
                            <v-text-field label="Select Image" @click='pickFile' v-model='imageName' prepend-icon='attach_file'></v-text-field>
                            <input
                                type="file"
                                style="display: none"
                                ref="image"
                                accept="image/*"
                                @change="onFilePicked"
                            >
                        </v-flex>
                    </v-flex>
                    <v-flex xs12><v-spacer></v-spacer></v-flex>
                </v-layout>
            </v-container>

        </v-card>
        <v-card dark>
            <v-card-title>
                Sales Information
            </v-card-title>
            <v-divider></v-divider>

                <v-container grid-list-md>
                    <v-layout row wrap>

                        <v-flex xs12><v-spacer></v-spacer></v-flex>


                        <v-flex xs6>
                            <v-flex xs12>
                                <v-select
                                    label="Select Brands"
                                    v-bind:items="options.brands"
                                    v-model="selectedBrand"
                                    dark
                                    item-text="value"
                                    item-value="label"
                                ></v-select>

                            </v-flex>

                            <v-flex xs12 sm12>
                                <v-text-field box dark label="MPN (Manufacturing Part Number)" v-model="mpn" ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm12>
                                <v-text-field box dark label="ISBN (Internation Standard Book Number)" v-model="isbn" ></v-text-field>
                            </v-flex>
                        </v-flex>

                        <v-flex xs6>
                            <v-flex xs12>
                                <v-select
                                    label="Manufacturer"
                                    v-bind:items="options.manufacturers"
                                    v-model="selectedManu"
                                    dark
                                    item-text="title"
                                    item-value="key"
                                ></v-select>

                            </v-flex>
                            <v-flex xs12 sm12>
                                <v-text-field box dark label="UPC (Universal Product Code)" v-model="upc" ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm12>
                                <v-text-field box dark label="EAN (Internation Article Number)" v-model="ean" ></v-text-field>
                            </v-flex>
                        </v-flex>




                        <v-flex xs12>
                            <v-btn @click="save()" :disabled="!valid" color="primary" dark>Save</v-btn>
                        </v-flex>
                    </v-layout>

                    <v-flex xs12 class="my-2"><v-divider></v-divider></v-flex>
                    <v-layout row wrap>
                    </v-layout>

                    <v-flex xs12 class="my-2"><v-divider></v-divider></v-flex>
                    <v-layout row wrap>
                    </v-layout>
                </v-container>

        </v-card>

        </v-form>
    </div>



</template>
<script>
    export default {
        data() {

            const self = this;

            return {
                valid: false,
                product_name: '',
                part_number: '',
                product_price: '',
                starting_inventory: '',
                product_label: '',
                requiredRules: [
                    (v) => !!v || 'This field is required',
                ],
                imageName: '',
                imageUrl: '',
                imageFile: '',
                mpn: '',
                isbn: '',
                upc: '',
                ean: '',

                groups: [],

                brands: [],
                category: [],
                punit: [],
                stores: [],
                manufacturer: [],
                availability:'',
                options: {
                    brands: [],
                    categories: [],
                    stores: [],
                    punit: [],
                    manufacturer: [],

                },
                alert: {
                    show: false,
                    icon: '',
                    color: '',
                    message: ''
                },
                selectedBrand: [],
                selectedBrandValue: 0,

                selectedUnit: {},
                selectedUnitValue: 0,

                selectedStore: {},
                selectedStoreValue: 0,

                selectedCategory: {},
                selectedCategoryValue: 0,

                selectedManu: {},
                selectedManuValue: 0,
            }
        },
        mounted() {
            console.log('components.ProductsForm.vue');

            const self = this;

            self.loadBrands(cb=>{});

            self.loadPunit(cb=>{});

            self.loadManufacturer(cb=>{});

            self.loadCategory(cb=>{});

            self.loadStores(cb=>{});

            self.$store.commit('setBreadcrumbs',[
                {label:'Products',name:'product.list'},
                {label:'Add',name:''},
            ]);
        },
        methods: {
            pickFile () {
                this.$refs.image.click ()
            },
            onFilePicked (e) {
                const files = e.target.files
                if(files[0] !== undefined) {
                    this.imageName = files[0].name
                    if(this.imageName.lastIndexOf('.') <= 0) {
                        return
                    }
                    const fr = new FileReader ()
                    fr.readAsDataURL(files[0])
                    fr.addEventListener('load', () => {
                        this.imageUrl = fr.result
                        this.imageFile = files[0] // this is an image file that can be sent to server...
                    })
                } else {
                    this.imageName = ''
                    this.imageFile = ''
                    this.imageUrl = ''
                }
            },

            save() {

                const self = this;

                let payload = {
                    product_name: self.product_name,
                    part_number: self.part_number,
                    product_price: self.product_price,
                    product_label: self.product_label,
                    starting_inventory: self.starting_inventory,
                    availability: self.availability,
                    permissions: self.permissions,
                    groups: self.groups,
                    stores: self.stores,
                    brands: self.brands,
                    category: self.category,
                    punit: self.punit,
                    manufacturer: self.manufacturer,
                };

                self.$store.commit('showLoader');

                axios.post('/admin/products',payload).then(function(response) {

                    self.$store.commit('showSnackbar',{
                       message: response.data.message,
                       color: 'success',
                       duration: 3000
                    });

                    self.$eventBus.$emit('USER_ADDED');
                    self.$store.commit('hideLoader');

                    // reset
                    self.$refs.userFormAdd.reset();
                    self.permissions = [];

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

            loadBrands(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/brands',{params: params}).then(function(response) {
                    console.log(response.data.data);
                    self.options.brands = response.data.data;
                    (cb || Function)();
                });
            },

            loadPunit(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/brands',{params: params}).then(function(response) {
                    self.options.punit = response.data.data.data;
                    (cb || Function)();
                });
            },

            loadManufacturer(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/manufacturer',{params: params}).then(function(response) {
                    self.options.manufacturer = response.data.data;
                    (cb || Function)();
                });
            },

            loadCategory(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/brands',{params: params}).then(function(response) {
                    self.options.category = response.data.data.data;
                    (cb || Function)();
                });
            },

            loadStores(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/brands',{params: params}).then(function(response) {
                    self.options.stores = response.data.data.data;
                    (cb || Function)();
                });
            },


        }
    }
</script>
