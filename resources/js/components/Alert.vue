<template>
    <div :class="style" role="alert">
        {{title}}
        <hr>
        <p>{{ details.message }}</p>
        <br>
        <ul v-if="details.data">
            <li v-for="e, key in details.data" :key="key">{{ e[0] }}</li>
        </ul>
    </div>
</template>

<script>
    export default { 
        props: ['type', 'title', 'details'],
        computed: {
            style() {
                return 'alert alert-'+this.type
            }
        }
    }
</script>
