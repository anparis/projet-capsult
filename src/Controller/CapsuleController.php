<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\Lien;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Capsule;
use App\Form\CapsuleType;
use App\Service\Validation;
use App\Service\FileUploader;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\ImageRepository;
use App\Repository\CapsuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CapsuleController extends AbstractController
{
  #[Route('/{slug_user}/{slug_capsule}', name: 'capsule_index', methods: ['GET'])]
  public function index(string $slug_user, string $slug_capsule, EntityManagerInterface $entityManager, BlocRepository $blocRepository): Response
  {
    $capsule = $entityManager->getRepository(Capsule::class)->findOneBy(['slug' => $slug_capsule]);

    return $this->render('capsule/index.html.twig', [
      'capsule' => $capsule,
      'blocs' => $blocRepository->findBy(['capsule' => $capsule->getId()]),
    ]);
  }

  #[Route('/add/{id}', name: 'capsule_add_bloc', methods: ['POST'])]
  public function addBloc(Capsule $capsule, HtmlSanitizerInterface $htmlSanitizer, Request $request,BlocRepository $br, ImageRepository $ir, LienRepository $lr, Validation $validation, ValidatorInterface $validator, FileUploader $fileUploader): Response
  {
    $post = $request->request;
    /** $textarea */
    $textarea = $post->get('txt_input');
    /** @var UploadedFile $imgFile */
    $imgFile = $request->files->get('image');

    //get token generated on form
    $submittedToken = $request->request->get('token');

    if ($this->isCsrfTokenValid('add-bloc', $submittedToken) && $post->has('submit') && ($imgFile || $textarea)) {

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

          // $imageName = $img->getNomFichier();
          // $fullSizeImgPath = $fileUploader->getTargetDirectory() . '/' . $imageName;
          // $imageOptimizer->resize($fullSizeImgPath);

          //using ImageRepository save method to persist and flush
          $ir->save($img, true);
          return $this->redirectToRoute(
            'capsule_index',
            [
              'slug_user' => 'kentaro-myura',
              'slug_capsule' => 'berserk-fanclub'
            ]
          );
        }
      }
      if ($textarea && !$imgFile) {
        //Sanitize user input
        //Code will not contain any scripts, styles or other elements that can cause the website to behave or look different.
        $safeTextArea = $htmlSanitizer->sanitize($textarea);

        //calling validation service to check if user input is a url
        $urlViolation = $validation->validateUrl($safeTextArea, $validator);

        if ($urlViolation) {
          $bloc->setType('Texte');
          $bloc->setContenu($safeTextArea);
          $br->save($bloc,true);
          return $this->redirectToRoute(
            'capsule_index',
            [
              'slug_user' => 'kentaro-myura',
              'slug_capsule' => 'berserk-fanclub'
            ]
          );
        } else {
          $bloc->setType('Lien');
          $link = new Lien();
          $link->setUrl($safeTextArea);
          $link->setBloc($bloc);
          $lr->save($link, true);

          // $knpSnappyImage->generate($safeTextArea, 'uploads/bloc_img/test.png');

          return $this->redirectToRoute(
            'capsule_index',
            [
              'slug_user' => 'kentaro-myura',
              'slug_capsule' => 'berserk-fanclub'
            ]
          );
        }
      }
      // user can't submit textarea and upload file at the same time
      return $this->redirectToRoute(
        'capsule_index',
        [
          'slug_user' => 'kentaro-myura',
          'slug_capsule' => 'berserk-fanclub'
        ]
      );
    } else {
      return new Response(
        'Erreur de formulaire'
      );
    }
  }

  // #[Route('/add', name: 'app_capsule_add', methods: ['GET', 'POST'])]
  // public function new(Request $request, CapsuleRepository $CapsuleRepository): Response
  // {
  //     $capsule = new Capsule();
  //     $form = $this->createForm(CapsuleType::class, $capsule);
  //     $form->handleRequest($request);

  //     if ($form->isSubmitted() && $form->isValid()) {
  //         $CapsuleRepository->save($capsule, true);

  //         return $this->redirectToRoute('app_Capsule_index', [], Response::HTTP_SEE_OTHER);
  //     }

  //     return $this->render('capsule/add.html.twig', [
  //         'form' => $form,
  //     ]);
  // }

  // #[Route('/{id}', name: 'app_capsule_show', methods: ['GET'])]
  // public function show(Capsule $capsule): Response
  // {
  //     return $this->render('capsule/show.html.twig', [
  //         'capsule' => $capsule,
  //     ]);
  // }

  // #[Route('/{id}/edit', name: 'app_capsule_edit', methods: ['GET', 'POST'])]
  // public function edit(Request $request, Capsule $capsule, CapsuleRepository $capsuleRepository): Response
  // {
  //     $form = $this->createForm(CapsuleType::class, $capsule);
  //     $form->handleRequest($request);

  //     if ($form->isSubmitted() && $form->isValid()) {
  //         $capsuleRepository->save($capsule, true);

  //         return $this->redirectToRoute('app_capsule_index', [], Response::HTTP_SEE_OTHER);
  //     }

  //     return $this->render('capsule/edit.html.twig', [
  //         'capsule' => $capsule,
  //         'form' => $form,
  //     ]);
  // }

  // #[Route('/{id}', name: 'app_capsule_delete', methods: ['POST'])]
  // public function delete(Request $request, Capsule $capsule, CapsuleRepository $capsuleRepository): Response
  // {
  //     if ($this->isCsrfTokenValid('delete'.$capsule->getId(), $request->request->get('_token'))) {
  //         $capsuleRepository->remove($capsule, true);
  //     }

  //     return $this->redirectToRoute('app_capsule_index', [], Response::HTTP_SEE_OTHER);
  // }
}
