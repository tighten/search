<?php namespace TightenCo\Search;

class AdvancedSearch
{
    protected $criteria;
    protected $results;

    public function __construct($criteria)
    {
        $this->criteria = $criteria;
    }

    public function getIds()
    {
        if ($this->results) {
            return $this->results;
        }

        // extract category, find slugs
        foreach ($this->criteria as $criterion) {
            // Cache results, all criteria are AND joined
            $ids = $criterion->getResults();

            $this->intersectAndStoreResults($ids);
        }

        return $this->results ? $this->results : collect([]);
    }

    protected function intersectAndStoreResults($ids)
    {
        if ($ids->count() && $this->results) {
            $this->results = $ids->intersect($this->results);
            return;
        }

        $this->results = $ids->count() ? $ids : collect([]);
    }

    public function count()
    {
        return $this->getIds()->count();
    }
}
