<?php namespace TightenCo\Search;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

abstract class AbstractCriterion implements Jsonable, Arrayable
{
    protected $account;
    protected $slug;
    protected $operators = [];
    protected $searchValue;
    protected $searchOperator;

    public function __construct($account = null, $operator = null, $searchValue = null)
    {
        $this->account = $account;
        $this->setOperator($operator);
        $this->searchValue = $this->sanitize($searchValue);
    }

    public static function fromParams($slug, $operator = null, $searchValue = null, $account = null)
    {
        return CriterionRepository::retrieve($slug, $operator, $searchValue, $account);
    }

    public function setOperator($operator)
    {
        if (! $this->isValidOperator($operator)) {
            throw new InvalidOperatorException("The operator '{$operator}' isn't available for the <i>" . $this->getSlug() . " </i> Criterion.");
        }

        $operatorFunctionName = $this->transformOperatorToFunction($operator);

        if (! method_exists($this, $operatorFunctionName)) {
            throw new \Exception("Method '{$operatorFunctionName}' does not exist on '" . static::class . "', but it should according to class operator settings.  Please define this function.'");
        }

        $this->searchOperator = $operator;

        return $this;
    }

    protected function isValidOperator($operatorSlug)
    {
        return is_null($operatorSlug) || array_key_exists($operatorSlug, $this->operators);
    }

    public function getResults()
    {
        $operatorFunctionName = $this->transformOperatorToFunction($this->searchOperator);

        return $this->$operatorFunctionName();
    }

    public function toJson($options = 0)
    {
        return json_encode([
            'criterion' => $this->getSlug(),
            'operator' => $this->getOperator(),
            'value' => $this->getSearchValue(),
        ]);
    }

    public function toArray()
    {
        return [
            'criterion' => $this->getSlug(),
            'operator' => $this->getOperator(),
            'value' => $this->getSearchValue(),
        ];
    }

    /**
     * Getters
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function getSearchValue()
    {
        return $this->searchValue;
    }

    public function getOperator()
    {
        return $this->searchOperator;
    }

    public function getOperators()
    {
        return collect($this->operators);
    }

    public function getOperatorSlugs()
    {
        return $this->getOperators()->keys();
    }

    /**
     * Use $account (or the appropriate object) to return lists of available search values
     * (ie: contact states) for ease in populating the UI
     */
    public static function getSuggestionsForAccount($account)
    {
        return collect([]);
    }

    /**
     * Use $account (or the appropriate object) to return lists of available search values
     * (ie: contact states) for ease in populating the UI
     */
    public static function isAppropriateForAccount($account)
    {
        return true;
    }

    protected function transformOperatorToFunction($operator)
    {
        return 'get' . collect(explode('_', $operator))->map(function ($segment) {
            return ucfirst($segment);
        })->implode('') . 'Results';
    }

    /**
     * Sanitize search input
     */
    protected function sanitize($searchValue)
    {
        // Example regex that only allows spaces, periods, dashes
        // return preg_replace("/[^a-zA-Z0-9\s.-_]/", '', $searchValue);
        return $searchValue;

    }

    /**
     * Transforms saved or form-submitted search criteria values into an array
     *
     * @param   mixed       value string, array, JSON string
     * @return  array       exploded values
     */
    protected function transformToArray($input)
    {
        if (is_array($input) && count($input) > 0) {
            // Return the array
            return $input;
        } elseif (is_string($input) && count(json_decode($input)) > 1) {
            // Decode the JSON-encoded string
            return json_decode($input, true);
        } elseif (is_string($input) && strpos($input, ',') !== false) {
            // Convert Comma-separated, trimmed string
            return array_map('trim', explode(',', $input));
        } elseif (is_string($input)) {
            return [$input];
        }

        return [];
    }
}
