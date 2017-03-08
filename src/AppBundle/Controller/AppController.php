<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;
use AppBundle\Repository;
use Symfony\Component\HttpFoundation\Session;

class AppController extends Controller
{

    public function getCLEACSLAction(Request $request)
    {
//        $teacherId = $request->getSession()->get('registeredId');
        $teacherId = '256343' ;

    	$groupDefaultSelection = 'Veuillez sélectionner un groupe.';
    	$ressourceDefaultSelection = 'Sélectionnez une ressource.';
    	$ressourceSelection = 'disabled';
    	$stagiaireDefaultSelection = 'Sélectionnez des stagiaires.';
    	$stagiaireSelection = 'disabled';

    	$dataManager = $this->get('app.repository');
        $groups = $dataManager->findGroupsByTeacher($teacherId);
        $role = $dataManager->findUserRole($teacherId);

        return $this->render('AppBundle:default:clea_csl.html.twig',array(
        	'groups' => $groups,
        	'teacherId' => $teacherId,
            'role' => $role,
        	'groupDefaultSelection' => $groupDefaultSelection,
            'ressourceDefaultSelection' => $ressourceDefaultSelection,
            'ressourceSelection'=> $ressourceSelection,
            'stagiaireDefaultSelection' => $stagiaireDefaultSelection,
            'stagiaireSelection'=> $stagiaireSelection,
    	));
    }

    //Generate PDF1
    public function getPDFResEvalAction(Request $request,$studentId,$isCheckbox1Checked,$isCheckbox2Checked,$ressourceId){

        //Get student infos based on the student id
        $dataManager = $this->get('app.repository');
        $studentInfos = $dataManager->findStudentInfos($studentId);

        $arrayDomainSubDomain = self::getDSDS($ressourceId,$studentId);

        $timestamp = $arrayDomainSubDomain[1];
        if ($timestamp > 0){
            $date = date('dmY', intval($timestamp)) ;
            $name ='ResultatEvaluation'.'_'.$studentInfos[0]["lastName"].$studentInfos[0]["firstName"].'_'.$date;
        }else{
            $name ='ResultatEvaluation'.'_'.$studentInfos[0]["lastName"].$studentInfos[0]["firstName"].'_NOTIME';
        }


        //Prepare HTML view, TWIG to HTML
        $preview = $this->renderView('AppBundle:default:clea_pdf_res_eval.html.twig',array(
                'studentInfos' => $studentInfos,
                'arrayDomainSubDomain' => $arrayDomainSubDomain,
                'isCheckbox1Checked' => $isCheckbox1Checked,
                'isCheckbox2Checked' => $isCheckbox2Checked,
                'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
                'arrayColorDomain' => $this->container->getParameter('colorDomain'),
                'date' => $timestamp
            )
        );

        return $this->genPdfFromHtml($name, $preview);
//        code pour debugger afficher du Debug dans le twig
//                return $html = $this->render('AppBundle:default:clea_pdf_res_eval.html.twig',array(
//                'studentInfos' => $studentInfos,
//                'arrayDomainSubDomain' => $arrayDomainSubDomain,
//                'isCheckbox1Checked' => $isCheckbox1Checked,
//                'isCheckbox2Checked' => $isCheckbox2Checked,
//                 'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
//                'arrayColorDomain' => $this->container->getParameter('colorDomain'),
//                'date' => $timestamp
//            )
//        );
    }

