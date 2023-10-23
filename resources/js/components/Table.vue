<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="t, key in titles" :key="key">{{t.title}}</th>
                    <th v-if="view.visible || update.visible || remove.visible"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj, key in dataFiltered" :key="key">
                    <td v-for="value, keyValue in obj" :key="keyValue">
                        <span v-if="titles[keyValue].tipo == 'texto'">{{valor}}</span>
                        <span v-if="titles[keyValue].tipo == 'data'">
                            {{ value | formataDateTimeGlobal }}
                        </span>
                        <span v-if="titles[keyValue].tipo == 'imagem'">
                            <img :src="'/storage/'+valor" width="30" height="30">
                        </span>
                    </td>
                    <td v-if="view.visible || update.visible || remove.visible">
                        <button v-if="view.visible" class="btn btn-outline-primary btn-sm" :data-toggle="view.dataToggle" :data-target="view.dataTarget" @click="setStore(obj)">view</button>
                        <button v-if="update.visible" class="btn btn-outline-primary btn-sm" :data-toggle="update.dataToggle" :data-target="update.dataTarget" @click="setStore(obj)">update</button>
                        <button v-if="remove.visible" class="btn btn-outline-danger btn-sm" :data-toggle="remove.dataToggle" :data-target="remove.dataTarget" @click="setStore(obj)">remove</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        props: ['data', 'titles', 'update', 'view', 'remove'],
        methods: {
            setStore(obj) {
                this.$store.state.transaction.status = ''
                this.$store.state.transaction.message = ''
                this.$store.state.transaction.data = ''
                this.$store.state.item = obj
            }
        },
        computed: {
            dataFiltered() {
                
                let campos = Object.keys(this.titles)
                let dataFiltered = []

                this.data.map((item, chave) => {

                    let itemFiltered = {}
                    campos.forEach(campo => {
                        
                        itemFiltered[campo] = item[campo] //utilizar a sintaxe de array para atribuir valores a objetos
                    })
                    dataFiltered.push(itemFiltered)
                })

                return dataFiltered //retorne um array de objetos 
            }
        }
    }
</script>
