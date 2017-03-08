<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
//use AppBundle\Manager;
use AppBundle\Repository;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Doctrine\DBAL\DriverManager;

class DataController extends Controller
{
    // --- Test ---
    // teacherId : userId (rôle 40 : teacher) 
    // ex : 486 (prof de français)
    public function getGroupsByTeacherAction(Request $request)
    {
        $session = json_decode($request->request->get('session'));
        //var_dump($request->request);
        $teacherId = $session->teacher->id;

        $dataManager = $this->get('app.repository');
        $groups = $dataManager->findGroupsByTeacher($teacherId);

        return new JsonResponse($groups);

        return $response->setData(array('groups'=>$groups));

    }

    // --- Test ---
    // groupId : studyClassId 
    // ex : 14 (6A Français)
    // return list of ressources (courses)
    public function getRessourcesByGroupAction(Request $request)
    {
        $session = json_decode($request->request->get('session'));
        $groupId = $session->group->id;

        $dataManager = $this->get('app.repository');
        $ressources = $dataManager->findRessourcesByGroup($groupId);

        return new JsonResponse($ressources);

        return $response->setData(array('ressources'=>$ressources));
    }

    // --- Test ---
    // groupId : studyClassId 
    // ex : 14 (Français 6e)
    // return list of students (user role student)
    public function getStudentsByGroupAndRessourceAction(Request $request)
    {
        $session = json_decode($request->request->get('session'));
        $groupId = $session->group->id;
        $ressourceId = $session->ressource->id;

        $dataManager = $this->get('app.repository');
        $students = $dataManager->findStudentsByGroupAndRessource($groupId,$ressourceId);

        return new JsonResponse($students);

        return $response->setData(array('students'=>$students));
    }

    // --- Session ---
    public function getSessionAction(Request $request)
    {

        $data = json_decode($request->request->get('data'));

        return new JsonResponse($data);

        return $this->render('clea_csl.html.twig', array(
            'data' => $data,
        ));

    }
}
