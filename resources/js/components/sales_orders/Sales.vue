
<template>
    <div class="page_wrap_vue pa-3">
        <h2>Sales PAGE</h2>

    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('pages.Warehouse.vue');

            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Warehouse',name:''}
            ]);
        }
    }
</script>
