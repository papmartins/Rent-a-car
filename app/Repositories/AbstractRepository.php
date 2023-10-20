<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository{
    public function __construct(Model $model){
        $this->model = $model;
        $this->c = array();
    }

    // metodos da regras de negócio
    
    public function selectAttributos($atributos){
        $this->model = $this->model->selectRaw($atributos);
    }

    public function selectAttributosRegistosRelacionados($atributos){
        $this->model = $this->model->with($atributos);
    }
    public function filtro($filtros){
        $filtros = explode(';', $filtros);
        foreach($filtros as $filtro){
            $c = explode(':',$filtro);
            $this->model = $this->model->where($c[0],$c[1],$c[2]);
        }
    } 
    public function filtro_other($filtros, $relationship,$atributos){
        $this->atributos = $atributos;
        $filtros = explode(';', $filtros);
        foreach($filtros as $filtro){
            $this->c = explode(':',$filtro);
            
            $this->model = $this->model->with(
                ["".$relationship => function ($query) {
                    $query->orderBy('created_at', 'desc');
                    if(strlen($this->atributos) > 0 )
                        $query->selectRaw(explode(":",$this->atributos)[1])->where($this->c[0],$this->c[1],$this->c[2]);
                    else
                        $query->where($this->c[0],$this->c[1],$this->c[2]);
                
                }]
            );
        }
    }
    
    public function getResultado(){
        return $this->model->get();
    }
    
    
}

?>