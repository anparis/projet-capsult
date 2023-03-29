<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\Lien;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Capsule;
use App\Form\CapsuleType;
use App\Entity\Connection;
use App\Service\Validation;
use App\Service\FileUploader;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\ImageRepository;
use App\Repository\CapsuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CapsuleController extends AbstractController
{
  // #[Route('/{slug_user}/{slug_capsule}', name: 'capsule_index', methods: ['GET'])]
  public function index(string $slug_user, string $slug_capsule, EntityManagerInterface $entityManager, BlocRepository $blocRepository, CapsuleRepository $capsuleRepository): Response
  {
    $capsule = $entityManager->getRepository(Capsule::class)->findOneBy(['slug' => $slug_capsule]);
    $user = $entityManager->getRepository(User::class)->findOneBy(['slug' => $slug_user]);

    $blocCol = $capsule->getBlocs()->isEmpty();
    $connectedBlocCol = $capsule->getConnections()->isEmpty();

    if ($blocCol && $connectedBlocCol) {
      return $this->render('capsule/index.html.twig', [
        'capsule' => $capsule,
        'user' => $user,
        'capsules' => $user->getCapsules(),
        'blocs' => null
      ]);
    } 
    else{
      foreach ($capsule->getConnections() as $connections) {
        //I associate a key with a connected bloc and his connection date
        $blocsConnected[] = ['bloc' => $connections->getBloc(), 'date' => $connections->getCreatedAt()];
      }
      foreach ($capsule->getBlocs() as $bloc) {
        //I associate a key with a bloc and his last updated date
        $blocs[] = ['bloc' => $bloc, 'date' => $bloc->getUpdatedAt()];
      }
      if($connectedBlocCol || $blocCol){
        if($blocCol)
          $allBlocs = $blocsConnected;
        else
          $allBlocs = $blocs;
      }
      else
        //merging the two arrays together
        $allBlocs = array_merge($blocsConnected, $blocs);
    } 

    //sorting the array by descending date
    usort($allBlocs, function ($a, $b) {
      return $a['date'] < $b['date'];
    });

    return $this->render('capsule/index.html.twig', [
      'capsule' => $capsule,
      'user' => $user,
      'capsules' => $user->getCapsules(),
      'blocs' => $allBlocs,
    ]);
  }

  #[Route('/add_bloc/{id}', name: 'capsule_add_bloc', methods: ['POST'])]
  public function addBloc(Capsule $capsule, Request $request, BlocRepository $br, ImageRepository $ir, LienRepository $lr, Validation $validation, ValidatorInterface $validator, FileUploader $fileUploader): Response
  {
    $post = $request->request;

    $textarea = $post->get('txt_input');
    /** @var UploadedFile $imgFile */
    $imgFile = $request->files->get('image');

    //get csrf token generated on form
    $submittedToken = $request->request->get('token');

    if ($this->isCsrfTokenValid('add-bloc', $submittedToken) && $post->has('submit')) {
      $bloc = new Bloc();
      $bloc->setCapsule($capsule);
      if ($imgFile && !$textarea) {
        //calling validation service to check if user upload is an image 
        $imgViolation = $validation->validateImage($imgFile, $validator);
        if ($imgViolation) {
          return new Response(
            '<h1>not an Image</h1>'
          );
        } else {
          $bloc->setType('Image');
          $img = new Image();
          $fileName = $fileUploader->upload($imgFile);
          $img->setNomFichier($fileName);
          $img->setTypeFichier($imgFile->getClientOriginalExtension());
          $img->setBloc($bloc);
          $ir->save($img, true);
          return $this->redirectToRoute(
            'capsule_index',
            [
              'slug_user' => $bloc->getCapsule()->getUser()->getSlug(),
              'slug_capsule' => $bloc->getCapsule()->getSlug()
            ]
          );
        }
      }
      if ($textarea && !$imgFile) {

        //calling validation service to check if user input is a url
        $urlViolation = $validation->validateUrl($textarea, $validator);

        if ($urlViolation) {
          $bloc->setType('Texte');
          $bloc->setContenu($textarea);
          $br->save($bloc, true);
          return $this->redirectToRoute(
            'capsule_index',
            [
              'slug_user' => $bloc->getCapsule()->getUser()->getSlug(),
              'slug_capsule' => $bloc->getCapsule()->getSlug()
            ]
          );
        } else {
          $bloc->setType('Lien');
          $link = new Lien();
          $link->setUrl($textarea);
          $link->setBloc($bloc);
          $lr->save($link, true);

          // $knpSnappyImage->generate($safeTextArea, 'uploads/bloc_img/test.png');

          return $this->redirectToRoute(
            'capsule_index',
            [
              'slug_user' => $bloc->getCapsule()->getUser()->getSlug(),
              'slug_capsule' => $bloc->getCapsule()->getSlug()
            ]
          );
        }
      }
      // user can't submit textarea and upload file at the same time
      return $this->redirectToRoute(
        'capsule_index',
        [
          'slug_user' => $bloc->getCapsule()->getUser()->getSlug(),
          'slug_capsule' => $bloc->getCapsule()->getSlug()
        ]
      );
    } else {
      return new Response(
        'Erreur de formulaire'
      );
    }
  }

  #[Route('/{user_slug}/{capsule_slug}/{id}/capsules-connection', name: 'capsules_connection', methods: ['GET'])]
  #[ParamConverter('bloc', options: ['mapping' => ['id' => 'id']])]
  #[ParamConverter('user', options: ['mapping' => ['user_slug' => 'slug']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_slug' => 'slug']])]
  public function userConnections(Bloc $bloc, User $user, Capsule $capsule, EntityManagerInterface $entityManager): Response
  {
    // $bloc = $entityManager->getRepository(Bloc::class)->find($bloc->getId());

    return $this->render('connection/index.html.twig', [
      'capsules' => $user->getCapsules(),
      'current_capsule' => $capsule,
      'bloc' => $bloc,
      'user' => $user
    ]);
  }


  #[Route('/{id}/edit', name: 'capsule_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, Capsule $capsule, CapsuleRepository $capsuleRepository): Response
  {
    $form = $this->createForm(CapsuleType::class, $capsule);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $capsuleRepository->save($capsule, true);

      return $this->redirectToRoute(
        'capsule_index',
        [
          'slug_user' => $capsule->getUser()->getSlug(),
          'slug_capsule' => $capsule->getSlug()
        ]
      );
    }

    return $this->render('capsule/edit.html.twig', [
      'capsule' => $capsule,
      'form' => $form,
    ]);
  }

  // #[Route('/{id}', name: 'app_capsule_delete', methods: ['POST'])]
  // public function delete(Request $request, Capsule $capsule, CapsuleRepository $capsuleRepository): Response
  // {
  //     if ($this->isCsrfTokenValid('delete'.$capsule->getId(), $request->request->get('_token'))) {
  //         $capsuleRepository->remove($capsule, true);
  //     }

  //     return $this->redirectToRoute('app_capsule_index', [], Response::HTTP_SEE_OTHER);
  // }
}
