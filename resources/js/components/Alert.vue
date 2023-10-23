<template>
    <div :class="estilo" role="alert">
        {{title}}
        <hr>
        <p>{{ details.mensagem }}</p>
        <br>
        <ul v-if="details.data">
            <li v-for="e, key in details.data" :key="key">{{ e[0] }}</li>
        </ul>
    </div>
</template>

<script>
    export default { 
        props: ['tipo', 'title', 'details'],
        computed: {
            estilo() {
                return 'alert alert-'+this.tipo
            }
        }
    }
</script>
