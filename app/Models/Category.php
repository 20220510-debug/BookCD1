<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['category_name', 'description'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}