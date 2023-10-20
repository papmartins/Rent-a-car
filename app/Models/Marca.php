<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    // permite inserir através dum array apenas os campos seguintes
    protected $fillable = ['nome', 'imagem'];
    
    public function rules(){
        // return  [
        //     'nome' => 'required|unique:marcas',
        //     'imagem' => 'required'
        // ];
        return  [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg'
        ];
        /*
            parametros do unique
            1) tabela
            2) nome da coluna que será pesquisada na tabela
            3) id do registo que será desconsiderado
        */
    }
    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome da marca já existe',
            'mimes' => 'Tipo de ficheiro não suportado'
        ];
    }
    
    public function modelos(){ // no plural porque uma marca tem muitos modelos
        return $this->hasMany('App\Models\Modelo');
    }
}
