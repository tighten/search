<?php namespace TightenCo\Search\Criteria;

use TightenCo\Search\AbstractCriterion;

class ExampleSimpleCriterion extends AbstractCriterion
{
    protected $slug = 'contact.state';
    protected $operators = [
        'is' => 'is',
        'like' => 'is like',
        'one_of' => 'is one of',
        'not_one_of' => 'is not one of',
        'empty' => 'is empty',
        'not_empty' => 'is not empty',
    ];

    protected function getIsResults()
    {
        return $this->account->contacts()
            ->where('contact_state', '=', $this->searchValue)
            ->pluck('contact_id');
    }

    protected function getLikeResults()
    {
        return $this->account->contacts()
            ->where('contact_state', 'like', "%{$this->searchValue}%")
            ->pluck('contact_id');
    }

    protected function getOneOfResults()
    {
        return $this->account->contacts()
            ->whereIn('contact_state', $this->transformToArray($this->searchValue))
            ->pluck('contact_id');
    }

    protected function getNotOneOfResults()
    {
        return $this->account->contacts()
            ->whereNotIn('contact_state', $this->transformToArray($this->searchValue))
            ->pluck('contact_id')
            ->diff($this->getOneOfResults());
    }

    protected function getEmptyResults()
    {
        return $this->account->contacts()
            ->where('contact_state', '')
            ->orWhereNull('contact_state')
            ->pluck('contact_id');
    }

    protected function getNotEmptyResults()
    {
        return $this->account->contacts()
            ->where('contact_state', '!=', '')
            ->whereNotNull('contact_state')
            ->pluck('contact_id');
    }

    protected function getFullNameAndAbbreviationsArray()
    {
        $list = returnStateList();
        $revList = array_flip($list);

        // First add the value to our searched array
        $values = $this->transformToArray($this->searchValue);

        foreach ($values as $value) {
            // Search the values and save the key
            if (in_array($value, $list)) {
                $values[] = $revList[$value];
            }

            // Search the keys and save the value
            if (array_key_exists($value, $list)) {
                $values[] = $list[$value];
            }
        }

        return $values;
    }
}
