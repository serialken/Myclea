<?php

namespace AppBundle\Action;

use AppBundle\Exception\ClientException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostErrorAction
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Log an error from a client.
     *
     * @ApiDoc(
     *     resource = true,
     *     statusCodes = {
     *         204 = "Returned when successful"
     *     }
     * )
     *
     * @Route("/errors")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data['type'] !== 'ApiError') {
            $this->logger->critical('An error occurred on the client.', [
                'exception' => ClientException::create($data['error']),
            ]);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
