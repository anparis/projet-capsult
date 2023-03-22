<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\Lien;
use App\Entity\Image;
use App\Form\BlocType;
use App\Service\Validation;
use App\Service\FileUploader;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/bloc')]
class BlocController extends AbstractController
{
  #[Route('/', name: 'app_bloc_index', methods: ['GET'])]
  public function blocIndex(BlocRepository $bl): Response
  {
    return $this->render('bloc/index.html.twig', [
      'blocs' => $bl->findAll()
    ]);
  }

  #[Route('/add', name: 'add_bloc')]
  public function addBloc(HtmlSanitizerInterface $htmlSanitizer, Request $request, ImageRepository $ir, LienRepository $lr, Validation $validation, ValidatorInterface $validator, FileUploader $fileUploader): Response
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
          return $this->redirectToRoute('app_bloc_index');
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
          return $this->redirectToRoute('app_bloc_index');
        } else {
          $bloc->setType('Lien');
          $link = new Lien();
          $link->setUrl($safeTextArea);
          $link->setBloc($bloc);
          $lr->save($link, true);

          // $knpSnappyImage->generate($safeTextArea, 'uploads/bloc_img/test.png');
          
          return $this->redirectToRoute('app_bloc_index');
        }
      }
      // user can't submit textarea and upload file at the same time
      return $this->redirectToRoute('app_bloc_index');
    } else {
      return new Response(
        'Erreur de formulaire'
      );
    }
  }

  #[Route('/{id}', name: 'app_bloc_show', methods: ['GET'])]
  public function show(Bloc $bloc): Response
  {
    return $this->render('bloc/show.html.twig', [
      'bloc' => $bloc,
    ]);
  }

  #[Route('/{id}/edit', name: 'app_bloc_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, Bloc $bloc, BlocRepository $blocRepository): Response
  {
    $form = $this->createForm(BlocType::class, $bloc);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $blocRepository->save($bloc, true);

      return $this->redirectToRoute('app_bloc_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('bloc/edit.html.twig', [
      'bloc' => $bloc,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'app_bloc_delete', methods: ['POST'])]
  public function delete(Request $request, Bloc $bloc, BlocRepository $blocRepository): Response
  {
    if ($this->isCsrfTokenValid('delete' . $bloc->getId(), $request->request->get('_token'))) {
      $blocRepository->remove($bloc, true);
    }

    return $this->redirectToRoute('app_bloc_index', [], Response::HTTP_SEE_OTHER);
  }
}
