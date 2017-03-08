<?php

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class AppCache extends HttpCache
{
    protected function getOptions()
    {
        return array(
            'debug'                  => true,
            'default_ttl'            => 0,
            'private_headers'        => array('Authorization', 'Cookie', 'Access-Control-Allow-Origin'),
            'allow_reload'           => true,
            'allow_revalidate'       => true,
            'stale_while_revalidate' => 2,
            'stale_if_error'         => 60,
        );
    }
}
