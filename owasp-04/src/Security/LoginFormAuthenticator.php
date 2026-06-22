<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

final class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $username = trim($request->request->getString('_username'));
        $password = $request->request->getString('_password');
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username, function (string $userIdentifier): User {
                $user = $this->userRepository->findOneByUsername($userIdentifier);

                if (!$user instanceof User) {
                    throw new UserNotFoundException();
                }

                return $user;
            }),
            new CustomCredentials(
                static function (mixed $plainPassword, UserInterface $user): bool {
                    if (!$user instanceof User) {
                        return false;
                    }

                    if (!is_string($plainPassword)) {
                        return false;
                    }

                    return md5($plainPassword) === $user->getPassword();
                },
                $password
            ),
            [
                new CsrfTokenBadge('authenticate', $request->request->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('dashboard'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('login');
    }
}
