<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = ['marca_id','nome', 'imagem','numero_portas','lugares','air_bag','abs'];
    
    public function rules(){
        return  [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
            'numero_portas' => 'required|integer|digits_between:1,5', // 1,2,3,4 ou 5
            'lugares' => 'required|integer|digits_between:1,2',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean' // true, false, 1, 0, "1" e "0"
        ];
        /*
            parametros do unique
            1) tabela
            2) nome da coluna que será pesquisada na tabela
            3) id do registo que será desconsiderado
        */
    }

    public function marca(){
        // um modelo pertence a uma marca
        return $this->belongsTo('App\Models\Marca');
    }
}
