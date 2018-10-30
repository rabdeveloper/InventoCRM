<template>

<v-container grid-list-md>
    <v-layout row wrap>
        <v-flex xs12 sm6>
            <v-card-title> Billing Address </v-card-title>
            <v-flex xs12 sm10>
                <v-text-field box dark multi-line label="Address 1" v-model="billing.address1" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark multi-line label="Address 2" v-model="billing.address2" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="City" v-model="billing.city" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="State" v-model="billing.state" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="Zip Code" v-model="billing.zipcode" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="Country" v-model="billing.country" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="Fax" v-model="billing.fax" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="Phone" v-model="billing.phone" ></v-text-field>
            </v-flex>


        </v-flex>

        <v-flex xs12 sm6>
            <v-card-title> Shipping Address </v-card-title>

            <v-flex xs12 sm10>
                <v-text-field box dark multi-line label="Address 1" v-model="shipping.address1" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark multi-line label="Address 2" v-model="shipping.address2" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="City" v-model="shipping.city" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="State" v-model="shipping.state" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="Zip Code" v-model="shipping.zipcode" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="Country" v-model="shipping.country" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="Fax" v-model="shipping.fax" ></v-text-field>
            </v-flex>

            <v-flex xs12 sm10>
                <v-text-field box dark label="Phone" v-model="shipping.phone" ></v-text-field>
            </v-flex>
        </v-flex>


        <v-flex xs12>
            <v-btn @click="save()" :disabled="!valid" color="primary" dark>Save</v-btn>
        </v-flex>


    </v-layout>
</v-container>

</template>

<script>
    export default {
        data() {
            return {
                valid: false,
                isLoading: false,
                billing:{},
                shipping:{},
                fieldRules: [
                    (v) => !!v || 'Field is required',
                ]
            }
        },
        mounted() {

            const self = this;
        },
        methods: {
            save() {

                const self = this;
                console.log(self);
                let payload = {
                    address: self,

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
