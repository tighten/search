<?php namespace TightenCo\Search;

class Search extends Model
{
    protected $table = 'search';
    protected $primaryKey = 'id';
    protected $fillable = [
        'account_id',
        'order',
        'title',
        'content_type',
        'criteria'
    ];

    public $timestamps = false;

    protected $casts = [
        'criteria' => 'array',
    ];
}
