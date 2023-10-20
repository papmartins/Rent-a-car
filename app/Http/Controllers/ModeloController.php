<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage; // eliminar ficheiros
use App\Models\Modelo;
use Illuminate\Http\Request;
use App\Repositories\ModeloRepository;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $modeloRepository = new ModeloRepository($this->modelo);
        
        if($request->has('atributos')){
            $modeloRepository->selectAttributos($request->atributos);
            
        }
        $atributos_marca = "";
        if($request->has('atributos_marca')){
            $atributos_marca = 'marca:'.$request->atributos_marca;
            $modeloRepository->selectAttributosRegistosRelacionados($atributos_marca);
        }
        else{
            $modeloRepository->selectAttributosRegistosRelacionados('marca');
        }
        
        
        if($request->has('filtro')){
            $modeloRepository->filtro($request->filtro);
        }
        
        if($request->has('filtro_marca')){
            $modeloRepository->filtro_other($request->filtro_marca,'marca', $atributos_marca);
        }
        
        return response()->json($modeloRepository->getResultado(),200);

        // dd($request->get('atributos'));
        // ou 
        // dd($request->atributos);

        $modelos = array();

        if($request->has('atributos_marca')){
            $atributos_marca = $request->atributos_marca;
            $modelos = $this->modelo->with('marca:'.$atributos_marca);
        }
        else{
            $modelos = $this->modelo->with('marca');
        }

        if($request->has('filtro')){
            $filtros = explode(';',$request->filtro);
            foreach($filtros as $filtro){
                $c = explode(':',$filtro);
                $modelos = $modelos->where($c[0],$c[1],$c[2]);
            }
        }

        if($request->has('atributos')){
            $atributos = $request->atributos;
            // $modelos = $this->modelo->select(explode(',',$atributos))->get();
            $modelos = $modelos->selectRaw($atributos)->get();
            
        }
        else{
            $modelos = $modelos->get();
        }

        // return response()->json($this->modelo->all(),200);
        return response()->json($modelos,200);
                // all() -> criando um objecto + get() = collection
                // get() -> modificar a consulta -> collection
            
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
        $request->validate($this->modelo->rules());
        // stateless - por padrão o redirect é para a pagina anterior, neste caso como não 
        // tem aponta para a raiz
        // A solução é acrescentar o Accept no header da requisição 

        // dd($request->nome);
        // dd($request->get('nome'));
        // dd($request->input('nome'));
        
        // dd($request->imagem);
        $imagem = $request->file('imagem');
        // $image->store('imagens'); // por padrão é o local
        $imagem_urn = $imagem->store('imagens/modelos','public'); // por padrão é o local

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
        ]);
        return response()->json($modelo,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $modelo = $this->modelo->find($id); // alterado pela relacção entre tabelas
        $modelo = $this->modelo->with('marca')->find($id);
        if($modelo === null){
            //modifica o estado da resposta
            return response()->json([env('ERRO_KEY') => env('RECURSO_VAZIO')],404);
        }
        return response()->json($modelo,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo === null){
            //modifica o estado da resposta
            return response()->json([env('ERRO_KEY') => env('RECURSO_VAZIO')],404);
        }
        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            foreach($modelo->rules() as $input => $regra){
                if(array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas);
        }
        else{
            $request->validate($modelo->rules());
        }
        //apagar imagem caso tenha sendo enviado um novo
        $imagem_urn = null;
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
            $imagem = $request->file('imagem');
            // $image->store('imagens'); // por padrão é o local
            $imagem_urn = $imagem->store('imagens/modelos','public'); // por padrão é o local
    
        }

        
        $modelo->fill($request->all());
        if($imagem_urn !== null)
            $modelo->imagem = $imagem_urn;
        $modelo->save();
        // dd($modelo->getAttributes());
        
        // $modelo->update([
        //     'marca_id' => $request->marca_id,
        //     'nome' => $request->nome,
        //     'imagem' => $imagem_urn,
        //     'numero_portas' => $request->numero_portas,
        //     'lugares' => $request->lugares,
        //     'air_bag' => $request->air_bag,
        //     'abs' => $request->abs,
        // ]);
        return response()->json($modelo,200);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);

        // apaga imagem
        Storage::disk('public')->delete($modelo->imagem);

        if($modelo === null){
            //modifica o estado da resposta
            return response()->json([env('ERRO_KEY') => env('RECURSO_VAZIO')],404);
        }
        $modelo->delete();
        return response()->json(['msg' => "Modelo removido com sucesso"],200);
    }
}
