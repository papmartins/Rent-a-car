<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                
                <!-- início do card de search -->
                <card-component title="Busca de marcas">
                    <template v-slot:content>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-container-component title="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID da marca">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID" v-model="search.id">
                                </input-container-component>                   
                            </div>
                            <div class="col mb-3">
                                <input-container-component title="Nome da marca" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da marca" v-model="search.name">
                                </input-container-component>
                            </div>
                        </div>
                    </template>
                    
                    <template v-slot:footer>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="search()">Pesquisar</button>
                    </template>
                </card-component>
                <!-- fim do card de search -->


                <!-- início do card de listagem de marcas -->
                <card-component title="Relação de marcas">
                    <template v-slot:content>
                        <table-component 
                            :data="brands.data"
                            :view="{visible: true, dataToggle: 'modal', dataTarget: '#modalBrandView'}"
                            :update="{visible: true, dataToggle: 'modal', dataTarget: '#modalBrandUpdate'}"
                            :remove="{visible: true, dataToggle: 'modal', dataTarget: '#modalBrandRemove'}"
                            :titles="{
                                id: {title: 'ID', tipo: 'texto'},
                                name: {title: 'Nome', tipo: 'texto'},
                                imagem: {title: 'Imagem', tipo: 'imagem'},
                                created_at: {title: 'Criação', tipo: 'data'},
                            }"
                        ></table-component>
                    </template>

                    <template v-slot:footer>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, key in brands.links" :key="key" 
                                        :class="l.active ? 'page-item active' : 'page-item'" 
                                        @click="paginacao(l)"
                                    >
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>

                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>
                    </template>
                </card-component>
                <!-- fim do card de listagem de marcas -->
            </div>
        </div>


        <!-- início do modal de inclusão de marca -->
        <modal-component id="modalMarca" title="Adicionar marca">

            <template v-slot:warnings>
                <alert-component tipo="success" :details="transactionDetails" title="Cadastro realizado com sucesso" v-if="transactionStatus == 'added'"></alert-component>
                <alert-component tipo="danger" :details="transactionDetails" title="Erro ao tentar cadastrar a marca" v-if="transactionStatus == 'error'"></alert-component>
            </template>

            <template v-slot:content>
                <div class="form-group">
                    <input-container-component title="Nome da marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da marca" v-model="nameBrand">
                    </input-container-component>
                    {{ nameBrand }}
                </div>

                <div class="form-group">
                    <input-container-component title="Imagem" id="novoImagem" id-help="novoImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                    {{ imageFiles }}
                </div>
            </template>

            <template v-slot:footer>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
        <!-- fim do modal de inclusão de marca -->

        <!-- início do modal de visualização de marca -->
        <modal-component id="modalBrandView" title="Visualizar marca">
            <template v-slot:warnings></template>
            <template v-slot:content>
                <input-container-component title="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component title="Nome da marca">
                    <input type="text" class="form-control" :value="$store.state.item.name" disabled>
                </input-container-component>

                <input-container-component title="Imagem">
                    <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                </input-container-component>

                <input-container-component title="Data de criação">
                    <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
                </input-container-component>
            </template>
            <template v-slot:footer>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!-- fim do modal de inclusão de marca -->


        <!-- início do modal de remoção de marca -->
        <modal-component id="modalBrandRemove" title="Remover marca">
            <template v-slot:warnings>
                <alert-component tipo="success" title="Transação realizada com sucesso" :details="$store.state.transaction" v-if="$store.state.transaction.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" title="Erro na transação" :details="$store.state.transaction" v-if="$store.state.transaction.status == 'erro'"></alert-component>
            </template>
            <template v-slot:content v-if="$store.state.transaction.status != 'sucesso'">
                <input-container-component title="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component title="Nome da marca">
                    <input type="text" class="form-control" :value="$store.state.item.name" disabled>
                </input-container-component>
            </template>
            <template v-slot:footer>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" @click="remove()" v-if="$store.state.transaction.status != 'sucesso'">Remover</button>
            </template>
        </modal-component>
        <!-- fim do modal de remoção de marca -->

        <!-- início do modal de atualização de marca -->
        <modal-component id="modalBrandUpdate" title="Atualizar marca">

            <template v-slot:warnings>
                <alert-component tipo="success" title="Transação realizada com sucesso" :details="$store.state.transaction" v-if="$store.state.transaction.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" title="Erro na transação" :details="$store.state.transaction" v-if="$store.state.transaction.status == 'erro'"></alert-component>
            </template>

            <template v-slot:content>
                <div class="form-group">
                    <input-container-component title="Nome da marca" id="atualizarNome" id-help="atualizarNomeHelp" texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="atualizarNome" aria-describedby="atualizarNomeHelp" placeholder="Nome da marca" v-model="$store.state.item.name">
                    </input-container-component>
                </div>

                <div class="form-group">
                    <input-container-component title="Imagem" id="atualizarImagem" id-help="atualizarImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control-file" id="atualizarImagem" aria-describedby="atualizarImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                </div>
            </template>

            <template v-slot:footer>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="update()">Atualizar</button>
            </template>
        </modal-component>
        <!-- fim do modal de atualização de marca -->
    </div>
