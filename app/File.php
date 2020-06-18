<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nameOrigin',
        'nameEncrypted',
        'fileExtension',
        'url',
        'user_id',
        'report_id',
        'item_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'nameOrigin',
        'nameEncrypted',
        'fileExtension',
    ];
}
