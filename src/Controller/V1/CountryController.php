<?php

declare(strict_types=1);

namespace App\Controller\V1;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/countries')]
#[OA\Tag(name: 'Countries')]
class CountryController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    // GET /api/v1/countries - List all (public)
    #[Route('', methods: ['GET'])]
    #[OA\Get(
        summary: 'List all countries',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of countries',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Country::class)))
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $countries = $this->entityManager->getRepository(Country::class)->findAll();
        return $this->json($countries, 200, [], ['groups' => 'country:read']);
    }

    // GET /api/v1/countries/{uuid} - Get one (public)
    #[Route('/{uuid}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get a country by UUID',
        responses: [
            new OA\Response(response: 200, description: 'Country details', content: new Model(type: Country::class)),
            new OA\Response(response: 404, description: 'Country not found')
        ]
    )]
    public function get(string $uuid): JsonResponse
    {
        $country = $this->entityManager->getRepository(Country::class)->find($uuid);
        if (!$country) {
            return $this->json(['message' => 'Country not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($country, 200, [], ['groups' => 'country:read']);
    }

    // POST /api/v1/countries - Create (secured)
    #[Route('', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create a new country (admin only)',
        security: [new Security(name: 'Basic')],
        requestBody: new OA\RequestBody(content: new Model(type: Country::class)),
        responses: [
            new OA\Response(response: 201, description: 'Country created', content: new Model(type: Country::class)),
            new OA\Response(response: 400, description: 'Invalid data')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['uuid'], $data['name'])) {
            return $this->json(['message' => 'uuid and name are required'], Response::HTTP_BAD_REQUEST);
        }

        $existing = $this->entityManager->getRepository(Country::class)->find($data['uuid']);
        if ($existing) {
            return $this->json(['message' => 'Country with this uuid already exists'], Response::HTTP_CONFLICT);
        }

        $country = new Country();

        $country->setUuid($data['uuid']);
        $country->setName($data['name'] ?? 'Unknown');
        $country->setRegion($data['region'] ?? 'Unknown'); 
        $country->setSubRegion($data['subRegion'] ?? null);
        $country->setDemonym($data['demonym'] ?? null);
        $country->setPopulation($data['population'] ?? 0);
        $country->setIndependant($data['independant'] ?? true); 
        $country->setFlag($data['flag'] ?? null);

        $currency = $country->getCurrency();
        $currency->setName($data['currency']['name'] ?? null);
        $currency->setSymbol($data['currency']['symbol'] ?? null);

        $this->entityManager->persist($country);
        $this->entityManager->flush();

        return $this->json($country, Response::HTTP_CREATED);
    }

    // PATCH /api/v1/countries/{uuid} - Update (secured)
    #[Route('/{uuid}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Update a country (admin only)',
        security: [new Security(name: 'Basic')],
        requestBody: new OA\RequestBody(content: new Model(type: Country::class)),
        responses: [new OA\Response(response: 200, description: 'Country updated')]
    )]
    public function update(string $uuid, Request $request): JsonResponse
    {
        $country = $this->entityManager->getRepository(Country::class)->find($uuid);
        if (!$country) {
            return $this->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $country->setName($data['name']);
        if (isset($data['name'])) $country->setName($data['name']);
        if (isset($data['region'])) $country->setRegion($data['region']);
        if (isset($data['subRegion'])) $country->setSubRegion($data['subRegion']);
        if (isset($data['demonym'])) $country->setDemonym($data['demonym']);
        if (isset($data['population'])) $country->setPopulation($data['population']);
        if (isset($data['independant'])) $country->setIndependant($data['independant']);
        if (isset($data['flag'])) $country->setFlag($data['flag']);

        if (isset($data['currency']['name'])) {
            $country->getCurrency()->setName($data['currency']['name']);
        }
        if (isset($data['currency']['symbol'])) {
            $country->getCurrency()->setSymbol($data['currency']['symbol']);
        }

        $this->entityManager->flush();

        return $this->json($country);
    }

    // DELETE /api/v1/countries/{uuid} - Delete (secured)
    #[Route('/{uuid}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a country (admin only)',
        security: [new Security(name: 'Basic')],
        responses: [new OA\Response(response: 204, description: 'Country deleted')]
    )]
    public function delete(string $uuid): JsonResponse
    {
        $country = $this->entityManager->getRepository(Country::class)->find($uuid);
        if (!$country) {
            return $this->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($country);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}