</template>

<script>
import Paginate from './Paginate.vue'
    export default {
  components: { Paginate },
        data() {
            return {
                urlBase: 'http://localhost/api/v1/marca',
                urlPagination: '',
                urlFilter: '',
                nameBrand: '',
                imageFiles: [],
                transactionStatus: '',
                transactionDetails: {},
                brands: { data: [] },
                search: { id: '', name: '' }
            }
        },
        methods: {
            update() {

                let formData = new FormData();
                formData.append('_method', 'patch')
                formData.append('name', this.$store.state.item.name)

                if(this.imageFiles[0]) {
                    formData.append('imagem', this.imageFiles[0])
                }

                let url = this.urlBase + '/' + this.$store.state.item.id

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }

                axios.post(url, formData, config)
                    .then(response => {
                        this.$store.state.transaction.status = 'sucesso'
                        this.$store.state.transaction.message = 'Registro de marca atualizado com sucesso!'

                        //limpar o campo de seleção de arquivos
                        atualizarImagem.value = ''
                        this.carregarLista()
                    })
                    .catch(errors => {
                        this.$store.state.transaction.status = 'erro'
                        this.$store.state.transaction.message = errors.response.data.message
                        this.$store.state.transaction.data = errors.response.data.errors
                    })
            },
            remove() {
                let confirmation = confirm('Tem certeza que deseja remover esse registro?')

                if(!confirmation) {
                    return false;
                }

                let formData = new FormData();
                formData.append('_method', 'delete')

                let url = this.urlBase + '/' + this.$store.state.item.id

                axios.post(url, formData)
                    .then(response => {                        
                        this.$store.state.transaction.status = 'sucesso'
                        this.$store.state.transaction.message = response.data.msg
                        this.carregarLista()
                    })
                    .catch(errors => {
                        this.$store.state.transaction.status = 'erro'
                        this.$store.state.transaction.message = errors.response.data.erro
                    })

            },
            search() {
                //console.log(this.search)

                let filter = ''

                for(let key in this.search) {

                    if(this.search[key]) {
                        //console.log(key, this.search[key])
                        if(filter != '') {
                            filter += ";"
                        }
                    
                        filter += key + ':like:' + this.search[key]
                    }
                }
                if(filter != '') {
                    this.urlPagination = 'page=1'
                    this.urlFilter = '&filter='+filter
                } else {
                    this.urlFilter = ''
                }

                this.carregarLista()
            },
            paginacao(l) {
                if(l.url) {
                    //this.urlBase = l.url //ajustando a url de consulta com o parâmetro de página
                    this.urlPagination = l.url.split('?')[1]
                    this.carregarLista() //requisitando novamente os dados para nossa API
                }
            },
            carregarLista() {

                let url = this.urlBase + '?' + this.urlPagination + this.urlFilter
                
                axios.get(url)
                    .then(response => {
                        this.brands = response.data
                        //console.log(this.brands)
                    })
                    .catch(errors => {
                        console.log(errors)
                    })
            },
            carregarImagem(e) {
                this.imageFiles = e.target.files
            },
            salvar() {
                console.log(this.nameBrand, this.imageFiles[0])

                let formData = new FormData();
                formData.append('name', this.nameBrand)
                formData.append('imagem', this.imageFiles[0])

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }

                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        this.transactionStatus = 'added'
                        this.transactionDetails = {
                            mensagem: 'ID do registro: ' + response.data.id
                        }
                        
                        console.log(response)
                    })
                    .catch(errors => {
                        this.transactionStatus = 'error'
                        this.transactionDetails = {
                            mensagem: errors.response.data.message,
                            data: errors.response.data.errors
                        }
                        //errors.response.data.message
                    })
            }
        },
        mounted() {
            this.carregarLista()
        }
    }
</script>
