<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class UserHobbies extends Model
{
    protected $table = 'user_hobbies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "user_id",
        'hobbie_id',
        'sub_hobbie_id',
    ];

    public function getShowMyAgeAttribute($value)
    {
        return (int) $value;
    }

    public function getDistanceVisibleAttribute($value)
    {
        return (int) $value;
    }

    public function getShowNotificationAttribute($value)
    {
        return (int) $value;
    }

    public function getGhostModeAttribute($value)
    {
        return (int) $value;
    }

    public function getSubHobbieIdAttribute($value)
    {
        return explode(',', $value);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function addUpdateHobbies($params = [])
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
