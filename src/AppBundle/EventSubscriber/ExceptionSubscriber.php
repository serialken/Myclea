<?php

namespace AppBundle\EventSubscriber;

use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\HttpUtils;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private static $ERROR_DB_CODE    = 'ERR_DWH';
    private static $ERROR_OTHER_CODE = 'ERR_eplateforme';

    /**
     * @var HttpUtils
     */
    private $httpUtils;
    private $logger;

    public function __construct(HttpUtils $httpUtils, LoggerInterface $logger = null)
    {
        $this->httpUtils = $httpUtils;
        $this->logger    = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->httpUtils->checkRequestPath($event->getRequest(), 'nelmio_api_doc_index')) {
            return;
        }

        $e          = $event->getException();
        // Uncomment the next 3 line to display errors
//        var_dump("Message: ".$e->getMessage());
//        var_dump("Line: ".$e->getLine());
//        var_dump("File: ".$e->getFile());
        $apiCode    = self::$ERROR_OTHER_CODE;
        $statusCode = 500;
        if ($e instanceof DBALException) {
            $apiCode = self::$ERROR_DB_CODE;
        } elseif ($e instanceof HttpExceptionInterface) {
            $statusCode = $e->getStatusCode();
        }

        $this->logException($e, sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));

        $response = new JsonResponse([
            'api_code'    => $apiCode,
            'status_code' => $statusCode,
            'status_text' => array_key_exists($statusCode, Response::$statusTexts) ? Response::$statusTexts[$statusCode] : 'Unknown error',
        ], $statusCode);

        $event->setResponse($response);
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $exception The \Exception instance
     * @param string     $message   The error message to log
     */
    protected function logException(\Exception $exception, $message)
    {
        if (null !== $this->logger) {
            if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical($message, ['exception' => $exception]);
            } else {
                $this->logger->error($message, ['exception' => $exception]);
            }
        }
    }
}
