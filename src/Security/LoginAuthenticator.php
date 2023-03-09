<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        //if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
        //    return new RedirectResponse($targetPath);
        //}
        $user = $token->getUser();

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_list_admin'));
        }
        if (in_array('ROLE_ADMIN_COACH', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_coachs'));
        }
        if (in_array('ROLE_ADMIN_CLUBOWNER', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_ListClubOwner'));
        }
        if (in_array('ROLE_COACH_UNAPPROVED', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_coach'));
        }
        if (in_array('ROLE_CLUBOWNER', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('list_club'));
        }
        if (in_array('ROLE_COACH', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('add_seance'));
        }
        if (in_array('ROLE_ADMIN_PRODUIT', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_vente'));
        }
        if (in_array('ROLE_ADMIN_RECLAMATION', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('afficher_reclamation'));
        }
        if (in_array('ROLE_ADMIN_COACH_banned', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_adminBanned'));
        }
        if (in_array('ROLE_ADMIN_CLUBOWNER_banned', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_adminBanned'));
        }
        if (in_array('ROLE_ADMIN_RECLAMATION_banned', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_adminBanned'));
        }
        if (in_array('ROLE_ADMIN_PRDOUIT_banned', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_adminBanned'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_test'));
        // For example:
        //return new RedirectResponse($this->urlGenerator->generate('app_admin_coachs'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
