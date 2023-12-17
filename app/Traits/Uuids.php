<?php

namespace App\Traits;

trait Uuids
{
    /**
     * Boot function from Laravel
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function($model){
            if(empty($model->{$model->getKeyName()})){
                do{
                    $model->{$model->getKeyName()} = strtolower(md5(uniqid(rand(), false) . microtime()));
                }
                while($model->where($model->getKeyName(), $model->{$model->getKeyName()})->exists());
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are Incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the Auto-Incrementing key type
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