    //Generate PDF2
    public function getPDFMatriceOfAction(Request $request,$studentId,$isCheckbox1Checked,$isCheckbox2Checked,$ressourceId){

        //Get student infos based on the student id
        $dataManager = $this->get('app.repository');
        $studentInfos = $dataManager->findStudentInfos($studentId);

        $arrayDomainSubDomain = self::getDSDS($ressourceId,$studentId);
        
        $timestamp = $arrayDomainSubDomain[1];
        if ($timestamp > 0){
            $date = date('dmY', intval($timestamp)) ;
            $name ='MatriceOf'.'_'.$studentInfos[0]["lastName"].$studentInfos[0]["firstName"].'_'.$date;
        }else{
            $name ='MatriceOf'.'_'.$studentInfos[0]["lastName"].$studentInfos[0]["firstName"].'_NOTIME';
        }

        //Prepare HTML view, TWIG to HTML
        $preview = $this->renderView('AppBundle:default:clea_pdf_matrice_of.html.twig',array(
                'studentInfos' => $studentInfos,
                'arrayDomainSubDomain' => $arrayDomainSubDomain,
                'isCheckbox1Checked' => $isCheckbox1Checked,
                'isCheckbox2Checked' => $isCheckbox2Checked,
                'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
                'arrayColorDomain' => $this->container->getParameter('colorDomain'),
                'date' => $timestamp
            )
        );

        return $this->genPdfFromHtml($name, $preview);
    }

    //Generate PDF3
    public function getPDFMatriceCandiAction(Request $request,$studentId,$isCheckbox1Checked,$isCheckbox2Checked,$ressourceId){
        //Get student infos based on the student id
        $dataManager = $this->get('app.repository');
        $studentInfos = $dataManager->findStudentInfos($studentId);

        $arrayDomainSubDomain = self::getDSDS($ressourceId,$studentId);

        $timestamp = $arrayDomainSubDomain[1];
        if ($timestamp > 0){
            $date = date('dmY', intval($timestamp)) ;
            $name ='MatriceCandidat'.'_'.$studentInfos[0]["lastName"].$studentInfos[0]["firstName"].'_'.$date;
        }else{
            $name ='MatriceCandidat'.'_'.$studentInfos[0]["lastName"].$studentInfos[0]["firstName"].'_NOTIME';
        }

        //Prepare HTML view, TWIG to HTML
        $preview = $this->renderView('AppBundle:default:clea_pdf_matrice_candi.html.twig',array(
                'studentInfos' => $studentInfos,
                'arrayDomainSubDomain' => $arrayDomainSubDomain,
                'isCheckbox1Checked' => $isCheckbox1Checked,
                'isCheckbox2Checked' => $isCheckbox2Checked,
                'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
                'arrayColorDomain' => $this->container->getParameter('colorDomain'),
                'date' => $timestamp
            )
        );

        return $this->genPdfFromHtml($name, $preview);
    }

    //Generate PDF4
    public function getPDFCombinedAction(Request $request,$studentId,$isCheckbox1Checked,$isCheckbox2Checked,$ressourceId){
        //Get student infos based on the student id
        $dataManager = $this->get('app.repository');
        $studentInfos = $dataManager->findStudentInfos($studentId);

        $arrayDomainSubDomain = self::getDSDS($ressourceId,$studentId);

        $timestamp = $arrayDomainSubDomain[1];
        if ($timestamp > 0){
            $date = date('dmY', intval($timestamp)) ;
            $name ='RapportConcaténé'.'_'.$studentInfos[0]["lastName"].$studentInfos[0]["firstName"].'_'.$date;
        }else{
            $name ='RapportConcaténé'.'_'.$studentInfos[0]["lastName"].$studentInfos[0]["firstName"].'_NOTIME';
        }

        //Prepare HTML view, TWIG to HTML
        $preview = $this->renderView('AppBundle:default:clea_pdf_tout.html.twig',array(
                'studentInfos' => $studentInfos,
                'arrayDomainSubDomain' => $arrayDomainSubDomain,
                'isCheckbox1Checked' => $isCheckbox1Checked,
                'isCheckbox2Checked' => $isCheckbox2Checked,
                'base_dir' => $this->get('kernel')->getRootDir() . '/../web' . $request->getBasePath(),
                'arrayColorDomain' => $this->container->getParameter('colorDomain'),
                'date' => $timestamp
            )
        );

        return $this->genPdfFromHtml($name, $preview);
    }

