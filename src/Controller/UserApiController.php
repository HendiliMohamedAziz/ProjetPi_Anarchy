<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserApiController extends AbstractController
{
    #[Route('/api/Register', name: 'api_Register')]
    public function Register(UserRepository $repository ,ManagerRegistry $doctrine, Request $request,UserPasswordHasherInterface $passwordHasher,SerializerInterface $serializer)
    {   
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return new Response("email invalid.");
        }


        
        $user->setRoles(['ROLE_CLIENT']);
        $user->setApproved(true);
        $user->setIsCoach(false);
        $user->setEmail($email);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setPassword($password);
        try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse("account is created", 200);
        }catch(\Exception $ex){
            return new Response("execption".$ex->getMessage());
        }
    }


    #[Route('/api/login', name: 'api_login')]
    public function Login(Request $request){
        $email = $request->query->get("email");
        $password = $request->query->get("password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if($user){
            if($password==$user->getPassword()){
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            }
            else{
                return new Response("password not found");
            }
        }
        else{
            return new Response("user not found");
        }
    }

    #[Route('/api/editProfile', name: 'api_editProfile')]
    public function editProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $id = $request->get("id");
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $password = $request->query->get("password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if($request->files->get("image")!=null){
            $file = $request->files->get("image");
            $fileName = $file->getClientOriginalName();

            $file->move(
                $fileName
            );
            $user->setImage($fileName);
        }
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setRoles(['ROLE_CLIENT']);
        $user->setApproved(true);
        $user->setIsCoach(false);

        try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return new JsonResponse("Success", 200);
        }catch(\Exception $ex){
            return new Response("fail".$ex->getMessage());
        }
    }


    #[Route('/api/getPasswordByEmail', name: 'api_password')]
    public function getPasswordByEmail(Request $request){

        $email = $request->get('email');
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email'=>$email]);
        if($user){
            $password = $user->getPassword();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($password);
            return new JsonResponse($formatted);
        }
        return new Response("user not found");
    }
}
