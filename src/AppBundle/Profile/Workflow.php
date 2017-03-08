<?php

namespace AppBundle\Profile;

use AppBundle\Repository\RecommendedActivitiesRepository;

class Workflow
{
    private $variablesCreator;
    private $notationCreator;
    private $recommendedActivitiesRepository;
    private $vars;
    private $terminate;
    /**
     * @var Profile
     */
    private $profile;
    /**
     * @var DisplayBag
     */
    private $displayBag;

    public function __construct(
        VariablesCreator $variablesCreator,
        NotationCreator $notationCreator,
        RecommendedActivitiesRepository $recommendedActivitiesRepository
    ) {
        $this->variablesCreator                = $variablesCreator;
        $this->notationCreator                 = $notationCreator;
        $this->recommendedActivitiesRepository = $recommendedActivitiesRepository;
    }

    /**
     * @param int    $userId
     * @param int    $studyClassId
     * @param string $courseCid
     * @param int    $period
     *
     * @return Profile
     */
    public function getProfile($userId, $studyClassId, $courseCid, $period)
    {
        $this->profile    = new Profile();
        $this->displayBag = new DisplayBag();
        $this->terminate  = false;
        $this->vars       = $this->variablesCreator->create($userId, $studyClassId, $courseCid, $period);
        $flow             = [
            'createNotation',
            'testAberrant',
            'testSuspect',
            'analyzeProfile',
        ];

        foreach ($flow as $step) {
            call_user_func([$this, $step]);

            if ($this->terminate) {
                break;
            }
        }

        if (!$this->terminate) {
            $this->profile->setRecommendedActivities($this->recommendedActivitiesRepository->findByNotation($this->profile->getNotation()));
            $this->profile->setProfile(ProfileToDisplaysMap::findProfile($this->displayBag->getDisplays()));
        }

        return $this->profile;
    }

    private function createNotation()
    {
        $varsProfile = ['TM', 'TP', 'TH', 'TD'];
        $notation    = '';

        foreach ($varsProfile as $var) {
            $notation .= $this->notationCreator->calculate($this->vars[$var]);
        }

        $this->profile->setNotation($notation);
    }

    private function testAberrant()
    {
        $notation = $this->profile->getNotation();

        if ($this->vars['TBN'] + 10 < $this->vars['THN'] && $notation !== 'AAAA') {
            $this->profile->setProfileAberrant(0);
            $this->endProgram();

            return;
        }

        if ($notation === 'FFAF') {
            if ($this->vars['TH'] - (($this->vars['TM'] + $this->vars['TP'] + $this->vars['TD']) / 3) > 2) {
                $this->profile->setProfileAberrant(6);
                $this->endProgram();

                return;
            } else {
                $this->profile
                    ->setProfileSuspect(5)
                    ->setNotation('FFFF');

                return;
            }
        }

        if ($notation === 'FFFA') {
            if ($this->vars['TD'] - (($this->vars['TM'] + $this->vars['TP'] + $this->vars['TH']) / 3) > 2) {
                $this->profile->setProfileAberrant(7);
                $this->endProgram();

                return;
            } else {
                $this->profile
                    ->setProfileSuspect(6)
                    ->setNotation('FFFF');

                return;
            }
        }

        if ($notation === 'FFAA') {
            $this->profile->setProfileAberrant(8);
            $this->endProgram();

            return;
        }

        if ($notation === 'FAFA') {
            $this->profile->setProfileAberrant(9);
            $this->endProgram();
        }

        return;
    }

