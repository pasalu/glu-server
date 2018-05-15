<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    protected $fillable = [
        'submitterID',
        'processorID',
        'status',
        'command',
        'submittedOn',
        'priority'
    ];
}
