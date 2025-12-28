<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Country;
use App\Entity\CurrencyEmbeddable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CountrySyncService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager
    ) {}

    public function syncCountries(): void
    {
        $response = $this->httpClient->request('GET', 'https://restcountries.com/v3.1/independent?status=true');
        $countriesData = $response->toArray();

        $repo = $this->entityManager->getRepository(Country::class);

        $apiUuids = [];
        foreach ($countriesData as $data) {
            $uuid = $data['cca3'] ?? null;
            if (!$uuid) {
                continue;
            }
            $apiUuids[] = $uuid;
        }

        $existingUuids = $repo->createQueryBuilder('c')
            ->select('c.uuid')
            ->getQuery()
            ->getSingleColumnResult();

        foreach ($countriesData as $data) {
            $uuid = $data['cca3'] ?? null;
            if (!$uuid) {
                continue;
            }

            $country = $repo->find($uuid);

            if (!$country) {
                $country = new Country();
                $country->setUuid($uuid);
            }

            $country->setName($data['name']['common'] ?? '');
            $country->setRegion($data['region'] ?? '');
            $country->setSubRegion($data['subregion'] ?? null);
            $country->setDemonym($data['demonyms']['eng']['m'] ?? $data['demonyms']['eng']['f'] ?? null);
            $country->setPopulation($data['population'] ?? 0);
            $country->setIndependant($data['independent'] ?? false);

            $country->setFlag($data['flags']['png'] ?? null);

            $currency = $country->getCurrency();
            if (isset($data['currencies']) && is_array($data['currencies']) && !empty($data['currencies'])) {
                $firstCurrency = reset($data['currencies']); 
                $currency->setName($firstCurrency['name'] ?? null);
                $currency->setSymbol($firstCurrency['symbol'] ?? null);
            } else {
                $currency->setName(null);
                $currency->setSymbol(null);
            }

            $this->entityManager->persist($country);
        }

        $uuidsToDelete = array_diff($existingUuids, $apiUuids);
        foreach ($uuidsToDelete as $uuid) {
            $country = $repo->find($uuid);
            if ($country) {
                $this->entityManager->remove($country);
            }
        }

        $this->entityManager->flush();
    }
}