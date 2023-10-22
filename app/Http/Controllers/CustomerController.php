<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Repositories\CustomerRepository;

class CustomerController extends Controller
{
    public function __construct(Customer $customer) {
        $this->customer = $customer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customerRepository = new CustomerRepository($this->customer);

        if($request->has('filter')) {
            $customerRepository->filter($request->filter);
        }

        if($request->has('attributes')) {
            $customerRepository->selectAtributos($request->attributes);
        } 

        return response()->json($customerRepository->getResults(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->customer->rules());

        $customer = $this->customer->create([
            'name' => $request->name
        ]);

        return response()->json($customer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = $this->customer->find($id);
        if($customer === null) {
            return response()->json(['error' => 'Recurso pesquisado não existe'], 404) ;
        } 

        return response()->json($customer, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = $this->customer->find($id);

        if($customer === null) {
            return response()->json(['error' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($customer->rules() as $input => $regra) {
                
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            
            $request->validate($regrasDinamicas);

        } else {
            $request->validate($customer->rules());
        }
        
        $customer->fill($request->all());
        $customer->save();
        
        return response()->json($customer, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = $this->customer->find($id);

        if($customer === null) {
            return response()->json(['error' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        $customer->delete();
        return response()->json(['msg' => 'O customer foi removido com sucesso!'], 200);
        
    }
}
