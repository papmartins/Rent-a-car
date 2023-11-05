<template>
    <nav aria-label="Page navigation example">
        <ul class="pagination" style="cursor:pointer">
            <li v-for="l, key in links" :key="key" 
                :class="l.active ? 'page-item active' : 'page-item'" 
                @click="pagination(l)"
            >
                <a class="page-link" v-html="l.label"></a>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        props: ['links'],
        emits: ["pagination"],
        methods: {
            pagination(l) {
                // Emit a custom event with the parameter
                this.$emit('pagination', l);
            }
        }
    }
</script>
