<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage; // eliminar ficheiros
use App\Models\CarModel;
use Illuminate\Http\Request;
use App\Repositories\CarModelRepository;

class CarModelController extends Controller
{
    private $carModel;
    
    public function __construct(CarModel $model)
    {
        $this->carModel = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $carModelRepository = new CarModelRepository($this->carModel);
        
        if($request->has('attributes')){
            $carModelRepository->selectAttributes($request->attributes);
            
        }
        $attributes_brand = "";
        if($request->has('attributes_brand')){
            $attributes_brand = 'brand:'.$request->attributes_brand;
            $carModelRepository->selectAttributesRelatedRecords($attributes_brand);
        }
        else{
            $carModelRepository->selectAttributesRelatedRecords('brand');
        }
        
        
        if($request->has('filter')){
            $carModelRepository->filter($request->filter);
        }
        
        if($request->has('filter_brand')){
            $carModelRepository->filter_other($request->filter_brand,'brand', $attributes_brand);
        }
        
        return response()->json($carModelRepository->getResults(),200);

        // dd($request->get('attributes'));
        // ou 
        // dd($request->attributes);

        $models = array();

        if($request->has('attributes_brand')){
            $attributes_brand = $request->attributes_brand;
            $models = $this->carModel->with('brand:'.$attributes_brand);
        }
        else{
            $models = $this->carModel->with('brand');
        }

        if($request->has('filter')){
            $filtros = explode(';',$request->filter);
            foreach($filtros as $filter){
                $c = explode(':',$filter);
                $models = $models->where($c[0],$c[1],$c[2]);
            }
        }

        if($request->has('attributes')){
            $attributes = $request->attributes;
            // $models = $this->carModel->select(explode(',',$attributes))->get();
            $models = $models->selectRaw($attributes)->get();
            
        }
        else{
            $models = $models->get();
        }

        // return response()->json($this->carModel->all(),200);
        return response()->json($models,200);
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
        // $brand = Brand::create($request->all());
        $request->validate($this->carModel->rules());
        // stateless - por padrão o redirect é para a pagina anterior, neste caso como não 
        // tem aponta para a raiz
        // A solução é acrescentar o Accept no header da requisição 

        // dd($request->name);
        // dd($request->get('name'));
        // dd($request->input('name'));
        
        // dd($request->image);
        $image = $request->file('image');
        // $image->store('images'); // por padrão é o local
        $image_urn = $image->store('images/models','public'); // por padrão é o local

        $model = $this->carModel->create([
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'image' => $image_urn,
            'doors' => $request->doors,
            'seats' => $request->seats,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
        ]);
        return response()->json($model,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CarModel  $model
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $model = $this->carModel->find($id); // alterado pela relacção entre tabelas
        $model = $this->carModel->with('brand')->find($id);
        if($model === null){
            //modifica o estado da resposta
            return response()->json([env('ERROR_KEY') => env('EMPTY_RESOURCE')],404);
        }
        return response()->json($model,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CarModel  $model
     * @return \Illuminate\Http\Response
     */
    public function edit(Model $model)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CarModel  $model
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $model = $this->carModel->find($id);
        if($model === null){
            //modifica o estado da resposta
            return response()->json([env('ERROR_KEY') => env('EMPTY_RESOURCE')],404);
        }
        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            foreach($model->rules() as $input => $regra){
                if(array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas);
        }
        else{
            $request->validate($model->rules());
        }
        //apagar image caso tenha sendo enviado um novo
        $image_urn = null;
        if($request->file('image')){
            Storage::disk('public')->delete($model->image);
            $image = $request->file('image');
            // $image->store('images'); // por padrão é o local
            $image_urn = $image->store('images/models','public'); // por padrão é o local
    
        }

        
        $model->fill($request->all());
        if($image_urn !== null)
            $model->image = $image_urn;
        $model->save();
        // dd($model->getAttributes());
        
        // $model->update([
        //     'brand_id' => $request->brand_id,
        //     'name' => $request->name,
        //     'image' => $image_urn,
        //     'doors' => $request->doors,
        //     'seats' => $request->seats,
        //     'air_bag' => $request->air_bag,
        //     'abs' => $request->abs,
        // ]);
        return response()->json($model,200);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CarModel  $model
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = $this->carModel->find($id);

        // apaga image
        Storage::disk('public')->delete($model->image);

        if($model === null){
            //modifica o estado da resposta
            return response()->json([env('ERROR_KEY') => env('EMPTY_RESOURCE')],404);
        }
        $model->delete();
        return response()->json(['msg' => "Model removido com sucesso"],200);
    }
}
