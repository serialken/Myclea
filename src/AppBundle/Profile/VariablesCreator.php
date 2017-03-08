<?php

namespace AppBundle\Profile;

use AppBundle\Repository\ResultRepository;

class VariablesCreator
{
    private $resultRepository;

    public function __construct(ResultRepository $resultRepository)
    {
        $this->resultRepository = $resultRepository;
    }

    public function create($userId, $studyClassId, $courseCid, $period)
    {
        $results     = $this->resultRepository->findObjectiveResults($userId, $studyClassId, $courseCid, $period);
        $specialVars = $this->resultRepository->getMDAndPSVars($userId, $studyClassId, $courseCid, $period);

        $M1  = $results['mots']['results'][0]['score'];
        $M2  = $results['mots']['results'][1]['score'];
        $TM  = $M1 + $M2;
        $P1  = $results['phrases']['results'][0]['score'];
        $P2  = $results['phrases']['results'][1]['score'];
        $TP  = $P1 + $P2;
        $H1  = $results['histoires']['results'][0]['score'];
        $H2  = $results['histoires']['results'][1]['score'];
        $TH  = $H1 + $H2;
        $D1  = $results['documents']['results'][0]['score'];
        $D2  = $results['documents']['results'][1]['score'];
        $TD  = $D1 + $D2;
        $TIE = $H1 + $D1;
        $TII = $H2 + $D2;
        $TBN = $TM + $TP;
        $THN = $TH + $TD;
        $TOT = $TBN + $THN;

        return [
            'M1'  => $M1,
            'M2'  => $M2,
            'TM'  => $TM,
            'MD'  => $specialVars['MD'][0]['score'],
            'P1'  => $P1,
            'P2'  => $P2,
            'PS'  => $specialVars['PS'][0]['score'],
            'TP'  => $TP,
            'H1'  => $H1,
            'H2'  => $H2,
            'TH'  => $TH,
            'D1'  => $D1,
            'D2'  => $D2,
            'TD'  => $TD,
            'TIE' => $TIE,
            'TII' => $TII,
            'TBN' => $TBN,
            'THN' => $THN,
            'TOT' => $TOT,
        ];
    }
}
