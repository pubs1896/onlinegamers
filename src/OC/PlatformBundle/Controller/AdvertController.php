<?php
// Crée par Blackperl1896 progfacil.com
namespace OC\PlatformBundle\Controller;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Team;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\Entity\Friends;
use OC\PlatformBundle\Entity\Comment;
use OC\PlatformBundle\Repository\AdvertRepository;
use OC\UserBundle\Entity\Messages;
use OC\PlatformBundle\Form\CommentType;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\CkeditorType;
use OC\PlatformBundle\Form\MessageType;
use OC\PlatformBundle\Form\TeamType;
use OC\PlatformBundle\Form\DefiType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Dwr\AvatarBundle\Model\AvatarFactory;
use Dwr\AvatarBundle\Model\PlainAvatar;
use Dwr\AvatarBundle\Model\ProfileAvatar;
use OC\UserBundle\Entity\User;
use OC\UserBundle\Entity\Likes;

class AdvertController extends Controller
{
  public function indexAction($page)
  {
	// Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
    $nbPerPage = 5; // la pagination fonctionne---------------------------èàç-rè"'-('ç"_è(-àé"'àç__çèàç-_ç))
	// Pour récupérer la liste de tous les utilisateurs
    $countuser = $userManager->findUsers();
  
    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($countuser) / $nbPerPage);
	  
    // affichage du top 5
	$userss = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\User')
      ->getUsers($page, 5)
    ;	
    
    $userstat = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\User')
      ->findBy(array('console' => 'playstation'), array('points' => 'desc'),5,0)
    ;
    
    $userstatcomputer = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\User')
      ->findBy(array('console' => 'computer'), array('points' => 'desc'),5,0)
    ;
    