    //Generate a PDF from Te Twig
    public function genPdfFromHtml($filename, $html){

        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('encoding', 'UTF-8');

        //HTML to PDF conversion
        return new Response(
            $snappy->getOutputFromHtml($html,array(
                    'orientation'=>'Landscape'
                )
            ),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachement; filename="'.$filename.'.pdf"'
            )
        );
    }

    //Get Domain SubDomain and Skill (DSDS) associated to a student for a particular ressource
    public function getDSDS($ressourceId,$studentId){

        $dataManager = $this->get('app.repository');

        $course = $dataManager->findCourseIdBy($ressourceId); //GET ID FROM CID

        //Get all tasks and there infos duplicated by depth (taskId --infos-- Depth = 1 (domain) , taskId --infos-- Depth = 2 (subdomain), taskId --infos-- Depth = 3 (skill))
        //infos = ( score, scoreMax )
        $skills = $dataManager->findAllTasksWithDSDSByCourseAndStudent($course[0]['id'],$studentId);

        //this array will store taskId and  infos without duplicate (taskId --infos-- (domain)(subdomain)(skill)
        $arrayTaskId = array();

        $arrayDSDS = array();

        //State1: here we merge duplicate lines
        //we create an index to keep in mind the order of the elements
        $index = 0;
        $timestamp = "";
        foreach ($skills as $element) {
            $taskId = $element['taskId'];
            $arrayTaskId[$taskId]['score'] = $element['automatedScore'];
            $arrayTaskId[$taskId]['scoreMax'] = $element['totalScore'];
            $arrayTaskId[$taskId]['index'] = $index;

            if (isset($element["submittedTime"]) && $timestamp == "") {
                $timestamp = substr($element["submittedTime"], 0, -3);
            }

            if ($element['depth'] == 1) {
                $arrayTaskId[$taskId]['domain'] = $element['description'];
            } elseif ($element['depth'] == 2){
                $arrayTaskId[$taskId]['subDomain'] = $element['description'];

            } elseif ($element['depth'] == 3){
                $arrayTaskId[$taskId]['skill'] = $element['description'];
                $index++;
            }
        }

        //$arrayTaskId is not sorted by pedagogicalId
        //we use the index to sort the array
        $arrayOrdered = array();
        foreach ($arrayTaskId as $element) {
            $arrayOrdered[$element['index']] = $element;
        }

        //we sort all elements
        ksort($arrayOrdered);
        $arrayDSDS = self::createStructureArrayDSDS($arrayOrdered);
        $arrayDSDS = self::getArraySkillsSucceed($arrayTaskId,$arrayDSDS);
        $arrayDSDS = self::getArrayDSDSucceed($arrayDSDS);

        return array($arrayDSDS,intval($timestamp));
    }

    //Get the ordered array of tasks and create the structure of the $arrayDSDS ( domain, subdomain, skill)
    public function createStructureArrayDSDS($arrayOrdered)
    {
        //array use to know if the domain/subdomain/skill already exist
        $arrayDomain = array();
        $arraySubdomain = array();
        $arraySkill = array();
        //we push all domain in the arrayDSDS ( Domain, SubDomain, Skill)
//        var_dump(count($arrayOrdered));
        foreach($arrayOrdered as $element){
//            var_dump($element);
//            die();
            if(!in_array($element['domain'],$arrayDomain)) {
                $arrayDomain[] = $element['domain'];
                $arrayDSDS = array_fill_keys($arrayDomain,array());
            }
        }
        //we push all subdomain in the arrayDSDS ( Domain, SubDomain, Skill)
        foreach($arrayOrdered as $element){

            if(!in_array($element['subDomain'],$arraySubdomain)) {
                $arraySubdomain[$element['domain']][] = $element['subDomain'];
                $arrayDSDS[$element['domain']] = array_fill_keys($arraySubdomain[$element['domain']],array());
            }
        }
        //we push all skill with infos in the arrayDSDS ( Domain, SubDomain, Skill)
        foreach($arrayOrdered as $element){
            if(!in_array($element['skill'],$arraySkill)) {
                $arraySkill[$element['domain']][$element['subDomain']][] = $element['skill'];
                $arrayDSDS[$element['domain']][$element['subDomain']] = array_fill_keys($arraySkill[$element['domain']][$element['subDomain']],array(
                    'score'=>0,
                    'scoreMax'=>0,
                    'succeed'=>false
                ));
            }
        }
        return $arrayDSDS;
    }

    /* Get an array $arrayTaskId with all task and infos
     * $arrayDSDS get all cumulate score and an info succeed
     * (true/false the skill is succeed or not, NO_DATA there is no data in a task )
     */
    public function getArraySkillsSucceed($arrayTaskId,$arrayDSDS)
    {
        //Calculate if the Skill is valid ?
        foreach($arrayTaskId as $element) {

            $domain = $element['domain'];
            $subdomain = $element['subDomain'];
            $skill = $element['skill'];
            $scoreNoScore = false;

            //calculate score
            if ($element['score'] == null) {
                $arrayDSDS[$domain][$subdomain][$skill]['succeed'] = 'NO_DATA';
                $scoreNoScore = true;
            }
            if ($element['score'] != null && !$scoreNoScore) {
                $arrayDSDS[$domain][$subdomain][$skill]['score'] += $element['score'];
            }

            //calculate scoreMax
            $arrayDSDS[$domain][$subdomain][$skill]['scoreMax'] += $element['scoreMax'];

            $score = $arrayDSDS[$domain][$subdomain][$skill]['score'];
            $scoreMax = $arrayDSDS[$domain][$subdomain][$skill]['scoreMax'];

            if ($scoreMax != 0 && !$scoreNoScore) {
                if (($score * 100 / $scoreMax) >= $this->container->getParameter('skillAccepted')) {
                    $arrayDSDS[$domain][$subdomain][$skill]['succeed'] = true;
                } else {
                    $arrayDSDS[$domain][$subdomain][$skill]['succeed'] = false;
                }
            } else {
                $arrayDSDS[$domain][$subdomain][$skill]['succeed'] = 'NO_DATA';
            }

        }
        return $arrayDSDS;
    }

    /* Get $arrayDSDS an indicate if the domain or subdomain is succeeded
     * (true/false the domain/subdomain is succeed or not, NO_DATA there is no data )
     */
    public function getArrayDSDSucceed($arrayDSDS)
    {
        //Calculate if the Subdomain and Domain is valid ?
        foreach(array($arrayDSDS) as $key => $domain) {
            foreach ($domain as $key => $subdomain) {
                $tempoDomain = $key;

                $cptSubDomains = 0;
                $cptSubDomainsSucceed = 0;
                $subdomain_no_data = false;
                $cptSubDomainScore = 0;

                foreach ($subdomain as $key => $skill) {
                    $cptSubDomains++;
                    $tempoSubDomain = $key;
                    $cptSkills = 0;
                    $cptSkillsScore = 0;
                    $skill_no_data = false;
                    foreach ($skill as $key => $infos) {
                        if($infos['succeed'] !== 'NO_DATA'){
                            $cptSkillsScore += $infos['score'] / $infos['scoreMax'];
                        }
                        if($infos['succeed'] === 'NO_DATA'){
                            $skill_no_data = true;
                        }
                        $cptSkills++;
                    }

                    if(!$skill_no_data) {
                        $arrayDSDS[$tempoDomain][$tempoSubDomain]['score'] = $cptSkillsScore / $cptSkills * 100;
                        $cptSubDomainScore += $cptSkillsScore / $cptSkills * 100;
                        $arrayDSDS[$tempoDomain][$tempoSubDomain]['succeedSubDomain'] = true;
                    } else {
                        $arrayDSDS[$tempoDomain][$tempoSubDomain]['succeedSubDomain'] = 'NO_DATA';
                        $arrayDSDS[$tempoDomain][$tempoSubDomain]['score'] = 0;
                        $subdomain_no_data = true;
                    }
                }

                if(!$subdomain_no_data){
                    $arrayDSDS[$tempoDomain]['succeedDomain']['value'] = true;
                    $arrayDSDS[$tempoDomain]['succeedDomain']['score'] = $cptSubDomainScore / $cptSubDomains;
                } else {
                    $arrayDSDS[$tempoDomain]['succeedDomain']['value'] = 'NO_DATA';
                    $arrayDSDS[$tempoDomain]['succeedDomain']['score'] = 0;
                }
            }
        }
        return $arrayDSDS;
    }
}
