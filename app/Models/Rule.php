<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $table = 'rules';

    protected $primaryKey = 'id';

    protected $fillable = [
       'uuid', 'user_id', 'action', 'condition', 'url'
    ];
}
