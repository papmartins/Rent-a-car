<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage; // eliminar ficheiros
use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    private $brand;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // injeção da instância aula 295 através do construtor
    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function index(Request $request)
    {
        $brandRepository = new BrandRepository($this->brand);
        $attributes_models = "";
        if($request->has('attributes_models')){
            $attributes_models = 'models:'.$request->attributes_models;
            // return$attributes_models;
            $brandRepository->selectAttributesRelatedRecords($attributes_models);
        }
        else{
            $brandRepository->selectAttributesRelatedRecords('models');
        }
        
        if($request->has('attributes')){
            $brandRepository->selectAttributes($request->attributes);
            
        }
        
        if($request->has('filter')){
            $brandRepository->filter($request->filter);
        }
        
        if($request->has('filter_car_model')){
            $brandRepository->filter_other($request->filter_car_model,'models',$attributes_models);
        }
        
        return response()->json($brandRepository->getResults(),200);


        // ---------------------------------------
        
        $brands = array();
        if($request->has('attributes_models')){
            $attributes_models = $request->attributes_models;
            $brands = $this->brand->with('models:'.$attributes_models);
        }
        else{
            $brands = $this->brand->with('models');
        }

        
        if($request->has('filter')){
            $filtros = explode(';',$request->filter);
            foreach($filtros as $filter){
                $c = explode(':',$filter);
                $brands = $brands->where($c[0],$c[1],$c[2]);
            }
        }

        if($request->has('attributes')){
            $attributes = $request->attributes;
            // $models = $this->model->select(explode(',',$attributes))->get();
            $brands = $brands->selectRaw($attributes)->get();
            
        }
        else{
            $brands = $brands->get();
        }
        
        return response()->json($brands,200);
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
        // $brand = Brand::create($request->all());
        $request->validate($this->brand->rules(),$this->brand->feedback());
        // stateless - por padrão o redirect é para a pagina anterior, neste caso como não 
        // tem aponta para a raiz
        // A solução é acrescentar o Accept no header da requisição 

        // dd($request->name);
        // dd($request->get('name'));
        // dd($request->input('name'));
        
        // dd($request->image);
        $image = $request->file('image');
        // $image->store('images'); // por padrão é o local
        $image_urn = $image->store('images','public'); // por padrão é o local

        $brand = $this->brand->create([
            'name' => $request->name,
            'image' => $image_urn
        ]);
        return response()->json($brand,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    // public function show(Brand $brand)
    // {
    //     return $brand;
    // }
    
    public function show($id)
    {
        // $brand = $this->brand->find($id); // alterado pela relacção entre tabelas
        $brand = $this->brand->with('models')->find($id);
        if($brand === null){
            //modifica o estado da resposta
            return response()->json([env('ERROR_KEY') => env('EMPTY_RESOURCE')],404);
        }
        return response()->json($brand,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Brand $brand)
    // {
    //     // print_r($request->all()); // dados atualizados
    //     // echo "<br>";        
    //     // print_r($brand->getAttributes()); // dados antigos
        
    //     $brand->update($request->all());
    //     return $brand;
    // }

    public function update(Request $request,$id)
    {
        $brand = $this->brand->find($id);
        if($brand === null){
            //modifica o estado da resposta
            return response()->json([env('ERROR_KEY') => env('EMPTY_RESOURCE')],404);
        }
        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            foreach($brand->rules() as $input => $regra){
                if(array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas,$brand->feedback());
        }
        else{
            $request->validate($brand->rules(),$brand->feedback());
        }
        //apagar image caso tenha sendo enviado um novo
        $image_urn = null;
        if($request->file('image')){
            Storage::disk('public')->delete($brand->image);
            $image = $request->file('image');
            // $image->store('images'); // por padrão é o local
            $image_urn = $image->store('images','public'); // por padrão é o local
        }


        // Para funcionar quando utilizado o patch ( não vêm preenchidos todos os campos)
        // Preenche com os que vêm no request
        $brand->fill($request->all());
        if($image_urn !== null)
            $brand->image = $image_urn;
        $brand->save();
        
        // $brand->update([
        //     'name' => $request->name,
        //     'image' => $image_urn
        // ]);
        return response()->json($brand,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Brand $brand)
    // {
    //     $brand->delete();
    //     return ['msg' => "Brand removida com sucesso"];
    // }
    
    public function destroy($id)
    {
        $brand = $this->brand->find($id);

        // apaga image
        Storage::disk('public')->delete($brand->image);

        if($brand === null){
            //modifica o estado da resposta
            return response()->json([env('ERROR_KEY') => env('EMPTY_RESOURCE')],404);
        }
        $brand->delete();
        return response()->json(['msg' => "Marca removida com sucesso"],200);
    }
    
}
