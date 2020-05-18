<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Security\UserRepositoryInterface;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotFound;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnprocessableEntity;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use PSR7Sessions\Storageless\Session\SessionInterface;

final class SessionLoginRequestHandler implements RequestHandlerInterface
{
    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var PasswordManagerInterface
     */
    private $passwordManager;

    /**
     * @var ResponseManagerInterface
     */
    private $responseManager;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(
        DecoderInterface $decoder,
        PasswordManagerInterface $passwordManager,
        ResponseManagerInterface $responseManager,
        UserRepositoryInterface $userRepository
    ) {
        $this->decoder = $decoder;
        $this->passwordManager = $passwordManager;
        $this->responseManager = $responseManager;
        $this->userRepository = $userRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getAttribute('accept');
        $contentType = $request->getAttribute('contentType');

        $data = $this->decoder->decode((string) $request->getBody(), $contentType);

        if ([] !== $errors = $this->validateData($data)) {
            return $this->responseManager->createFromApiProblem(
                new UnprocessableEntity($errors),
                $accept
            );
        }

        if (null === $user = $this->userRepository->findByUsername($data['username'])) {
            return $this->responseManager->createFromApiProblem(
                new NotFound('Missing user'),
                $accept
            );
        }

        if (!$this->passwordManager->verify($data['password'], $user->getPassword())) {
            return $this->responseManager->createFromApiProblem(
                new NotFound('Missing user'),
                $accept
            );
        }

        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $session->set(SessionAuthentication::SESSION_KEY, [
            SessionAuthentication::ID_KEY => $user->getId(),
            SessionAuthentication::HASH_KEY => $user->getHash(),
        ]);

        return $this->responseManager->createEmpty($accept);
    }

    /**
     * @param array<string, string> $data
     *
     * @return array<string, string>
     */
    private function validateData(array $data): array
    {
        $errors = [];

        if (!isset($data['username']) || !is_string($data['username'])) {
            $errors['username'] = 'Missing or empty';
        }

        if (!isset($data['password']) || !is_string($data['password'])) {
            $errors['password'] = 'Missing or empty';
        }

        return $errors;
    }
}
