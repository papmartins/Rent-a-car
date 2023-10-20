<?php
namespace App\Repositories;

class ModeloRepository extends AbstractRepository{

    /*
    public function __construct(Model $model){
        $this->model = $model;
        $this->c = array();
    }

    // metodos da regras de negócio
    
    public function filtro_marca($filtros){
        $filtros = explode(';', $filtros);
        foreach($filtros as $filtro){
            $this->c = explode(':',$filtro);
            
            $this->model = $this->model->with(
                ['marca' => function ($query) {
                    $query->orderBy('created_at', 'asc');
                    $query->where($this->c[0],$this->c[1],$this->c[2]);
                
                }]
            );
        }
    }
    
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
    
    public function getResultado(){
        return $this->model->get();
    }
    */
}

?>