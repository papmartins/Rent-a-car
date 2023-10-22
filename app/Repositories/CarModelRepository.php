<?php
namespace App\Repositories;

class CarModelRepository extends AbstractRepository{

    /*
    public function __construct(Model $model){
        $this->model = $model;
        $this->c = array();
    }

    // metodos da regras de negócio
    
    public function filter_brand($filtros){
        $filtros = explode(';', $filtros);
        foreach($filtros as $filter){
            $this->c = explode(':',$filter);
            
            $this->model = $this->model->with(
                ['brand' => function ($query) {
                    $query->orderBy('created_at', 'asc');
                    $query->where($this->c[0],$this->c[1],$this->c[2]);
                
                }]
            );
        }
    }
    
    public function selectAttributes($attributes){
        $this->model = $this->model->selectRaw($attributes);
    }

    public function selectAttributesRelatedRecords($attributes){
        $this->model = $this->model->with($attributes);
    }
    public function filter($filtros){
        $filtros = explode(';', $filtros);
        foreach($filtros as $filter){
            $c = explode(':',$filter);
            $this->model = $this->model->where($c[0],$c[1],$c[2]);
        }
    } 
    
    public function getResults(){
        return $this->model->get();
    }
    */
}

?>