<?php

namespace AppBundle\Profile;

class DisplayBag
{
    private $displays = [];

    /**
     * @return array
     */
    public function getDisplays()
    {
        sort($this->displays);

        return $this->displays;
    }

    /**
     * @param string $display
     *
     * @return DisplayBag
     */
    public function addDisplay($display)
    {
        if (!in_array($display, $this->displays, true)) {
            $this->displays[] = $display;
        }

        return $this;
    }
}