    private function testSuspect()
    {
        $notation = $this->profile->getNotation();

        if ($notation === 'FAAA') {
            $this->profile->setProfileSuspect(4);

            return;
        }

        if ($notation === 'FAFF') {
            if ($this->vars['TP'] > 17) {
                $this->profile->setProfileAberrant(1);
                $this->endProgram();

                return;
            } else {
                $this->profile
                    ->setProfileSuspect(1)
                    ->setNotation('FFFF');

                return;
            }
        }

        if ($notation === 'FAAF') {
            if ($this->vars['TP'] > 17 && $this->vars['TH'] > 17) {
                $this->profile->setProfileAberrant(5);
                $this->endProgram();

                return;
            } else {
                $this->profile
                    ->setProfileSuspect(3)
                    ->setNotation('FFFF');

                return;
            }
        }

        if ($notation === 'AFAF') {
            if ($this->vars['TH'] > 17) {
                $this->profile->setProfileAberrant(2);
                $this->endProgram();

                return;
            } else {
                $this->profile
                    ->setProfileSuspect(2)
                    ->setNotation('AFFF');

                return;
            }
        }

        if ($notation === 'AFFA') {
            if ($this->vars['TD'] > 17) {
                $this->profile->setProfileAberrant(3);
                $this->endProgram();

                return;
            } else {
                $this->profile
                    ->setProfileSuspect(2)
                    ->setNotation('AFFF');

                return;
            }
        }

        if ($notation === 'AFAA') {
            if ($this->vars['TH'] + $this->vars['TD'] > 38) {
                $this->profile->setProfileAberrant(4);
                $this->endProgram();

                return;
            } else {
                $this->profile
                    ->setProfileSuspect(2)
                    ->setNotation('AFFF');

                return;
            }
        }
    }

    private function analyzeProfile()
    {
        $notation = $this->profile->getNotation();

        if ($notation === 'AAAA') {
            $this->displayBag->addDisplay('Profil6');

            if ($this->vars['TII'] < 16) {
                $this->displayBag->addDisplay('Profil6a');
            }
        }

        if ($notation === 'AAAF') {
            $this->displayBag->addDisplay('Profil5');

            if ($this->vars['D1'] < 8) {
                $this->displayBag->addDisplay('Profil5a');
            } elseif ($this->vars['D2'] < 8) {
                $this->displayBag->addDisplay('Profil5b');
            }
        }

        if ($notation === 'AAFA') {
            $this->displayBag->addDisplay('Profil4');

            if ($this->vars['H1'] < 8) {
                $this->displayBag->addDisplay('Profil4a');

                if ($this->vars['P1'] < 11) {
                    $this->displayBag->addDisplay('Profil4b');

                    if ($this->vars['M1'] < 11) {
                        $this->displayBag->addDisplay('Profil4c');
                    }
                }
            } elseif ($this->vars['PS'] < 11) {
                $this->displayBag->addDisplay('Profil4d');
            }
        }

        if ($notation === 'AAFF') {
            $this->displayBag->addDisplay('Profil3');

            if ($this->vars['P1'] < 11) {
                if ($this->vars['M1'] < 11) {
                    $this->displayBag->addDisplay('Profil3a');
                } else {
                    $this->displayBag->addDisplay('Profil3d');
                }
            } elseif ($this->vars['P2'] < 11) {
                $this->displayBag->addDisplay('Profil3b');
            } elseif ($this->vars['PS'] < 11) {
                $this->displayBag->addDisplay('Profil3c');
            } elseif ($this->vars['TIE'] < 15) {
                $this->displayBag->addDisplay('Profil3e');
            }
        }

        if (in_array($notation, ['AFFF', 'FFFF', 'FAAA'], true)) {
            if ($notation === 'FFFF') {
                if ($this->vars['TOT'] < 40) {
                    $this->displayBag->addDisplay('Profil1a');
                } else {
                    $this->displayBag->addDisplay('Profil1b');
                }
            } elseif ($notation === 'AFFF') {
                $this->displayBag->addDisplay('Profil2');
            }

            if ($this->vars['M1'] < 11) {
                $this->displayBag->addDisplay('Profil2a');

                if ($this->vars['MD'] < 7) {
                    $this->displayBag->addDisplay('Profil2c');
                }
            }

            if ($this->vars['M2'] < 8) {
                $this->displayBag->addDisplay('Profil2b');
            }
        }
    }

    private function endProgram()
    {
        $this->terminate = true;
    }
}
