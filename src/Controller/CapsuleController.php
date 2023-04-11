<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\Lien;
use App\Entity\User;
use App\Entity\Image;
use SimpleXMLElement;
use App\Entity\Capsule;
use App\Form\CapsuleType;
use App\Entity\Connection;
use App\Service\Validation;
use App\Service\FileUploader;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\ImageRepository;
use App\Repository\CapsuleRepository;
use App\Repository\ConnectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CapsuleController extends AbstractController
{
  // #[Route('/{slug_user}/{slug_capsule}', name: 'capsule_index', methods: ['GET'])]
  public function index(string $slug_user, string $slug_capsule, EntityManagerInterface $entityManager, CapsuleRepository $capsuleRepository): Response
  {

    $capsule = $entityManager->getRepository(Capsule::class)->findOneBy(['slug' => $slug_capsule]);
    $user = $entityManager->getRepository(User::class)->findOneBy(['slug' => $slug_user]);

    $connectedBlocCol = $capsule->getConnections()->isEmpty();

    if ($connectedBlocCol) {
      return $this->render('capsule/index.html.twig', [
        'capsule' => $capsule,
        'user' => $user,
        'capsules' => $user->getCapsules(),
        'blocs' => null
      ]);
    } else {
      foreach ($capsule->getConnections() as $connection) {
        //I associate a key with a connected bloc and his connection date
        if ($connection->getBloc()->getCapsule()) {
          if ($connection->getBloc()->getCapsule()->getId() === $connection->getCapsule()->getId())
            $blocsConnected[] = ['bloc' => $connection->getBloc(), 'date' => $connection->getBloc()->getUpdatedAt()];
          else
            $blocsConnected[] = ['bloc' => $connection->getBloc(), 'date' => $connection->getCreatedAt()];
        } else
          $blocsConnected[] = ['bloc' => $connection->getBloc(), 'date' => $connection->getCreatedAt()];
      }
    }

    //sorting the array by descending date
    usort($blocsConnected, function ($a, $b) {
      return $a['date'] < $b['date'];
    });


    return $this->render('capsule/index.html.twig', [
      'capsule' => $capsule,
      'user' => $user,
      'capsules' => $user->getCapsules(),
      'blocs' => $blocsConnected,
      'explorable_capsules' => $capsuleRepository->findExplorableCapsules()
    ]);
  }

  #[Route('/add_bloc/{id}', name: 'capsule_add_bloc', methods: ['POST'])]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
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
        }
      } elseif ($textarea && !$imgFile) {

        //check if user input is url
        $urlViolation = $validation->validateUrl($textarea, $validator);

        // user can't submit textarea and upload file in the same time
        if ($urlViolation) {
          $bloc->setType('Texte');
          $bloc->setContent($textarea);
          $br->save($bloc, true);
        } else {
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

  #[Route('/{user_slug}/{capsule_slug}/{id}/capsules-connection', name: 'capsules_connection', methods: ['GET'])]
  #[ParamConverter('bloc', options: ['mapping' => ['id' => 'id']])]
  #[ParamConverter('user', options: ['mapping' => ['user_slug' => 'slug']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_slug' => 'slug']])]
  public function userConnections(Bloc $bloc, User $user, Capsule $capsule): Response
  {

    return $this->render('connection/index.html.twig', [
      'capsules' => $user->getCapsules(),
      'current_capsule' => $capsule,
      'bloc' => $bloc,
      'user' => $user
    ]);
  }


  #[Route('/{id}/edit', name: 'capsule_edit', methods: ['GET', 'POST'])]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
  public function editCapsule(Request $request, Capsule $capsule, CapsuleRepository $capsuleRepository): Response
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

  // Show the capsules details
  #[Route('/{id}/show', name: 'capsule_show', methods: ['GET', 'POST'])]
  public function showCapsule(Capsule $capsule): Response
  {
    return $this->render(
      'capsule/show.html.twig',
      [
        'capsule' => $capsule,
      ]
    );
  }

  // #[Route('/{slug}/{id}/delete-capsule', name: 'app_capsule_delete', methods: ['POST'])]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
  public function deleteCapsule(Request $request, string $slug, Capsule $capsule, CapsuleRepository $capsuleRepository, ConnectionRepository $connectionRepository): Response
  {
    if ($this->isCsrfTokenValid('delete' . $capsule->getId(), $request->request->get('_token'))) {
      foreach ($capsule->getConnections() as $connection) {
        $connectionRepository->remove($connection, true);
      }
      foreach ($capsule->getBlocs() as $bloc){
        $capsule->removeBloc($bloc);
      }
      $capsuleRepository->remove($capsule, true);
    }

    return $this->redirectToRoute('profile_index', [
      'slug' => $slug
    ]);
  }

  // Update the status of the Capsule (sealed or open)
  #[Route('/status/capsule/{id}', name: 'capsule_status')]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
  public function capsuleStatus(Capsule $capsule, EntityManagerInterface $entityManager): Response
  {
    if ($capsule->isOpen()) {
      $capsule->setStatus('sealed');
      $entityManager->flush();

      return $this->json([
        'message' => 'La capsule est scellée',
        'status_fr' => 'scellée'
      ]);
    }

    $capsule->setStatus('open');
    $entityManager->flush();

    return $this->json([
      'message' => 'La capsule est ouverte',
      'status_fr' => 'ouverte'
    ]);
  }

  // Add or remove the Capsule exploration
  #[Route('/explore/capsule/{id}', name: 'capsule_explore')]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
  public function capsuleExplore(Capsule $capsule, EntityManagerInterface $entityManager): Response
  {
    if ($capsule->getExplore()) {
      $capsule->setExplore(0);
      $entityManager->flush();

      return $this->json([
        'message' => 'La capsule est de retour sur terre U+1F30D'
      ]);
    }

    $capsule->setExplore(1);
    $entityManager->flush();

    return $this->json([
      'message' => 'La capsule est dans l\'espace U+1F680',
    ]);
  }
}
