<?php


namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Room;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Hotel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Json;

class HotelJsonController extends  AbstractController
{


    /******************Ajouter Hotel*****************************************/
    /**
     * @Route("/addHotelJson", name="add_HotelJson")
     * @Method("POST")
     */

    public function ajouterHotelJson(Request $request,NormalizerInterface $Normalizer)
    {
        $hotel = new Hotel();
        $name = $request->query->get("name");
        $stars = $request->query->get("stars");
        $photo = $request->query->get("photo");
        $description = $request->query->get("description");
        $adress = $request->query->get("adress");
        $rooms = $request->query->get("rooms");
        $galleries = $request->query->get("galleries");

        $em = $this->getDoctrine()->getManager();

        $hotel->setName($name);
        $hotel->setStars($stars);
        $hotel->setPhoto($photo);
        $hotel->setDescription($description);
        $hotel->setAdress($adress);


        $roomx = $em->getRepository(Room::class)->find($rooms);
        $galleriesx = $em->getRepository(Gallery::class)->find(1);

        //dd($galleriesx) ;
        $hotel->addRoom($roomx);


        $hotel->addGallery($galleriesx);


        //$em->clear($hotel);
        $em->persist($hotel);
        $em->flush();
        //$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $Normalizer->normalize($hotel, 'json',['groups'=>['hotels','rooms','galleries'],"preserve_empty_objects" => true]);

        //$formatted = $serializer->normalize($hotel);
        return new JsonResponse($formatted);

    }

    /******************Supprimer Hotel*****************************************/

    /**
     * @Route("/deleteHotelJson", name="delete_HotelJson")
     */

    public function deleteHotelJson(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $hotel = $em->getRepository(Hotel::class)->find($id);
        if($hotel!=null ) {
            $em->remove($hotel);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Hotel deleted bro");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id hotel invalide.");


    }

    /******************Modifier hotel*****************************************/
    /**
     * @Route("/updateHotelJson", name="update_hotelJson", methods={"GET","POST"})
     */
    public function modifierHotelJson(Request $request) {
        $em = $this->getDoctrine()->getManager();
        /*$hotel = $this->getDoctrine()->getManager()
            ->getRepository(Hotel::class)
            ->find($request->get("id"));*/

        //$hotel->setDescription($request->get("description"));
        $id = $request->get("id");
        $hotel = $em->getRepository(Hotel::class)->find($id);
        //dd($hotel->getName());


        $hotel->setName($request->get("name"));
        $hotel->setStars($request->get("stars"));
        $hotel->setPhoto($request->get("photo"));
        $hotel->setDescription($request->get("description"));



       // $roomx = $em->getRepository(Room::class)->find($rooms);
       // $galleriesx = $em->getRepository(Gallery::class)->find(1);

       // $hotel->addRoom($roomx);
       // $hotel->addGallery($galleriesx);



        $em->persist($hotel);
        $em->flush();
       // $serializer = new Serializer([new ObjectNormalizer()]);
       // $formatted = $serializer->normalize($hotel);
        //$formatted = $Normalizer->normalize($hotel, 'json',['groups'=>'hotels']);
        return new JsonResponse("Hotel updated");

    }

   /* public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'nb_personnes' => $this->nb_personnes,
            'description' => $this->getBranches()->toArray(), // <--
        ];
    }*/


    /******************affichage Hotel*****************************************/

    /**
     * @Route("/displayHotelsJson", name="display_hotelsJson")
     */
    public function allHotelsJson(NormalizerInterface $Normalizer)
    {



        $hotel = $this->getDoctrine()->getManager()->getRepository(Hotel::class)->findAll();
       // $serializer = new Serializer([new ObjectNormalizer()]);

        $formatted = $Normalizer->normalize($hotel, 'json',['groups'=>['hotels','rooms','galleries'],"preserve_empty_objects" => true]);

        return new JsonResponse($formatted);

    }


    /******************Detail Hotel*****************************************/

    /**
     * @Route("/detailHotelJson", name="detail_hotelJson")
     * @Method("GET")
     */

    //Detail Hotel
    public function detailHotelJson(Request $request,NormalizerInterface $Normalizer)
    {
        $id = $request->get("id");

        $hotel = $this->getDoctrine()->getManager()->getRepository(Hotel::class)->find($id);

        $formatted = $Normalizer->normalize($hotel, 'json',['groups'=>'hotels']);
        return new JsonResponse($formatted);
    }


}