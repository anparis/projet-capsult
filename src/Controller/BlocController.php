<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\Lien;

use App\Entity\Image;

use App\Form\BlocType;
use App\Entity\Capsule;
use App\Entity\Connection;
use App\Service\Validation;
use App\Service\EmbedService;
use App\Service\FileUploader;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\ImageRepository;
use Symfony\Component\Form\FormError;
use App\Repository\ConnectionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Route('/bloc')]
class BlocController extends AbstractController
{
  #[Route('/add_bloc/{id}', name: 'capsule_add_bloc',  methods: ['POST'])]
  #[Security("user === capsule.getUser() or capsule.isUserCollaborator(user)")]
  #[IsGranted('ROLE_USER')]
  public function addBloc(Capsule $capsule, Request $request, BlocRepository $br, ImageRepository $ir, LienRepository $lr, ConnectionRepository $cr, Validation $validation, ValidatorInterface $validator, FileUploader $fileUploader, EmbedService $embedService): Response
  {
    $post = $request->request;

    $textarea = $post->get('txt_input');
    /** @var UploadedFile $imgFile */
    $imgFile = $request->files->get('image');

    //get csrf token generated on form
    $submittedToken = $post->get('token');

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
          throw new BadRequestHttpException('Le fichier envoyé n\'est pas une image valide.');
        }
      } elseif ($textarea && !$imgFile) {
        //check if user input is url
        $valideUrl = $validation->validateUrl($textarea, $validator);
        $valideTxt = $validation->validateText($textarea, $validator);

        if ($valideTxt && !$valideUrl) {
          $bloc->setType('Texte');
          $bloc->setContent($textarea);
          $br->save($bloc, true);
        } elseif ($valideUrl) {
          $bloc->setType('Lien');
          $link = new Lien();
          $link->setUrl($textarea);
          $file = $embedService->fetchEmbedData($textarea);
          if (isset($file->thumbnail_url)) {
            $link->setThumb($file->thumbnail_url);
          }
          if (isset($file->html)) {
            $link->setHtml($file->html);
          }
          $link->setBloc($bloc);
          $lr->save($link, true);
        }else{
          throw new BadRequestHttpException('Les données soumises ne sont pas valides.');
        }
      } else {
        $this->addFlash(
          'error',
          'Le formulaire est invalide. Vous ne pouvez utiliser qu\'un seul champ dans ce formulaire.'
        );
        return $this->redirectToRoute(
          'capsule_index',
          [
            'slug_user' => $bloc->getCapsule()->getUser()->getSlug(),
            'slug_capsule' => $bloc->getCapsule()->getSlug()
          ]
        );
      }
    } else {
      throw new BadRequestHttpException('Token CSRF non valide ou soumission du formulaire non détectée.');
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

  #[Route('/{id}', name: 'app_bloc_delete', methods: ['GET', 'POST'])]
  #[Security("is_granted('ROLE_USER')")]
  public function delete(Request $request, Bloc $bloc, BlocRepository $blocRepository): Response
  {
    if ($this->getUser() === $bloc->getCapsule() || $bloc->getCapsule()->isUserCollaborator($this->getUser())) {

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
    } else {
      throw new AccessDeniedHttpException('Accès non autorisé');
    }
  }
}
