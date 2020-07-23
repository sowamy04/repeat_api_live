<?php

namespace App\Controller;

use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/regions", name="api_add_region_api", methods={"GET"})
     */
    public function addRegionByApi(SerializerInterface $serializerInterface)
    {
        $regionJson=file_get_contents("https://geo.api.gouv.fr/regions");

       /*  //Décoder les données de du json vers le tableau. On décode puis dénormalise les données: methode 1
        
        $regionTab=$serializerInterface->decode($regionJson,"json");

        $regionObject = $serializerInterface->denormalize($regionTab, 'App\Entity\Region[]'); */

        //Transformation du JSON vers un tableau d'objet : methode 2
        $regionObject = $serializerInterface->deserialize($regionJson,'App\Entity\Region[]','json');
        $entityManager = $this->getDoctrine()->getManager();
        foreach($regionObject as $region){
            $entityManager->persist($region);
        }
        $entityManager->flush();
        
        return new JsonResponse("succes",Response::HTTP_CREATED,[],true);
    }

    /**
     * @Route("/api/regions/list", name="api_all_region", methods={"GET"})
     */
    public function showRegion(SerializerInterface $serializerInterface, RegionRepository $repo)
    {
        $regionsObject=$repo->findAll();
        $regionsJson =$serializerInterface->serialize($regionsObject,"json",
            [
                "groups"=>["region:read_all"]
            ]
        );
        return new JsonResponse($regionsJson,Response::HTTP_OK,[],true);
    }

    /**
    * @Route("/api/regions", name="api_add_region",methods={"POST"})
    */
    public function addRegions(SerializerInterface $serializer, Request $request)
    {
        $regionJson = $request->getContent();
        dd($regionJson);   
    }
}
