<?php

namespace AppBundle\Profile;

class NotationCreator
{
    /**
     * @param int $score
     *
     * @return string
     */
    public function calculate($score)
    {
        if ($score < 16) {
            return 'F';
        }

        return 'A';
    }
}
