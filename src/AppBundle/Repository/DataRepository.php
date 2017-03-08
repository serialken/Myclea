<?php

namespace AppBundle\Repository;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class DataRepository
{
    protected $connexion;
    private $coursesCid;

    // --- Constructor ---
    // Set The connexion : $connexion (DBAL)
    // Set coursesCid : courses_cid (parameters.yml)
    public function __construct(Connection $connexion, $isbn)
    {
        $this->connexion = $connexion;
        $this->isbn = $isbn;
    }

    // --- Function CreateQueryBuilder --- 
    // Create QueryBuilder based on the $connexion
    protected function createQueryBuilder()
    {
        return $this->connexion->createQueryBuilder();
    }

    // --- Function findCourses ---
    // Get Course's with the right isbn
    public function findCourses()
    {

        $queryBuilder = $this->createQueryBuilder();
        return $queryBuilder
            ->select('*')
            ->from('dim_course', 'c')
            ->where('c.isbn = :isbn')
            ->andWhere('c.islatest = 1')//The course is the latest version
            ->setParameter('isbn', $this->isbn, \PDO::PARAM_STR);//The isbn of the course is the good one
//            ->execute()
//            ->fetchAll(\PDO::FETCH_ASSOC);

    }
    // --- Function findGroupsByTeacher --- 
    // Get Group's Teacher Specified (With The id : $id)
    public function findGroupsByTeacher($teacherid)
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('sc.id, sc.name')
            ->from('dim_study_class', 'sc')
            ->innerJoin('sc', 'map_teacher_study_class', 'mts', 'sc.id = mts.studyClassId')
            ->innerJoin('sc', 'map_studyclass_course', 'msc', 'sc.id = msc.studyClassId')
            ->innerJoin('msc', sprintf('(%s)', $this->findCourses()->getSQL()), 'fc', 'msc.courseCid = fc.cid')
            ->where('mts.userId = ' . $queryBuilder->createNamedParameter($teacherid, \PDO::PARAM_INT))
            ->andWhere('mts.relationEnd > NOW()')
            ->andWhere('sc.deleted = "9999-12-12 12:12:12"')
            ->groupBy('sc.id')
            ->orderBy('sc.name')
            ->setParameter('isbn', $this->isbn, \PDO::PARAM_STR)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    // --- Function findRessourcesByGroup ---
    // Get Ressources From Group Specified (With The id : $id)
    public function findRessourcesByGroup($groupid)
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select(
                'c.cid AS courseCid',
                'c.title AS courseTitle'
            )
            ->from('dim_course', 'c')
            ->innerJoin('c', 'map_studyclass_course', 'msc', 'c.cid = msc.courseCid AND msc.studyClassId = :studyClassId AND msc.associated = 1 AND msc.deleted = "9999-12-12 12:12:12"')
            ->Where('c.deleted = "9999-12-12 12:12:12"')
            ->andWhere('c.isLatest = 1')
            ->andwhere('c.isbn = :isbn')
            ->orderBy('c.title')
            ->setParameter('studyClassId', $groupid, \PDO::PARAM_INT)
            ->setParameter('isbn', $this->isbn, \PDO::PARAM_STR)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    // --- Function findStudentsByGroup ---
    // Get Student By Group Specified (With The id : $id)
    public function findStudentsByGroupAndRessource($groupId, $ressourceId)
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('u.id', 'u.firstName', 'u.lastName')
            ->from('dim_user', 'u')
            ->innerJoin('u', 'map_student_course', 'msc', 'u.id = msc.userId')
            ->innerJoin('u', 'map_student_study_class', 'mssc', 'u.id = mssc.userId')
            ->where('msc.courseCid = ' . $queryBuilder->createNamedParameter($ressourceId, \PDO::PARAM_INT))
            ->andwhere('mssc.studyClassId = ' . $queryBuilder->createNamedParameter($groupId, \PDO::PARAM_INT))
            ->andWhere('mssc.relationEnd > NOW()')
            ->andWhere('u.deleted = "9999-12-12 12:12:12"')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }
    // --- Function findCourseIdBy ---
    // Get Id of the course (With The courseId : $cid)
    public function findCourseIdBy($cid)
    {
        $queryBuilder = $this->createQueryBuilder();
        return $queryBuilder
            ->select('c.id')
            ->from('dim_course', 'c')
            ->where('c.cid = :cid')
            ->andWhere('c.isLatest = 1')//Get Last version of the course
            ->andwhere('c.isbn = :isbn')//Get the right ISBN
            ->setParameter('cid', $cid)
            ->setParameter('isbn', $this->isbn, \PDO::PARAM_STR)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Phase1: GET ALL TASKS DONE WITH DSDS BY A STUDENT: DOMAIN(Depth=1),SubDomain(depth = 2),Skills(depth = 3)

    // --- FindAllAssessmentsByCourse ---
    public function findAllAssessmentsByCourse($courseId){
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('assessmentId,da.title as titleAssessment')
            ->from('map_assessment_course', 'mac')
            ->innerJoin('mac', 'dim_assessment', 'da', 'mac.assessmentId = da.id')
            ->where('mac.courseId = :courseId')
            ->setParameter('courseId',$courseId, \PDO::PARAM_STR);
//            ->execute()
//            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    // --- Function findAllTasksByDSDS ---
    public function findAllTasksAssociatedToAssessments($courseId)
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('taskId,title,titleAssessment,totalScore,standardId')
            ->from(sprintf('(%s)', $this->findAllAssessmentsByCourse($courseId)->getSQL()), 'fabc')
            ->innerJoin('fabc', 'map_sequence_assessment', 'msa', 'fabc.assessmentId = msa.assessmentId')
            ->innerJoin('msa', 'dim_assessment_task', 'dat', 'msa.sequenceId = dat.sequenceId')
            ->innerJoin('dat', 'map_assessment_task_standard', 'mats', 'dat.id = mats.taskId')
            ->setParameter('courseId',$courseId, \PDO::PARAM_STR);
//            ->execute()
//            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    // --- Function findAllDSDS ---
    // DSDS: Domain SubDomain Skills
    //Domain = standard (depth = 1)
    //SubDomain = standard (depth = 2)
    //Skill = standard (depth = 3)
    public function findAllDSDS(){
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('id,depth,name,description,pedagogicalId')
            ->from('dim_standard','ds')
            ->where('ds.depth = 1 or ds.depth = 2 or ds.depth = 3')
            ->orderBy('pedagogicalId','ASC');
//            ->execute()
//            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    // --- Function findAllTasksWithDSDSByCourse ---
    public function  findAllTasksWithDSDSByCourse($courseId){
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('taskId,title,titleAssessment,totalScore,depth,name,description,fad.pedagogicalId,standardId')
            ->from(sprintf('(%s)', $this->findAllDSDS()->getSQL()),'fad')
            ->innerJoin('fad', sprintf('(%s)', $this->findAllTasksAssociatedToAssessments($courseId)->getSQL()), 'fatbd', 'fad.id = fatbd.standardId')
            ->setParameter('courseId',$courseId, \PDO::PARAM_STR);
//            ->execute()
//            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    // --- Function findAllTasksByStudent ---
    public function findAllTasksByStudent($studentId)
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('*')
            ->from('fct_student_assessment_session','fsas')
            ->where('fsas.userId = :studentId')
            ->setParameter('studentId',$studentId, \PDO::PARAM_INT);
//            ->execute()
//            ->fetchAll(\PDO::FETCH_ASSOC);
    }
    // --- Function findAllTasksWithDSDSByCourseAndStudent ---
    public function findAllTasksWithDSDSByCourseAndStudent($courseId,$studentId)
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('userId,assessmentId,titleAssessment,fatwd.taskId,automatedScore,totalScore,depth,name,description,pedagogicalId,standardId,fatbs.submittedTime')
            ->from(sprintf('(%s)', $this->findAllTasksByStudent($studentId)->getSQL()),'fatbs')
            ->rightJoin('fatbs', sprintf('(%s)', $this->findAllTasksWithDSDSByCourse($courseId)->getSQL()),'fatwd','fatbs.taskId = fatwd.taskId ')
            ->setParameter('courseId',$courseId, \PDO::PARAM_STR)
            ->setParameter('studentId',$studentId, \PDO::PARAM_INT)
            ->OrderBy('pedagogicalId','ASC')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    // --- Function findStudentInfos ---
    // Get Student Infos (With The sudentId : $studentId)
    public function findStudentInfos($studentId)
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('*')
            ->from('dim_user', 'u')
            ->where('id = :studentId')
            ->setParameter('studentId', $studentId, \PDO::PARAM_INT)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    // --- Function findUserRole ---
    // Get Role (With The userId : $userId)
    public function findUserRole($userId)
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('r.name')
            ->from('dim_role', 'r')
            ->innerJoin('r', 'map_user_role', 'mur', 'r.id = mur.roleId AND mur.userId = :userId')
            ->setParameter('userId', $userId, \PDO::PARAM_INT)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    //--- Get Courses By Study Class Using map_toc_contentitem to get some infos ----
    public function CourseByStudyClass($id)
    {
        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder
            ->select(
                'c.cid AS courseCid',
                'c.title AS courseTitle',
                'a.cid AS assessmentCid',
                'a.title AS assessmentTitle',
                'COUNT(fsas.assessmentCid) AS hasScore'
            )
            ->from('dim_course', 'c')
//            ->innerJoin('c', 'map_studyclass_course', 'msc', 'c.cid = msc.courseCid AND msc.studyClassId = :studyClassId AND msc.associated = 1 AND msc.deleted = "9999-12-12 12:12:12"')
            ->innerJoin('c', 'map_toc_contentitem', 'mtc1', 'c.id = mtc1.courseId AND mtc1.tocDepthLevel = 1 AND mtc1.contentItemType = "assessment"')
            ->innerJoin('mtc1', 'dim_assessment', 'a', 'mtc1.contentItemId = a.id')
            ->leftJoin(
                'a',
                sprintf('(%s)', $this->getResultsQueryBuilder()->getSQL()),
                'fsas',
                'c.cid = fsas.courseCid AND a.cid = fsas.assessmentCid'
            )
            ->where($queryBuilder->expr()->in(
                'c.cid',
                ':coursesCid'
            ))
            ->andWhere('c.isLatest = 1')
            ->andWhere('c.deleted = "9999-12-12 12:12:12"')
            ->groupBy('a.cid')
            ->orderBy('c.title')
            ->addOrderBy('a.title')
            ->setParameter('studyClassId', $id, \PDO::PARAM_INT)
            ->setParameter('coursesCid', $this->coursesCid, Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }
    private function getResultsQueryBuilder()
    {
        return $this->createQueryBuilder()
            ->select('f.studyClassId, c.cid AS courseCid, f.assessmentCid')
            ->from('fct_student_assessment_session', 'f')
            ->innerJoin('f', 'dim_course', 'c', 'f.courseId = c.id AND c.cid IN (:coursesCid)')
            ->where('f.studyClassId = :studyClassId')
            ->groupBy('f.studyClassId, c.cid, f.assessmentCid');
    }





}
