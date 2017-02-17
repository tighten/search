<?php namespace TightenCo\Search\Criteria;

use TightenCo\Search\AbstractCriterion;

class ExampleComplexCriterion extends AbstractCriterion
{
    protected $slug = 'contact.all-fields';
    protected $operators = [
        'like' => 'Like',
    ];
    protected $includedFields = [
        'contact_org_id',
        'contact_fname',
        'contact_mname',
        'contact_lname',
        'contact_sp_fname',
        'contact_sp_mname',
        'contact_sp_lname',
        'contact_address',
        'contact_address2',
        'contact_city',
        'contact_state',
        'contact_postal_code',
        'contact_country',
        'contact_notes',
    ];

    protected function getLikeResults()
    {
        $contactIdsFromFields = $this->account->contacts()
            ->where(function ($query) {
                $includedFields = $this->includedFields;
                $query->where(array_shift($includedFields), 'like', "%{$this->searchValue}%");
                foreach ($includedFields as $field) {
                    $query->orWhere($field, 'like', "%{$this->searchValue}%");
                }
            })
            ->pluck('contact_id');

        // Additional Searches
        $emailPartial = new ContactEmailsCriterion($this->account, 'like', $this->getSearchValue());
        $phonePartial = new ContactPhonesCriterion($this->account, 'like', $this->getSearchValue());

        // Return the Merged Search Results
        return $contactIdsFromFields
            ->merge(
                $emailPartial->getResults()
            )->merge(
                $phonePartial->getResults()
            )->unique();
    }
}
