<?php

declare(strict_types=1);

namespace Jadob\Security\Supervisor\EventListener;

use Jadob\Core\Event\BeforeControllerEvent;
use Jadob\Core\RequestContext;
use Jadob\EventDispatcher\EventDispatcher;
use Jadob\EventDispatcher\ListenerProviderPriorityInterface;
use Jadob\Security\Auth\Event\UserEvent;
use Jadob\Security\Auth\Exception\AuthenticationException;
use Jadob\Security\Auth\Exception\InvalidCredentialsException;
use Jadob\Security\Auth\Exception\UserNotFoundException;
use Jadob\Security\Auth\IdentityStorage;
use Jadob\Security\Auth\User\UserInterface;
use Jadob\Security\Supervisor\RequestAttribute;
use Jadob\Security\Supervisor\RequestSupervisor\RequestSupervisorInterface;
use Jadob\Security\Supervisor\Authenticator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function get_class;

/**
 * @author  pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class AuthenticatorListener implements ListenerProviderInterface, ListenerProviderPriorityInterface
{
    protected Authenticator $authenticator;
    protected IdentityStorage $identityStorage;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Authenticator $supervisor,
        IdentityStorage $identityStorage,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->authenticator = $supervisor;
        $this->identityStorage = $identityStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($event instanceof BeforeControllerEvent) {
            return [
                [$this, 'onBeforeController']
            ];
        }

        return [];
    }

    public function onBeforeController(BeforeControllerEvent $event): BeforeControllerEvent
    {
        $requestSupervisor = $this->authenticator->matchRequestSupervisor($event->getRequest());

        //Assign current provider to context
        $event->getContext()->setSupervisor($requestSupervisor);

        /**
         * There is nothing to do when there is no supervisor available
         */
        if ($requestSupervisor === null) {
            return $event;
        }


        //At first, handle stateless
        if ($requestSupervisor->isStateless()) {
            $response = $this->authenticator->handleStatelessRequest(
                $event->getContext(),
                $requestSupervisor
            );

            if ($response !== null) {
                $event->setResponse($response);
            }
            return $event;
        }

        $response = $this->handleNonStatelessRequest($event->getContext(), $requestSupervisor);

        if ($response !== null) {
            $event->setResponse($response);
        }

        return $event;

    }


    //@TODO: this must be moved to supervisor
    protected function handleNonStatelessRequest(RequestContext $context, RequestSupervisorInterface $supervisor): ?Response
    {
        $request = $context->getRequest();

        //1. Check if this is an authentication attempt:
        if ($supervisor->isAuthenticationRequest($request)) {
            try {
                //2. Handle Authentication
                $credentials = $supervisor->extractCredentialsFromRequest($request);
                if ($credentials === null) {
                    throw new \LogicException(
                        sprintf('%s::extractCredentialsFromRequest should not return null.', get_class($supervisor))
                    );
                }

                if ($credentials === false || count($credentials) === 0) {
                    throw UserNotFoundException::emptyCredentials();
                }

                //Get user
                $user = $supervisor->getIdentityFromProvider(
                    $credentials,
                    $this->authenticator->getUserProviderForSupervisor($supervisor)
                );

                if ($user === null) {
                    throw UserNotFoundException::userNotFound();
                }

                //verify user
                $verified = $supervisor->verifyIdentity($user, $credentials);
                if ($verified === false) {
                    throw InvalidCredentialsException::invalidCredentials();
                }

            } catch (AuthenticationException $exception) {
                return $supervisor->handleAuthenticationFailure($exception, $request);
            }

            $this->identityStorage->setUser($user, $request->getSession(), get_class($supervisor));
            $this->eventDispatcher->dispatch(
                new UserEvent(
                    $user,
                    UserEvent::CONTEXT_AUTHENTICATED,
                    $supervisor
                )
            );

            return $supervisor->handleAuthenticationSuccess($request, $user);
        }

        $userFromStorage = $this->identityStorage->getUser($request->getSession(), get_class($supervisor));
        if ($userFromStorage !== null) {
            /** @var UserEvent $userAfterEvents */
            $userAfterEvents = $this->eventDispatcher->dispatch(
                    new UserEvent(
                        $userFromStorage,
                        UserEvent::CONTEXT_TAKEN_FROM_SESSION,
                        $supervisor
                    )
                );

            $userFromStorage = $userAfterEvents->getUser();
        }

        $context->setUser($userFromStorage);

        /**
         * Case #1: User is logged in, nothing to do
         * Allow request to continue as everything is ok
         */
        if ($userFromStorage !== null) {
            return null;
        }

        $anonymousRequestAllowed = $supervisor->isAnonymousRequestAllowed($request);
        $request->attributes->set(RequestAttribute::SUPERVISOR_ANONYMOUS_ALLOWED, $anonymousRequestAllowed);

        /**
         * Case #2: User is not authenticated, but supervisor allows these request to continue without authentication
         */
        if ($userFromStorage === null && $anonymousRequestAllowed) {
            return null;
        }

        /**
         * Case #3: User is not authenticated, but authentication is required
         */
        if ($userFromStorage === null && !$anonymousRequestAllowed) {
            return $supervisor->handleUnauthenticated();
        }


        return null;
        //3. User is not logged in, but supervisor allows unauthenticated user to enter
        //4. User is not logged in and supervisor wants user to be authenticated
        //5. User is logged in, there is nothing to do
    }

    public function getListenerPriorityForEvent(object $event): int
    {
        return 110; // DEFAULT_LISTENER_PRIORITY + 10
    }
}