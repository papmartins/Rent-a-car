<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage; // eliminar ficheiros
use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // injeção da instância aula 295 através do construtor
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);
        $atributos_modelos = "";
        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:'.$request->atributos_modelos;
            // return$atributos_modelos;
            $marcaRepository->selectAttributosRegistosRelacionados($atributos_modelos);
        }
        else{
            $marcaRepository->selectAttributosRegistosRelacionados('modelos');
        }
        
        if($request->has('atributos')){
            $marcaRepository->selectAttributos($request->atributos);
            
        }
        
        
        if($request->has('filtro')){
            $marcaRepository->filtro($request->filtro);
        }
        
        if($request->has('filtro_modelo')){
            $marcaRepository->filtro_other($request->filtro_modelo,'modelos',$atributos_modelos);
        }
        
        return response()->json($marcaRepository->getResultado(),200);


        // ---------------------------------------
        
        $marcas = array();
        if($request->has('atributos_modelos')){
            $atributos_modelos = $request->atributos_modelos;
            $marcas = $this->marca->with('modelos:'.$atributos_modelos);
        }
        else{
            $marcas = $this->marca->with('modelos');
        }

        
        if($request->has('filtro')){
            $filtros = explode(';',$request->filtro);
            foreach($filtros as $filtro){
                $c = explode(':',$filtro);
                $marcas = $marcas->where($c[0],$c[1],$c[2]);
            }
        }

        if($request->has('atributos')){
            $atributos = $request->atributos;
            // $modelos = $this->modelo->select(explode(',',$atributos))->get();
            $marcas = $marcas->selectRaw($atributos)->get();
            
        }
        else{
            $marcas = $marcas->get();
        }
        
        return response()->json($marcas,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $marca = Marca::create($request->all());
        $request->validate($this->marca->rules(),$this->marca->feedback());
        // stateless - por padrão o redirect é para a pagina anterior, neste caso como não 
        // tem aponta para a raiz
        // A solução é acrescentar o Accept no header da requisição 

        // dd($request->nome);
        // dd($request->get('nome'));
        // dd($request->input('nome'));
        
        // dd($request->imagem);
        $imagem = $request->file('imagem');
        // $image->store('imagens'); // por padrão é o local
        $imagem_urn = $imagem->store('imagens','public'); // por padrão é o local

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        return response()->json($marca,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    // public function show(Marca $marca)
    // {
    //     return $marca;
    // }
    
    public function show($id)
    {
        // $marca = $this->marca->find($id); // alterado pela relacção entre tabelas
        $marca = $this->marca->with('modelos')->find($id);
        if($marca === null){
            //modifica o estado da resposta
            return response()->json([env('ERRO_KEY') => env('RECURSO_VAZIO')],404);
        }
        return response()->json($marca,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Marca $marca)
    // {
    //     // print_r($request->all()); // dados atualizados
    //     // echo "<br>";        
    //     // print_r($marca->getAttributes()); // dados antigos
        
    //     $marca->update($request->all());
    //     return $marca;
    // }

    public function update(Request $request,$id)
    {
        $marca = $this->marca->find($id);
        if($marca === null){
            //modifica o estado da resposta
            return response()->json([env('ERRO_KEY') => env('RECURSO_VAZIO')],404);
        }
        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            foreach($marca->rules() as $input => $regra){
                if(array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas,$marca->feedback());
        }
        else{
            $request->validate($marca->rules(),$marca->feedback());
        }
        //apagar imagem caso tenha sendo enviado um novo
        $imagem_urn = null;
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);
            $imagem = $request->file('imagem');
            // $image->store('imagens'); // por padrão é o local
            $imagem_urn = $imagem->store('imagens','public'); // por padrão é o local
        }


        // Para funcionar quando utilizado o patch ( não vêm preenchidos todos os campos)
        // Preenche com os que vêm no request
        $marca->fill($request->all());
        if($imagem_urn !== null)
            $marca->imagem = $imagem_urn;
        $marca->save();
        
        // $marca->update([
        //     'nome' => $request->nome,
        //     'imagem' => $imagem_urn
        // ]);
        return response()->json($marca,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Marca $marca)
    // {
    //     $marca->delete();
    //     return ['msg' => "Marca removida com sucesso"];
    // }
    
    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        // apaga imagem
        Storage::disk('public')->delete($marca->imagem);

        if($marca === null){
            //modifica o estado da resposta
            return response()->json([env('ERRO_KEY') => env('RECURSO_VAZIO')],404);
        }
        $marca->delete();
        return response()->json(['msg' => "Marca removida com sucesso"],200);
    }
    
}
