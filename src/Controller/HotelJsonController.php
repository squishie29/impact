<?php


namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\ReservationHotel;
use App\Entity\Room;
use App\Entity\Utilisateur;
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
     */

    public function ajouterHotelJson(Request $request,NormalizerInterface $Normalizer)
    {
        $hotel = new ReservationHotel();
        $userId = $request->query->get("user");
        $roomId = $request->query->get("room");
        $debut = $request->query->get("debut");
        $fin = $request->query->get("fin");
        $confirmation = $request->query->get("confirmation");


        $em = $this->getDoctrine()->getManager();
        $debutx = new \DateTime($debut);
        $finx = new \DateTime($fin);
        //dd($debut);
        //$finx=date( "Y-m-d", strtotime( $fin ) );

        $hotel->setDebut($debutx);
        $hotel->setFin($finx);
        $hotel->setConfirmation($confirmation);


        $userx = $em->getRepository(Utilisateur::class)->find($userId);
        $roomx = $em->getRepository(Room::class)->find($roomId);


        //dd($galleriesx) ;
        $hotel->setRoomId($roomx);


        $hotel->setUserId($userx);


        //$em->clear($hotel);
        $em->persist($hotel);
        $em->flush();
        //$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $Normalizer->normalize($hotel, 'json',['groups'=>['reservationH','rooms','user'],"preserve_empty_objects" => true]);

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
        $hotel = $em->getRepository(ReservationHotel::class)->find($id);
        if($hotel!=null ) {
            $em->remove($hotel);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Reservation Hotel deleted bro");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id reservation hotel invalide.");


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
        $hotel = $em->getRepository(ReservationHotel::class)->find($id);
        //dd($hotel->getName());

        $debut = $request->get("debut");
        $fin = $request->get("fin");
        $debutx = new \DateTime($debut);
        $finx = new \DateTime($fin);
        //dd($request->get("id"));


        $hotel->setDebut($debutx);
        $hotel->setFin($finx);
        $hotel->setConfirmation($request->get("confirmation"));





       // $roomx = $em->getRepository(Room::class)->find($rooms);
       // $galleriesx = $em->getRepository(Gallery::class)->find(1);

       // $hotel->addRoom($roomx);
       // $hotel->addGallery($galleriesx);



        $em->persist($hotel);
        $em->flush();
       // $serializer = new Serializer([new ObjectNormalizer()]);
       // $formatted = $serializer->normalize($hotel);
        //$formatted = $Normalizer->normalize($hotel, 'json',['groups'=>'hotels']);
        return new JsonResponse("Reservation Hotel updated");

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



        $hotel = $this->getDoctrine()->getManager()->getRepository(ReservationHotel::class)->findAll();
       // $serializer = new Serializer([new ObjectNormalizer()]);

        $formatted = $Normalizer->normalize($hotel, 'json',['groups'=>['reservationH','rooms','user'],"preserve_empty_objects" => true]);

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

        $hotel = $this->getDoctrine()->getManager()->getRepository(ReservationHotel::class)->find($id);

        $formatted = $Normalizer->normalize($hotel, 'json',['groups'=>['reservationH','rooms','user'],"preserve_empty_objects" => true]);
        return new JsonResponse($formatted);
    }


}