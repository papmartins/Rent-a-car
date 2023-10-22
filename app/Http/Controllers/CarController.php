<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Repositories\CarRepository;

class CarController extends Controller
{
    private $car;

    public function __construct(Car $car) {
        $this->car = $car;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carRepository = new CarRepository($this->car);

        if($request->has('attributes_model')) {
            $attributes_model = 'model:id,'.$request->attributes_model;
            $carRepository->selectAttributesRelatedRecords($attributes_model);
        } else {
            $carRepository->selectAttributesRelatedRecords('model');
        }

        if($request->has('filter')) {
            $carRepository->filter($request->filter);
        }

        if($request->has('attributes')) {
            $carRepository->selectAttributes($request->attributes);
        } 

        return response()->json($carRepository->getResults(), 200);
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
        $request->validate($this->car->rules());

        $car = $this->car->create([
            'model_id' => $request->model_id,
            'license_plate' => $request->license_plate,
            'available' => $request->available,
            'km' => $request->km
        ]);

        return response()->json($car, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $car = $this->car->with('model')->find($id);
        if($car === null) {
            return response()->json(['error' => 'Recurso pesquisado não existe'], 404) ;
        } 

        return response()->json($car, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function edit(Car $car)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $car = $this->car->find($id);

        if($car === null) {
            return response()->json(['error' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($car->rules() as $input => $regra) {
                
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            
            $request->validate($regrasDinamicas);

        } else {
            $request->validate($car->rules());
        }
        
        $car->fill($request->all());
        $car->save();
        
        return response()->json($car, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Car  $car
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $car = $this->car->find($id);

        if($car === null) {
            return response()->json(['error' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        $car->delete();
        return response()->json(['msg' => 'O carro foi removido com sucesso!'], 200);
        
    }
}
