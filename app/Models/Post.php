<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;

    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * Data that will be indexed by TNTSearch
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,      // VERY IMPORTANT
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
}
