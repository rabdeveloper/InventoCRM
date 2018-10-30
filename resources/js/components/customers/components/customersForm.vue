<template>
    <div>
        <v-card dark>
            <v-card-title>
                <v-icon>person</v-icon> Adding Customer
            </v-card-title>
            <v-divider></v-divider>
            <v-form v-model="valid" ref="customerFormAdd" lazy-validation>
                <v-container grid-list-md>
                    <v-layout row wrap>
                    <v-flex xs12 sm2>
                        <v-select
                                label="Salutation"
                                v-bind:items="options.salutation"
                                v-model="selectedSalutation"
                                dark
                                item-text="label"
                                item-value="value"
                        ></v-select>
                    </v-flex>

                    <v-flex xs12 sm4>
                        <v-text-field box dark label="First Name" v-model="firstname" :rules="nameRules"></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm4>
                        <v-text-field box dark label="Last Name" v-model="lastname" :rules="nameRules"></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm10>
                        <v-text-field box dark label="Contact Display Name" v-model="display_name" :rules="fieldRules"></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm10>
                        <v-text-field box dark label="Company Name" v-model="company_name" :rules="fieldRules"></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm5>
                        <v-text-field box dark label="Work Phone" v-model="work_phone" :rules="fieldRules"></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm5>
                        <v-text-field box dark label="Mobile Phone" v-model="mobile_phone" :rules="fieldRules"></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm10>
                        <v-text-field box dark label="Designation" v-model="designation" :rules="fieldRules"></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm10>
                        <v-text-field box dark label="Department" v-model="department" :rules="fieldRules"></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm10>
                        <v-text-field box dark label="Website" v-model="website" :rules="fieldRules"></v-text-field>
                    </v-flex>




                    </v-layout>
                </v-container>

                <v-flex xs12>
                    <v-btn @click="save()" :disabled="!valid" color="primary" dark>Save</v-btn>
                </v-flex>

            </v-form>


                <div class="page_wrap_vue">
                    <v-tabs color="primary" dark v-model="active">

                        <v-tab href="#manage-payment" ripple>
                            Payment Details
                        </v-tab>
                        <v-tab href="#manage-address" ripple>
                            Address
                        </v-tab>


                        <v-tab-item id="manage-payment">
                            <v-card flat>
                                <v-card-text>
                                    <payment-details></payment-details>
                                </v-card-text>
                            </v-card>
                        </v-tab-item>
                        <v-tab-item id="manage-address">
                            <v-card flat>
                                <v-card-text>
                                    <customer-address></customer-address>
                                </v-card-text>
                            </v-card>
                        </v-tab-item>

                    </v-tabs>
                </div>




        </v-card>
    </div>
</template>

<script>

    import PaymentDetails from './PaymentDetails.vue';
    import CustomerAddress from './CustomerAddress.vue';

    export default {
        components: {
            PaymentDetails,
            CustomerAddress
        },
        data() {

            const self = this;

            return {
                valid: false,
                name: '',
                nameRules: [
                    (v) => !!v || 'Name is required',
                ],
                email: '',
                fieldRules: [
                    (v) => !!v || 'Field is required',

                ],

                passwordConfirm: '',
                passwordConfirmRules: [
                    (v) => !(v!==self.password) || 'Password do not match.',
                ],

                active:'',
                firstname:'',
                lastname:'',
                display_name:'',
                company_name:'',
                work_phone:'',
                mobile_phone:'',
                designation:'',
                department:'',
                website:'',
                options: {

                    salutation:[
                        {label:'Mr.', value:'Mr.'},
                        {label:'Mrs.', value:'Mrs.'},
                        {label:'Ms.', value:'Ms.'},
                        {label:'Dr.', value:'Dr.'},
                    ],

                },
                selectedSalutation: {},
                selectedPermission: {},
                selectedPermissionValue: 0,

                alert: {
                    show: false,
                    icon: '',
                    color: '',
                    message: ''
                }
            }
        },

        mounted() {


            const self = this;


            self.$store.commit('setBreadcrumbs',[
                {label:'Customer',name:'customers.list'},
                {label:'Create',name:''},
            ]);
        },
        methods: {
            save() {

                const self = this;
                console.log(self);
                let payload = {
                    name: self.name,
                    email: self.email,


                };

                self.$store.commit('showLoader');

                axios.post('/admin/customers',payload).then(function(response) {

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


        }
    }
</script>

<style scoped="">
    .finder_wrap {
        padding: 0 20px;
    }
</style>