    $userstatxbox = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\User')
      ->findBy(array('console' => 'xbox'), array('points' => 'desc'),5,0)
    ;

      
	$title = $page;
    $nbPerPage = 3;
    // On récupère notre objet Paginator
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
    ; 
    $actu = array('cat' => 'actualité');
    $listAdverts = $listAdverts->getAdvertWithCategories($actu);

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts) / $nbPerPage);
    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }
	  
	// Pour les meta tags et titre - - ----------
	$metas = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->findOneBy(array(), array('id' => 'desc'))
    ;
	  
	$index = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->findOneBy(array('id' => 1), array('id' => 'desc'))
    ;	

      
	$time = date('Y-m-d H:i:s');
	// La liste des défis a venir 
	$defis = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\Messages')
      ->getDefis($time)
    ;
      
    // La liste des défis en direct 
	$defidirects = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\Messages')
      ->getDefisdirect($time)
    ;
      
    // copier le code si dessus et crée un autre repository ex "getDefidirect"--((((((((((((((()))))))))))))))
	  
	$wait = array();
	foreach ($defis as $defi) {
		// permet de calculer la difference de date
		$now = new \DateTime(); 
		$after= $defi->getDate();
		$now->diff($after, true)->i;
		$now->diff($after, true)->s;
		
		$showtime = $now->diff($after, true)->i . ' : ' . $now->diff($after, true)->s;
		
		$defi->setDate($showtime);
	}
	  
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page,
      'metatag'     => $metas,
	  'index'		=> $index,
	  'defis'		=> $defis,
	  'wait'		=> $wait,
      'users'       => $userss,
      'userstat'    => $userstat,
      'userstatcomputer'    => $userstatcomputer,
      'userstatxbox'    => $userstatxbox,
      'defidirects' => $defidirects,
    ));
  }  
	
  public function pagegamerAction($page, $p)
  {
	$title = $page;
    $nbPerPage = 3;
	$current = $p;
	// Récupération des AdvertSkill de l'annonce
    $listAdvertSkills = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Category')
      ->findAll()
    ;	
	  	  
	$gamersc = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->findBy(array('author' => $page), array('id' => 'desc'))
	  
    ;
    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($gamersc) / $nbPerPage);
	  
	$gamers = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->findBy(array('author' => $page), array('id' => 'desc'),$nbPages, $p)
	  
    ;
 	
    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }
	  
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:pagegamer.html.twig', array(
      'listAdvertSkills' => $listAdvertSkills,
      'nbPages'     => $nbPages,
      'page'     => $page,
	  'gamers'		=> $gamers,
	  'current'		=> $current,
    ));
  }
  public function commentAction(Request $request, $id) {
  	
    $advertid = new  Comment();
    $form   = $this->get('form.factory')->create(CommentType::class, $advertid);
	
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
	  $advertid->setAdvertid(8);
      $em->persist($advertid);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Commentaire envoyé.');
    }
    return $this->render('OCPlatformBundle:Advert:comment.html.twig', array(
      'form' => $form->createView(),
    ));
  }
	
  public function categoryAction($name)
  {
	  
	$adverts = $this
	  ->getDoctrine()
	  ->getManager()
	  ->getRepository('OCPlatformBundle:Advert')
	;
	$graphisme = array('cat' => $name);
	  
	$adverts = $adverts->getAdvertWithCategories($graphisme);
	  
	// Récupération des AdvertSkill de l'annonce
    $listAdvertSkills = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Category')
      ->findAll()
    ;
    return $this->render('OCPlatformBundle:Advert:categories.html.twig', array(
      'adverts'           => $adverts,
      'listAdvertSkills'           => $listAdvertSkills,
    ));
  }
	
  public function translationAction($name)
  {
	// On récupère le service translator
	$translator = $this->get('translator');
	// Pour traduire dans la locale de l'utilisateur :
	$texteTraduit = $translator->trans('Mon message à inscrire dans les logs');
    return $this->render('OCPlatformBundle:Advert:translation.html.twig', array(
      'name' => $name
    ));
  }
  
  public function mentionAction()
  {
    return $this->render('OCPlatformBundle:Advert:mention-legal.html.twig', array(
    ));
  }	
	
   /**
   * @Security("has_role('ROLE_ADMIN')")
   */
  public function adminAction($page)
  {
    // verifi si le visiteur est connecter sinon sa renvoi à la page /login
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
	// Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
	  
	// Pour récupérer la liste de tous les utilisateurs
    $users = $userManager->findUsers();
    $nbPerPage = 6; // la pagination fonctionne---------------------------èàç-rè"'-('ç"_è(-àé"'àç__çèàç-_ç))
	  
    $comments = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Comment')
      ->getPagination($page, $nbPerPage)
    ;
      
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;
      
    $nbPages = ceil(count($comments) / $nbPerPage);
      
    $userscount = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\User')
      ->getUsers($page, $nbPerPage)
    ;
    $userscount = ceil(count($userscount));
      
    $advertscount = ceil(count($listAdverts));
      
    $time = date('Y-m-d H:i:s');
      // La liste des défis en direct 
    $defidirects = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\Messages')
      ->getDefisall($time)
    ;
    $defiscount = ceil(count($defidirects));
      
    $tickets = $this->getDoctrine()->getRepository('OC\UserBundle\Entity\Messages')->findBy(array('userreceived' => 'admin'));
    $tickets = ceil(count($tickets));
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:admin.html.twig', array(
      'nbPages'     => $nbPages,
      'page'        => $page,
      'users'       => $users,
	  'comments'    => $comments, 
      'userscount'  => $userscount,  
      'advertscount'  => $advertscount,  
      'defiscount'  => $defiscount,
      'tickets'     => $tickets,
    ));
  }  
   /**
   * @Security("has_role('ROLE_ADMIN')")
   */
  public function admincontentAction($page)
  {
    // verifi si le visiteur est connecter sinon sa renvoi à la page /login
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
	// Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
	  
	// Pour récupérer la liste de tous les utilisateurs
    $users = $userManager->findUsers();
    $nbPerPage = 6; // la pagination fonctionne---------------------------èàç-rè"'-('ç"_è(-àé"'àç__çèàç-_ç))
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;
	  
    // Récupération des AdvertSkill de l'annonce
    $comments = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Comment')
      ->findBy(array('published' => 1))
    ;
    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts) / $nbPerPage);
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:admincontent.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page,
      'users'       => $users,
	  'comments'    => $comments, 
    ));
  }    
    
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
  public function adminuserAction($page)
  {
	// Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
    $nbPerPage = 6; // la pagination fonctionne---------------------------èàç-rè"'-('ç"_è(-àé"'àç__çèàç-_ç))
	// Pour récupérer la liste de tous les utilisateurs
    $countuser = $userManager->findUsers();
  
    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($countuser) / $nbPerPage);
	  
	$users = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\User')
      ->getUsers($page, $nbPerPage)
    ;
    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($users) / $nbPerPage);
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:adminuser.html.twig', array(
      'nbPages'     => $nbPages,
      'page'        => $page,
      'users'       => $users,
    ));
  }  
	
  public function alluserAction($page, $p)
  {
	// Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
    $nbPerPage = 6; // la pagination fonctionne---------------------------èàç-rè"'-('ç"_è(-àé"'àç__çèàç-_ç))
	// Pour récupérer la liste de tous les utilisateurs
    $countuser = $userManager->findUsers();
  
    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($countuser) / $nbPerPage);
	  
	$userss = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\User')
      ->getUsers($page, $nbPerPage)
    ;
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:alluser.html.twig', array(
      'nbPages'     => $nbPages,
      'page'        => $page,
      'users'       => $userss,
    ));
  }
    
  public function teamsAction() {
    $bdd = $this->getDoctrine()->getManager();
    $teamviewusersall = $bdd->getRepository('OCPlatformBundle:Advert')->findBy(array('isteam' => true)); 
	  
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:teams.html.twig', array(
        'teamviewusersall' => $teamviewusersall,
    ));
  }

  public function tournamentAction() {
    
    $time = date('Y-m-d H:i:s');
      // La liste des défis en direct 
	  $defidirects = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\Messages')
      ->getDefisall($time)
    ;
      
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:tournament.html.twig', array(
        'defidirects' => $defidirects,
    )); 
  }
	
  public function userAction(Request $request, $user){
	  
  	// Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
	  
    $userp = $user;
	$user=$userManager->finduserBy(array('username' => $user));
	// récupérer l'utilisateur courant
	$useractive=$this->getUser($user);
	$advert = $userManager->findUserBy(array('id' => (int)$user->getId()));
            
    $nbPerPage = 3;
    $page = 1;
  
	$bdd = $this->getDoctrine()->getManager();
	  
    $listAdverts = $bdd->getRepository('OCPlatformBundle:Advert')->getAdverts(1, $nbPerPage);
	  
	$link = $bdd->getRepository('OCPlatformBundle:Friends')->findBy(array('userid' => $user->getId()));
      
	$teamlink = $bdd->getRepository('OCPlatformBundle:Team')->findOneBy(array('userid' => (string)$this->getUser()));
      
	$isteam = $bdd->getRepository('OCPlatformBundle:Team')->findOneBy(array('userid' => $userp));
      
	$myteam = $bdd->getRepository('OCPlatformBundle:Team')->findOneBy(array('userid' => (string)$useractive));
	  
	$linkwaitings = $bdd->getRepository('OCPlatformBundle:Friends')->findBy(array('friendswaitingid' => 3));
	
	$teamview = $bdd->getRepository('OCPlatformBundle:Advert')->findOneBy(array('team' => $user->getUsername()));
	  
	$bioview = $bdd->getRepository('OCPlatformBundle:Advert')->findOneBy(array('slug' => $user->getUsername() . 'bio'));
 
	// Les amis déjà accepter, méthode multi critères utiliser if empty
	$friendsallow = $bdd->getRepository('OCPlatformBundle:Friends')->findBy(array('userid' => $user->getId(), 'friendswaitingid' => 1));
	  
	if(empty($friendsallow)) {
		$friendsallow = $bdd->getRepository('OCPlatformBundle:Friends')->findBy(array('friendsid' => $user->getId(), 'friendswaitingid' => 1));
	}
	  
	if(!empty($myteam)) {
        $myteam = $bdd->getRepository('OCPlatformBundle:advert')->findOneBy(array('slug' => (string)$myteam->getAdvertid()));
    }
	if(empty($teamview)) {
		$teamviewusers = 'ok';
	} else {
		$teamviewusers = $bdd->getRepository('OCPlatformBundle:Team')->findBy(array('advertid' => $teamview->getSlug())); 	 	
	}	
      
	  
	$metas = $bdd
      ->getRepository('OCPlatformBundle:Advert')
      ->findBy(array('author' => $user), array('id' => 'desc'))
    ;
	  
	$messages = $bdd->getRepository('OC\UserBundle\Entity\Messages')->findBy(array('userreceived' => $user->getUsername()));
	  
	$messagesend = $bdd->getRepository('OC\UserBundle\Entity\Messages')->findBy(array('author' => $user->getUsername()));
	 
	// Servira de lien entre le groupe et l'article
	$random = random_bytes(15);
    $random2 = sha1($random);
	  
	$teamc = new Advert();
	$advertlink = new Team();
    $form   = $this->get('form.factory')->create(AdvertType::class, $teamc);
    $this->get('form.factory')->create(TeamType::class, $advertlink);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid() && $user == $useractive) {
		
	  // pour le lien entre le contenue est le groupe
      $em = $this->getDoctrine()->getManager();
	  $advertlink->setUserid($user->getUsername());
	  $advertlink->setGradesid('master');
	  $advertlink->setFriendswaitingid(1);
	  $advertlink->setAdvertid($random2);
	  
	  // contenue du groupe
	  $teamc->setTeam($user);
	  $teamc->setAuthor($user->getUsername());
	  $teamc->setIsteam(true);
	  $teamc->setSlug($random2);
      $em->persist($teamc);
      $em->persist($advertlink);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Team crée.');
	  return $this->redirect($request->getUri());
    }
    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts) / $nbPerPage);

    $linkwaitingsnb = ceil(count($linkwaitings));
	  
	// Le nombre d'amis
	$nbfriends = ceil(count($friendsallow));
      
	$avatar = new AvatarFactory();
    $profileAvatar = $avatar->generate(new ProfileAvatar(140, 140)); 
	$image = 'images/' . $user->getUsername() . 'avatar.jpg';
	if(file_exists($image)) {
		$gravatar = $image;
	} else {
		$gravatar = $profileAvatar->save('', $image);
	} 
	  
	  
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:user.html.twig', array(
      'listAdverts' => $listAdverts,
      'links'       => $link,
      'linkwaitings'=> $linkwaitings,
      'linkwaitingsnb'=> $linkwaitingsnb,
      'friendsallow'=> $friendsallow,
      'nbPages'     => $nbPages,
      'page'        => $page,
      'user'        => $advert,
      'nbfriends'   => $nbfriends,
	  'messages'	=> $messages,
	  'messagesends'	=> $messagesend,
      'profileAvatar'        => $gravatar,
	  'form' 		=> $form->createView(),
	  'teamview'    => $teamview,
	  'teamviewusers' => $teamviewusers,
      'bioview'        => $bioview,
      'teamlink'        => $teamlink,
      'isteam'        => $isteam,
      'myteam'      => $myteam,
    ));
  }
    
  public function rejoinAction(Request $request, $link) {
      // verifi si le visiteur est connecter sinon sa renvoi à la page /login
	  $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      
      $userManager = $this->get('fos_user.user_manager');
      // récupérer l'utilisateur courant
      $user=$this->getUser();
      
      $bdd = $this->getDoctrine()->getManager();
      $teamview = $bdd->getRepository('OCPlatformBundle:Team')->findOneBy(array('userid' => (string)$user->getUsername()));
      
      if(empty($teamview)) {              
          $advertlink = new Team();	  
          // pour le lien entre le contenue est le groupe
          $em = $this->getDoctrine()->getManager();
          $advertlink->setUserid($user);
          $advertlink->setGradesid('member');
          $advertlink->setFriendswaitingid(1);
          $advertlink->setAdvertid($link);
          $em->persist($advertlink);
          $em->flush();
          
          $request->getSession()->getFlashBag()->add('notice', 'Vous avez rejoin la team');
          return $this->redirectToRoute('oc_platform_user', array('user' => (string)$user)); 
      } else {
          $advertlink = new Team();	  
          // pour le lien entre le contenue est le groupe
          $em = $this->getDoctrine()->getManager();
          $advertlink->setUserid($user);
          $advertlink->setGradesid('member');
          $advertlink->setFriendswaitingid(1);
          $advertlink->setAdvertid($link);
          $em->persist($advertlink);
          $em->remove($teamview);
          $em->flush();
          $request->getSession()->getFlashBag()->add('notice', 'Vous avez rejoin une autre team');
          return $this->redirectToRoute('oc_platform_user', array('user' => (string)$user)); 
      }
      
  }
    
  public function likeAction($id) {
    
  	// Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
      
    // récuperer ID de l'utilisateur
    $user = $this->getUser()->getId();
    
    $us = $userManager->findUserBy(array('id' => $id));
      
    $userss = $this->getDoctrine()
      ->getManager()
      ->getRepository('OC\UserBundle\Entity\User')
      ->getAddLike($user, $id)
    ;
      
    return $this->redirectToRoute('oc_platform_user', array('user' => $us));
  }

  // à finir
  public function updateavatarAction() {
    // Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
      
    // récuperer ID de l'utilisateur
    $this->getUser();
    
    $uploaddir = 'images/';
    $uploadfile = $uploaddir . $this->getUser() . "avatar.jpg";
      
    // verification avant envoi
    if(preg_match("/\bimage\b/i", $_FILES['userfile']['type'])) {
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            echo "Le fichier est valide, et a été téléchargé
                   avec succès. Voici plus d'informations :\n";
        } 
        return $this->redirectToRoute('oc_platform_user', array('user' => $us));  
    }
    
    return $this->redirectToRoute('oc_platform_user', array('user' => $us));  
  }
	
	
  public function friendsAction($id) {
  		  
	// verifi si le visiteur est connecter sinon sa renvoi à la page /login
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
  	// Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
	  	  
	// récupérer l'utilisateur courant
	$user=$this->getUser();
	$advert = $userManager->findUserBy(array('id' => $id));
	   
	$bdd = $this->getDoctrine()->getManager();  
	  
	$link = $bdd->getRepository('OCPlatformBundle:Friends')->findBy(array('userid' => $id));  
	$linkwaitings = $bdd->getRepository('OCPlatformBundle:Friends')->findBy(array('userid' => $user->getId(), 'friendswaitingid' => 3));
	// Les amis déjà accepter, méthode multi critères
	$friendsallows = $bdd->getRepository('OCPlatformBundle:Friends')->findBy(array('userid' => $id, 'friendswaitingid' => 1));
	  
	// Pour afficher la liste de tous les amis  
	if(empty($friendsallows)) {
		$friendsallows = $bdd->getRepository('OCPlatformBundle:Friends')->findBy(array('friendsid' => $id, 'friendswaitingid' => 1));
		$friendsallowss = array();
		foreach ($friendsallows as $friendsallow) {
		  $friendsallow->getUserid();
		  $viewuser = $userManager->findUserBy(array('id' => $friendsallow->getUserid()));	
		  array_push($friendsallowss, $viewuser);
		}	
	} else {
		$friendsallowss = array();
		foreach ($friendsallows as $friendsallow) {
		   $friendsallow->getUserid();
		   $viewuser = $userManager->findUserBy(array('id' => $friendsallow->getFriendsid()));	
		   array_push($friendsallowss, $viewuser);
		  
		}
	}
  
	  
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:friends.html.twig', array(
      'links'       => $link,
      'linkwaitings'=> $linkwaitings,
      'friendsallows'=> $friendsallowss,  
	  'user'		 => $advert,
    ));
  }
   /**
   * @Security("has_role('ROLE_ADMIN')")
   */
  public function deleteuserAction($user) {
	  
    // Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
  	// Pour supprimer un utilisateur
	// Pour charger un utilisateur
	$userr = $userManager->findUserBy(array('username' => $user));
	$userManager->deleteUser($userr);
	  
	return $this->redirectToRoute('oc_platform_admin', array());
  }  
  
  public function deletecommentAction(Request $request, $id) {
  		
	// verifi si le visiteur est connecter sinon sa renvoi à la page /login
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
	  
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OCPlatformBundle:Comment')->find($id);
    if (null === $advert) {
      throw new NotFoundHttpException("Le commentaire d'id ".$id." n'existe pas.");
    }else {
      $em->remove($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('info', "Le commentaire a bien été supprimé.");
      return $this->redirectToRoute('oc_platform_admin');
    }
	  
	return $this->redirectToRoute('oc_platform_admin', array());
  }  
	
  public function deletemessageAction(Request $request, $id) {
  		
	// verifi si le visiteur est connecter sinon sa renvoi à la page /login
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
	  
	$userManager = $this->get('fos_user.user_manager');
	  	  
	// récupérer l'utilisateur courant
	$user=$this->getUser();
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OC\UserBundle\Entity\Messages')->find($id);
    if (null === $advert) {
      throw new NotFoundHttpException("Le message d'id ".$id." n'existe pas.");
    }else {
      $em->remove($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('info', "Le message a bien été supprimé.");
      return $this->redirectToRoute('oc_platform_user', array('user' => $user->getUsername()));
    }
	  
	  return $this->redirectToRoute('oc_platform_user', array('user' => $user->getUsername()));
  }
  
  public function searchinguserAction(Request $request) {
	  
	// Pour récupérer le service UserManager du bundle ENFIN çA FONCTIONNE LA RECHERCHE
	$userManager = $this->get('fos_user.user_manager');
	  if(empty($_POST['find'])){
	  	$_POST['find'] = $find;
	  }
	  	  
	  $user = $userManager->findUserBy(
	    array('username' => htmlspecialchars($_POST['find'])), // Critere
	    array('date' => 'desc'),  // Tri
	    50,                       // Limite
	    0                         // debut
	  );
	  
	  if(empty($user)){
		// On ajoute un message flash arbitraire
		$request->getSession()->getFlashBag()->add('info', 'Aucun utilisateur trouvé');
	  	return $this->redirectToRoute('oc_platform_home', array());
	  }
	  
	  if(empty($user)){ 
		 // On ajoute un message flash arbitraire
		$request->getSession()->getFlashBag()->add('info', 'Aucun utilisateur trouvé');
	  	return $this->redirectToRoute('oc_platform_home', array());
	  }
	  
	return $this->render('OCPlatformBundle:Advert:searchinguser.html.twig', array(
      'user'        => $user,
    ));
  }
	
  public function finduserAction($find, Request $request) {
	  
	// Pour récupérer le service UserManager du bundle 
	$userManager = $this->get('fos_user.user_manager');
	  	  
	  $user = $userManager->findUserBy(
	    array('id' => htmlspecialchars($find)), // Critere
	    array('date' => 'desc'),  // Tri
	    50,                       // Limite
	    0                         // debut
	  );
	  
	  if(empty($user)){ 
	    // On ajoute un message flash arbitraire
		$request->getSession()->getFlashBag()->add('info', 'Aucun utilisateur trouvé');
	  	return $this->redirectToRoute('oc_platform_home', array());
	  }
	  
	  return $this->redirectToRoute('oc_platform_user', array('user' => $user->getUsername()));
  }
	
	// A finir pour pouvoir ajouter des amies ---------------------------------------------
	public function addfriendAction(Request $request, $id) {
		
		$em = $this->get('fos_user.user_manager');
		
		// récupérer l'utilisateur courant
		$user=$this->getUser();
		$userc = $em->findUserBy(array('id' => $user->getId()));
		
		// demande d'ajout d'amie
		$friend = $em->findUserBy(array('id' => $id));
	
		$friends = new Friends();
		
		// pour l'entiter friendswaiting 11+=attente, 1=accepter, 0=refuser.
		$friends->setUserid($userc->getId());
		$friends->setFriendsid($friend->getId());		
		$friends->setFriendswaitingid(3);		
		// connection en base de donnée
		$bdd = $this->getDoctrine()->getManager();
		$link = $bdd->getRepository('OCPlatformBundle:Friends')->findBy(array('friendswaitingid' => 3));
		
	    // préparer pour l'envoi en base de donnée
		$bdd->persist($friends);
		// permet de toutes envoyer en base de donnée
		if(3 < $link) {
			$bdd->flush();
			$request->getSession()->getFlashBag()->add('info', "Demande d'amie effectuer.");
			return $this->redirectToRoute('oc_platform_home');	
		}
		
		$request->getSession()->getFlashBag()->add('info', "Demande d'amie déjà effectuer.");
		return $this->redirectToRoute('oc_platform_home');
	}
  public function messageboxAction(Request $request, $id) {
  		// verifi si le visiteur est connecter sinon sa renvoi à la page /login
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
  	// Pour récupérer le service UserManager du bundle
	$userManager = $this->get('fos_user.user_manager');
	  	  
	// récupérer l'utilisateur courant
	$user=$this->getUser();
    $nbPerPage = 3;
    $page = 1;
  
	$bdd = $this->getDoctrine()->getManager();
    $user->getUsername();
	// ajouter des critèrs de séléctions pour la sécurité et afficher un message a la fois
	$messages = $bdd->getRepository('OC\UserBundle\Entity\Messages')->findBy(array('author' => $user->getUsername(), 'id' => $id));
	  
    $nbPages = ceil(count($messages) / $nbPerPage);
	  
    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:messagebox.html.twig', array(
      'nbPages'     => $nbPages,
      'page'        => $page,
	  'messages'	=> $messages,
    ));
  }
	
  // Gestion messagerie interne ----------------------------------------------------------------------------------lll
  public function postprivateAction(Request $request, $id) {
	
	// verifi si le visiteur est connecter sinon sa renvoi à la page /login
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
	 
  	$em = $this->get('fos_user.user_manager');
		
	// récupérer l'utilisateur courant
	$user=$this->getUser();
	
	// changer l'entiter advert pour envoyer dans la bonne table
	$advert = new Messages();
    $form   = $this->get('form.factory')->create(MessageType::class, $advert);
	$form->remove('categories');
	$form->remove('image');
	
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
	  $advert->setUserreceived($id);
	  $advert->setAuthor($this->getUser());
      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('info', 'Message envoyé.');
 	  return $this->redirectToRoute('oc_platform_postprivate', array(
      'form' => $form->createView(),
	  'id'   => $id,
    ));
    }
    return $this->render('OCPlatformBundle:Advert:message.html.twig', array(
      'form' => $form->createView(),
    ));
  
  }  
	
  public function postdefiAction(Request $request, $id) {
	
	// verifi si le visiteur est connecter sinon sa renvoi à la page /login
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
	 
  	$em = $this->get('fos_user.user_manager');
		
	// récupérer l'utilisateur courant
	$user=$this->getUser();
	
	$advert = new Messages();
    $form   = $this->get('form.factory')->create(DefiType::class, $advert);
	$form->remove('image');
	
    if ($request->isMethod('POST') && $form->handleRequest($request)) {
	  $advert->setUserreceived($id);
	  $advert->setAuthor($this->getUser());
	  $advert->setAuthorpoints(1);
      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('info', 'Défi Envoyé.');
 	  return $this->redirectToRoute('oc_platform_postdefi', array(
	  'id'   => $id,
    ));
    }
    return $this->render('OCPlatformBundle:Advert:defi.html.twig', array(
      'form' => $form->createView(),
    ));
  
  }
	
  public function viewAction(Advert $advert, Request $request)
  {
	
    $em = $this->getDoctrine()->getManager();
	$this->get('fos_user.user_manager');		
	// récupérer l'utilisateur courant
	$user=$this->getUser();
	  
    $listApplications = $em
      ->getRepository('OCPlatformBundle:Application')
      ->findBy(array('advert' => $advert))
    ;
    $listAdvertSkills = $em
      ->getRepository('OCPlatformBundle:AdvertSkill')
      ->findBy(array('advert' => $advert))
    ;    
	
    // Pour les meta tags et titre - - ----------
	$metas = $em
      ->getRepository('OCPlatformBundle:Advert')
      ->findOneBy(array('slug' => $advert->getSlug()), array('id' => 'desc'))
    ;
	
		
    $comments = $em
      ->getRepository('OCPlatformBundle:Comment')
      ->findBy(array('title' => $metas->getSlug()))
    ;
	
	$advertid = new  Comment();
    $form   = $this->get('form.factory')->create(CommentType::class, $advertid);	
    if ($request->isMethod('POST') && $form->handleRequest($request)) {
      $em = $this->getDoctrine()->getManager();
	  $advertid->setAdvertid(3);
	  $advertid->setAuthor('anonymous');
	  $advertid->setTitle($metas->getSlug());
		
		
	
      $em->persist($advertid);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Commentaire envoyé.');
	  return $this->redirect($request->getUri());
    }    
	
    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert'           => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills,
	  'metatag'			 => $metas,
	  'comments'		 => $comments,
	  'form' 			 => $form->createView(),
    ));
  }
  /**
   * @Security("has_role('ROLE_AUTEUR')")
   */
  public function addAction(Request $request)
  {
	$this->get('fos_user.user_manager');		
	// récupérer l'utilisateur courant
	$user=$this->getUser();
    $advert = new Advert();
    $form   = $this->get('form.factory')->create(AdvertType::class, $advert);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
	  $advert->setAuthor($user->getUsername());
      $em->persist($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirectToRoute('oc_platform_view', array('slug' => $advert->getSlug()));
    }
    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }  
  public function bioAction(Request $request)
  {
	// verifi si le visiteur est connecter sinon sa renvoi à la page /login
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
	$this->get('fos_user.user_manager');		
	// récupérer l'utilisateur courant
	$user=$this->getUser();
    $advert = new Advert();
    $form   = $this->get('form.factory')->create(AdvertType::class, $advert);
	$form->remove('categories');
	$form->remove('title');
	$form->remove('date');
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
	  $advert->setAuthor($user->getUsername());
	  $advert->setSlug($user->getUsername() . 'bio');
	  $advert->setTitle($user->getUsername() . 'bio');
      $em->persist($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirectToRoute('oc_platform_view', array('slug' => $advert->getSlug()));
    }
    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }  
	
  public function editAction($slug, Request $request)
  {
	  
	$this->get('fos_user.user_manager');		
	// récupérer l'utilisateur courant. Pour donner l'autorisation d'edition qu'a l'editeur de l'article
	$user=$this->getUser();
	  
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OCPlatformBundle:Advert')->findOneBy(array('slug' => $slug));
    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$slug." n'existe pas.");
    }
    $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);
	$advert->getAuthor();
	  
	  
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid() && $user == $advert->getAuthor()) {
      // Inutile de persister ici, Doctrine connait déjà notre annonce
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
      return $this->redirectToRoute('oc_platform_view', array('slug' => $advert->getSlug()));
    }
    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert,
      'form'   => $form->createView(),
    ));
  }
   /**
   * @Security("has_role('ROLE_AUTEUR')")
   */
  public function deleteAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }
    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->get('form.factory')->create();
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em->remove($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");
      return $this->redirectToRoute('oc_platform_home');
    }
    
    return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
      'advert' => $advert,
      'form'   => $form->createView(),
    ));
  }
  public function menuAction($limit)
  {
    $em = $this->getDoctrine()->getManager();
    $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
      array(),                 // Pas de critère
      array('date' => 'desc'), // On trie par date décroissante
      $limit,                  // On sélectionne $limit annonces
      0                        // À partir du premier
    );
    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }
 /* // Méthode facultative pour tester la purge
  public function purgeAction($days, Request $request)
  {
    // On récupère notre service
    $purger = $this->get('oc_platform.purger.advert');
    // On purge les annonces
    $purger->purge($days);
    // On ajoute un message flash arbitraire
    $request->getSession()->getFlashBag()->add('info', 'Les annonces plus vieilles que '.$days.' jours ont été purgées.');
    // On redirige vers la page d'accueil
    return $this->redirectToRoute('oc_platform_home');
  }
  */
}