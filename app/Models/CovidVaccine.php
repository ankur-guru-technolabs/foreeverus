<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class CovidVaccine extends Model
{
    protected $table = 'covid_vaccine';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "title",
        'created_at',
        'updated_at'
    ];

    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected function addUpdate($params = [])
    {
        if(empty($params)) {
            return false;   
        }

        $id = Arr::get($params, 'id', 0);
        
        if($id > 0) {
            $model = self::find($id);
            if(!$model) {
                $model = new self();    
            }
        } else {
            $model = new self();
        }

        $params        = Arr::except($params, ['id']);
        $fillableFiled = $model->getFillable();
        $fillableFiled = array_intersect_key($params, array_flip($fillableFiled));
        $model->fill($fillableFiled);
        $result = $model->save();
        if($result) {
            return $model;
        }

        return false;
    }
}
