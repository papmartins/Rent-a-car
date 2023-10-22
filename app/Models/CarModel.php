<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;

    protected $fillable = ['brand_id','name', 'image','doors','seats','air_bag','abs'];
    
    public function rules(){
        return  [
            'brand_id' => 'exists:brands,id',
            'name' => 'required|unique:models,name,'.$this->id.'|min:3',
            'image' => 'required|file|mimes:png,jpeg,jpg',
            'doors' => 'required|integer|digits_between:1,5', // 1,2,3,4 ou 5
            'seats' => 'required|integer|digits_between:1,2',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean' // true, false, 1, 0, "1" e "0"
        ];
        /*
            parametros do unique
            1) tabela
            2) name da coluna que será pesquisada na tabela
            3) id do registo que será desconsiderado
        */
    }

    public function brand(){
        // um model pertence a uma brand
        return $this->belongsTo('App\Models\Brand');
    }
}
