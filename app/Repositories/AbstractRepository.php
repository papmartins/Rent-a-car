<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository{
    private $model;
    private $c;
    private $attributes;

    public function __construct(Model $model){
        $this->model = $model;
        $this->c = array();
    }

    // metodos da regras de negócio
    
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
    public function filter_other($filtros, $relationship,$attributes){
        $this->attributes = $attributes;
        $filtros = explode(';', $filtros);
        foreach($filtros as $filter){
            $this->c = explode(':',$filter);
            
            $this->model = $this->model->with(
                ["".$relationship => function ($query) {
                    $query->orderBy('created_at', 'desc');
                    if(strlen($this->attributes) > 0 )
                        $query->selectRaw(explode(":",$this->attributes)[1])->where($this->c[0],$this->c[1],$this->c[2]);
                    else
                        $query->where($this->c[0],$this->c[1],$this->c[2]);
                
                }]
            );
        }
    }
    
    public function getResults(){
        return $this->model->get();
    }
    
    
}

?>