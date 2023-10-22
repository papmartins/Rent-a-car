<template>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Login</div> 
                <div class="card-body">
                    <form method="POST" action="" @submit.prevent="login($event)">
                    <!-- <form method="POST" action="">  -->
                        <input type="hidden" name="_token" :value="csrf_token">
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="" required autocomplete="email" autofocus v-model="email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" v-model="password">
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" >

                                    <label class="form-check-label" for="remember">
                                        Lembrar
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                               
                                    <a class="btn btn-link" href="#">
                                        Esqueceu-se a password?
                                    </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default{
    props:['csrf_token'], // semelhante ao data, tem que ser em minusculas
    data(){
        return {
            email : 'paulo',
            password: ''
        }
    },
    methods: {
        login(e){
            // console.log(this.email,this.password)
            let url = "http://localhost:8000/api/login";
            let configuracoes = {
                method : 'post',
                body: new URLSearchParams({
                    'email' : this.email,
                    'password': this.password
                })
            }
            fetch(url,configuracoes)
                    .then(response => response.json())
                    .then(responseJson => {
                            console.log(responseJson.token)
                            // console.log(e)
                            if(responseJson.token){
                                document.cookie = 'token=' + responseJson.token // utilizar a chave token para que a aplicação 
                            }
                            // dar sequencia do envio de form por sessão tem que star dentro do then para funcionar no firefox
                            e.target.submit()
                    }
                    )
        }
    }
}
</script>
