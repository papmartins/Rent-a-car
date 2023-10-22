<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    // permite inserir através dum array apenas os campos seguintes
    protected $fillable = ['name', 'image'];
    
    public function rules(){
        // return  [
        //     'name' => 'required|unique:brands',
        //     'image' => 'required'
        // ];
        return  [
            'name' => 'required|unique:brands,name,'.$this->id.'|min:3',
            'image' => 'required|file|mimes:png,jpeg,jpg'
        ];
        /*
            parametros do unique
            1) tabela
            2) name da coluna que será pesquisada na tabela
            3) id do registo que será desconsiderado
        */
    }
    public function feedback(){
        return [
            'required' => 'O campo :attribute é obrigatório',
            'name.unique' => 'O name da brand já existe',
            'mimes' => 'Tipo de ficheiro não suportado'
        ];
    }
    
    public function models(){ // no plural porque uma brand tem muitos models
        return $this->hasMany('App\Models\CarModel');
    }
}
