<?php namespace TightenCo\Search;

use Exception;
use Karani\Facades\KaraniAuth;
use TightenCo\Search\Criteria\ContactAllFieldsCriterion;
use TightenCo\Search\Criteria\ContactCityCriterion;
use TightenCo\Search\Criteria\ContactCountryCriterion;
use TightenCo\Search\Criteria\ContactEmailsCriterion;
use TightenCo\Search\Criteria\ContactGroupsCriterion;
use TightenCo\Search\Criteria\ContactIsAnonymousCriterion;
use TightenCo\Search\Criteria\ContactIsOrganizationCriterion;
use TightenCo\Search\Criteria\ContactOrganizationIdCriterion;
use TightenCo\Search\Criteria\ContactPhonesCriterion;
use TightenCo\Search\Criteria\ContactPostalCodeCriterion;
use TightenCo\Search\Criteria\ContactStateCriterion;
use TightenCo\Search\Criteria\ContactStatusCriterion;
use TightenCo\Search\Criteria\ContactTagsCriterion;

class CriterionRepository
{
    protected $items;
    protected $classDirectory = 'Criteria';
    protected static $criteria = [
        ContactAllFieldsCriterion::class,
        ContactStatusCriterion::class,
        ContactTagsCriterion::class,
        ContactGroupsCriterion::class,
        ContactCityCriterion::class,
        ContactStateCriterion::class,
        ContactPostalCodeCriterion::class,
        ContactCountryCriterion::class,
        ContactPhonesCriterion::class,
        ContactEmailsCriterion::class,
        ContactIsOrganizationCriterion::class,
        ContactOrganizationIdCriterion::class,
        ContactIsAnonymousCriterion::class,
    ];

    public static function retrieve($slug, $operator = null, $searchValue = null, $account = null)
    {
        if ($account === null) {
            $account = KaraniAuth::account();
        }

        $criteria = static::getAllForAccount($account);

        if (! $criteria->has($slug)) {
            throw new Exception("Search Category '{$slug}' is not available.");
        }

        $class = get_class($criteria->get($slug));

        return new $class($account, $operator, $searchValue);
    }

    public static function getAllForAccount($account)
    {
        return collect(static::$criteria)->mapWithKeys(function ($class) use ($account) {
            $criterionInstance = new $class($account);

            return [$criterionInstance->getSlug() => $criterionInstance];
        })->filter(function ($criterion) use ($account) {
            return $criterion->isAppropriateForAccount($account);
        });
    }
}
