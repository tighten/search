<?php namespace TightenCo\Search;

use Illuminate\Support\Collection;

class CriterionCollection extends Collection
{
    public static function fromArray($criterionArray, $account = null)
    {
        return static::make($criterionArray)->map(function ($params) use ($account) {
                return CriterionRepository::retrieve(
                    array_get($params, 'category'),
                    array_get($params, 'operator'),
                    array_get($params, 'value'),
                    $account
                );
        });
    }

    public static function fromJson($criterionJson, $account = null)
    {
        return static::fromArray(json_decode($criterionJson, true), $account);
    }
}
