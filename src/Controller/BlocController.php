<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\Lien;

use App\Entity\Image;

use App\Form\BlocType;
use App\Entity\Capsule;
use App\Entity\Connection;
use App\Service\Validation;
use App\Service\FileUploader;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\ImageRepository;
use App\Repository\ConnectionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/bloc')]
class BlocController extends AbstractController
{
  #[Route('/', name: 'app_bloc_index')]
  public function blocIndex(BlocRepository $bl): Response
  {
    return $this->render('bloc/index.html.twig', [
      'blocs' => $bl->findAll()
    ]);
  }

  #[Route('/{id}', name: 'app_bloc_show', methods: ['GET'])]
  public function show(Bloc $bloc): Response
  {
    return $this->render('bloc/show.html.twig', [
      'bloc' => $bloc,
    ]);
  }

  #[Route('/add_bloc/{id}', name: 'capsule_add_bloc',  methods: ['GET', 'POST'])]
  #[Security("is_granted('ROLE_USER') and (user === capsule.getUser() or capsule.isUserCollaborator(user))")]
  public function addBloc(Capsule $capsule, Request $request, BlocRepository $br, ImageRepository $ir, LienRepository $lr, ConnectionRepository $cr, Validation $validation, ValidatorInterface $validator, FileUploader $fileUploader): Response
  {
    $post = $request->request;

    $textarea = $post->get('txt_input');
    /** @var UploadedFile $imgFile */
    $imgFile = $request->files->get('image');

    //get csrf token generated on form
    $submittedToken = $request->request->get('token');

    if ($this->isCsrfTokenValid('add-bloc', $submittedToken) && $post->has('submit')) {
      $bloc = new Bloc();
      $bloc->setUser($this->getUser());
      $bloc->setCapsule($capsule);
      if ($imgFile && !$textarea) {
        //calling validation service to check if user upload is an image 
        $valideImg = $validation->validateImage($imgFile, $validator);
        if ($valideImg) {
          $bloc->setType('Image');
          $img = new Image();
          $fileName = $fileUploader->upload($imgFile);
          $img->setNomFichier($fileName);
          $img->setTypeFichier($imgFile->getClientOriginalExtension());
          $img->setBloc($bloc);
          $ir->save($img, true);
        } else {
          return new Response(
            '<h1>not an Image</h1>'
          );
          
        }
      } elseif ($textarea && !$imgFile) {
        //check if user input is url
        $valideUrl = $validation->validateUrl($textarea, $validator);
        $valideTxt = $validation->validateText($textarea, $validator);
        // user can't submit textarea and upload file in the same time
        if ($valideTxt && !$valideUrl) {
          $bloc->setType('Texte');
          $bloc->setContent($textarea);
          $br->save($bloc, true);
        } elseif($valideUrl) {
          $bloc->setType('Lien');
          $link = new Lien();
          $link->setUrl($textarea);
          $file = json_decode(file_get_contents("https://iframe.ly/api/oembed?url=$textarea&api_key=4e6fb13787561fe9d031a0"));
          if(isset($file->thumbnail_url)){
            $link->setThumb($file->thumbnail_url);
          }
          if(isset($file->html)){
            $link->setHtml($file->html);
          }
          $link->setBloc($bloc);
          $lr->save($link, true);
        }
      } else {
        return new Response(
          'Erreur de formulaire'
        );
      }
    } else {
      return new Response(
        'Erreur de formulaire'
      );
    }

    // I finally set a new connection between bloc and capsule
    $connection = new Connection();
    $connection->setBloc($bloc);
    $connection->setCapsule($capsule);
    $cr->save($connection, true);
    return $this->redirectToRoute(
      'capsule_index',
      [
        'slug_user' => $bloc->getCapsule()->getUser()->getSlug(),
        'slug_capsule' => $bloc->getCapsule()->getSlug()
      ]
    );
  }

  /**
   * This controller allow us to edit a bloc
   * @param Bloc $bloc
   * @param Request $resquest
   * @param Capsule $capsule
   * @param BlocRepository $blocRepository
   * @return Response 
  **/
  #[Route('/{bloc_id}/{capsule_slug}/edit', name: 'app_bloc_edit', methods: ['GET', 'POST'])]
  #[ParamConverter('bloc', options: ['mapping' => ['bloc_id' => 'id']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_slug' => 'slug']])]
  #[Security("is_granted('ROLE_USER') and (user === bloc.getCapsule().getUser() or capsule.isUserCollaborator(user))")]
  public function editBloc(Bloc $bloc, Capsule $capsule, Request $request, BlocRepository $blocRepository): Response
  {
    $form = $this->createForm(BlocType::class, $bloc);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $blocRepository->save($bloc, true);
      return $this->redirectToRoute('capsule_index', [
        'slug_capsule' => $capsule->getSlug(),
        'slug_user' => $capsule->getUser()->getSlug()
      ]);
    }

    return $this->render('bloc/edit.html.twig', [
      'bloc' => $bloc,
      'capsule' => $capsule,
      'form' => $form,
    ]);
  }

  /**
   * This controller allow us to display a bloc
   * @param Bloc $bloc
   * @param Capsule $capsule
   * @return Response 
  **/
  #[Route('/{bloc_id}/{capsule_slug}/show', name: 'app_bloc_show', methods: ['GET'])]
  #[ParamConverter('bloc', options: ['mapping' => ['bloc_id' => 'id']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_slug' => 'slug']])]
  public function showBloc(Bloc $bloc, Capsule $capsule): Response
  {
    return $this->render('bloc/show.html.twig', [
      'bloc' => $bloc,
      'capsule' => $capsule
    ]);
  }
  
  #[Route('/{id}', name: 'app_bloc_delete', methods: ['GET','POST'])]
  #[Security("is_granted('ROLE_USER') and (user === bloc.getCapsule().getUser() or bloc.getCapsule().isUserCollaborator(user))")]
  public function delete(Request $request, Bloc $bloc, BlocRepository $blocRepository): Response
  {
    if ($this->isCsrfTokenValid('delete' . $bloc->getId(), $request->request->get('_token'))) {
      $blocRepository->remove($bloc, true);
    }

    return $this->redirectToRoute(
      'capsule_index',
      [
        'slug_user' => $bloc->getCapsule()->getUser()->getSlug(),
        'slug_capsule' => $bloc->getCapsule()->getSlug()
      ]
    );
  }
}
