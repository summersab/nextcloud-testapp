<?php
/**
 * @copyright Copyright (c) 2021, Andrew Summers
 *
 * @author Andrew Summers
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\TestApp\AppInfo;

use OCA\TestApp\Listener\AllListener;

use OCP\App\ManagerEvent;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

use OCA\DAV\Events\AddressBookCreatedEvent;
use OCA\DAV\Events\AddressBookDeletedEvent;
use OCA\DAV\Events\AddressBookShareUpdatedEvent;
use OCA\DAV\Events\AddressBookUpdatedEvent;
use OCA\DAV\Events\CachedCalendarObjectCreatedEvent;
use OCA\DAV\Events\CachedCalendarObjectDeletedEvent;
use OCA\DAV\Events\CachedCalendarObjectUpdatedEvent;
use OCA\DAV\Events\CalendarCreatedEvent;
use OCA\DAV\Events\CalendarDeletedEvent;
use OCA\DAV\Events\CalendarObjectCreatedEvent;
use OCA\DAV\Events\CalendarObjectDeletedEvent;
use OCA\DAV\Events\CalendarObjectUpdatedEvent;
use OCA\DAV\Events\CalendarPublishedEvent;
use OCA\DAV\Events\CalendarShareUpdatedEvent;
use OCA\DAV\Events\CalendarUnpublishedEvent;
use OCA\DAV\Events\CalendarUpdatedEvent;
use OCA\DAV\Events\CardCreatedEvent;
use OCA\DAV\Events\CardDeletedEvent;
use OCA\DAV\Events\CardUpdatedEvent;
use OCA\DAV\Events\SabrePluginAuthInitEvent;
use OCA\DAV\Events\SubscriptionCreatedEvent;
use OCA\DAV\Events\SubscriptionDeletedEvent;
use OCA\DAV\Events\SubscriptionUpdatedEvent;
use OCA\FederatedFileSharing\Events\FederatedShareAddedEvent;
use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\Files_Sharing\Event\BeforeTemplateRenderedEvent as Files_SharingBeforeTemplateRenderedEvent;
use OCA\Settings\Events\BeforeTemplateRenderedEvent as SettingsBeforeTemplateRenderedEvent;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent as AppFrameworkBeforeTemplateRenderedEvent;
use OCA\User_LDAP\Events\GroupBackendRegistered;
use OCA\User_LDAP\Events\UserBackendRegistered;
use OCA\Viewer\Event\LoadViewer;
use OCP\Authentication\Events\LoginFailedEvent;
use OCP\Authentication\TwoFactorAuth\TwoFactorProviderDisabled;
use OCP\Contacts\Events\ContactInteractedWithEvent;
use OCP\Files\Events\BeforeFileScannedEvent;
use OCP\Files\Events\BeforeFolderScannedEvent;
use OCP\Files\Events\FileCacheUpdated;
use OCP\Files\Events\FileScannedEvent;
use OCP\Files\Events\FolderScannedEvent;
use OCP\Files\Events\NodeAddedToCache;
use OCP\Files\Events\NodeRemovedFromCache;
use OCP\Group\Events\BeforeGroupCreatedEvent;
use OCP\Group\Events\BeforeGroupDeletedEvent;
use OCP\Group\Events\BeforeUserAddedEvent;
use OCP\Group\Events\BeforeUserRemovedEvent;
use OCP\Group\Events\GroupCreatedEvent;
use OCP\Group\Events\GroupDeletedEvent;
use OCP\Group\Events\SubAdminAddedEvent;
use OCP\Group\Events\SubAdminRemovedEvent;
use OCP\Group\Events\UserAddedEvent;
use OCP\Group\Events\UserRemovedEvent;
use OCP\Mail\Events\BeforeMessageSent;
use OCP\Security\Events\GenerateSecurePasswordEvent;
use OCP\Security\Events\ValidatePasswordPolicyEvent;
use OCP\Share\Events\ShareCreatedEvent;
use OCP\Share\Events\VerifyMountPointEvent;
use OCP\User\Events\BeforeUserLoggedInWithCookieEvent;
use OCP\User\Events\UserLoggedInWithCookieEvent;
use OCP\User\Events\BeforePasswordUpdatedEvent;
use OCP\User\Events\PasswordUpdatedEvent;
use OCP\User\Events\BeforeUserCreatedEvent;
use OCP\User\Events\UserCreatedEvent;
use OCP\User\Events\BeforeUserDeletedEvent;
use OCP\User\Events\UserDeletedEvent;
use OCP\User\Events\BeforeUserLoggedInEvent;
use OCP\User\Events\BeforeUserLoggedOutEvent;
use OCP\User\Events\CreateUserEvent;
use OCP\User\Events\PostLoginEvent;
use OCP\User\Events\UserChangedEvent;
use OCP\User\Events\UserLiveStatusEvent;
use OCP\User\Events\UserLoggedInEvent;
use OCP\User\Events\UserLoggedOutEvent;
use OCP\WorkflowEngine\LoadSettingsScriptsEvent;
use OCP\WorkflowEngine\RegisterChecksEvent;
use OCP\WorkflowEngine\RegisterEntitiesEvent;
use OCP\WorkflowEngine\RegisterOperationsEvent;

use OCP\User\GetQuotaEvent;
use OCP\EventDispatcher\GenericEvent;

// These break things for some reason
// use OCP\DirectEditing\RegisterDirectEditorEvent;
// use OCP\Security\CSP\AddContentSecurityPolicyEvent;
// use OCP\Security\FeaturePolicy\AddFeaturePolicyEvent;

// Needed to register middleware
use OC_App;
use OCP\AppFramework\QueryException;
use OC\AppFramework\DependencyInjection\DIContainer;

use OCA\TestApp\Middleware\TestMiddleware;


class Application extends App implements IBootstrap {

	public const APP_ID = 'testapp';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);

		$enabledApps = OC_App::getEnabledApps();
		$requestPath = array_values(array_filter(explode('/', $_SERVER['PATH_INFO'])));

		// Get the current app and register an app container. Modify as necessary.
		if (
			(
				$requestPath[0] == 'settings' &&
				$appId = 'settings'
			) ||
			(
				sizeof($requestPath) >= 2 &&
				$requestPath[0] == 'apps' &&
				in_array($appId = $requestPath[1], $enabledApps)
			)
		) {
			try {
				\OC::$server->registerAppContainer($appId, new DIContainer($appId));
			}
			catch (\Exception $e) {
				// Shouldn't happen
			}
		}

		// Registers the middleware to all applications. Modify as necessary.
		foreach ($enabledApps as $appId) {
			if ($appId != self::APP_ID) {
				try {
					$appContainer = \OC::$server->getRegisteredAppContainer($appId);
					$appContainer->registerMiddleWare(TestMiddleware::class);
				}
				catch (QueryException $e) {
					// Who cares?
				}
			}
		}
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(AddContentSecurityPolicyEvent::class, AllListener::class);
		$context->registerEventListener(AddFeaturePolicyEvent::class, AllListener::class);
		$context->registerEventListener(AddressBookCreatedEvent::class, AllListener::class);
		$context->registerEventListener(AddressBookDeletedEvent::class, AllListener::class);
		$context->registerEventListener(AddressBookShareUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(AddressBookUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(BeforeFileScannedEvent::class, AllListener::class);
		$context->registerEventListener(BeforeFolderScannedEvent::class, AllListener::class);
		$context->registerEventListener(BeforeGroupCreatedEvent::class, AllListener::class);
		$context->registerEventListener(BeforeGroupDeletedEvent::class, AllListener::class);
		$context->registerEventListener(BeforeMessageSent::class, AllListener::class);
		$context->registerEventListener(BeforePasswordUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(Files_SharingBeforeTemplateRenderedEvent::class, AllListener::class);
		$context->registerEventListener(SettingsBeforeTemplateRenderedEvent::class, AllListener::class);
		$context->registerEventListener(AppFrameworkBeforeTemplateRenderedEvent::class, AllListener::class);
		$context->registerEventListener(BeforeUserAddedEvent::class, AllListener::class);
		$context->registerEventListener(BeforeUserCreatedEvent::class, AllListener::class);
		$context->registerEventListener(BeforeUserDeletedEvent::class, AllListener::class);
		$context->registerEventListener(BeforeUserLoggedInEvent::class, AllListener::class);
		$context->registerEventListener(BeforeUserLoggedInWithCookieEvent::class, AllListener::class);
		$context->registerEventListener(BeforeUserLoggedOutEvent::class, AllListener::class);
		$context->registerEventListener(BeforeUserRemovedEvent::class, AllListener::class);
		$context->registerEventListener(CachedCalendarObjectCreatedEvent::class, AllListener::class);
		$context->registerEventListener(CachedCalendarObjectDeletedEvent::class, AllListener::class);
		$context->registerEventListener(CachedCalendarObjectUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(CalendarCreatedEvent::class, AllListener::class);
		$context->registerEventListener(CalendarDeletedEvent::class, AllListener::class);
		$context->registerEventListener(CalendarObjectCreatedEvent::class, AllListener::class);
		$context->registerEventListener(CalendarObjectDeletedEvent::class, AllListener::class);
		$context->registerEventListener(CalendarObjectUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(CalendarPublishedEvent::class, AllListener::class);
		$context->registerEventListener(CalendarShareUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(CalendarUnpublishedEvent::class, AllListener::class);
		$context->registerEventListener(CalendarUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(CardCreatedEvent::class, AllListener::class);
		$context->registerEventListener(CardDeletedEvent::class, AllListener::class);
		$context->registerEventListener(CardUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(ContactInteractedWithEvent::class, AllListener::class);
		$context->registerEventListener(CreateUserEvent::class, AllListener::class);
		$context->registerEventListener(FederatedShareAddedEvent::class, AllListener::class);
		$context->registerEventListener(FileCacheUpdated::class, AllListener::class);
		$context->registerEventListener(FileScannedEvent::class, AllListener::class);
		$context->registerEventListener(FolderScannedEvent::class, AllListener::class);
		$context->registerEventListener(GenerateSecurePasswordEvent::class, AllListener::class);
		$context->registerEventListener(GroupBackendRegistered::class, AllListener::class);
		$context->registerEventListener(GroupCreatedEvent::class, AllListener::class);
		$context->registerEventListener(GroupDeletedEvent::class, AllListener::class);
		$context->registerEventListener(LoadAdditionalScriptsEvent::class, AllListener::class);
		$context->registerEventListener(LoadSettingsScriptsEvent::class, AllListener::class);
		$context->registerEventListener(LoadViewer::class, AllListener::class);
		$context->registerEventListener(LoginFailedEvent::class, AllListener::class);
		$context->registerEventListener(NodeAddedToCache::class, AllListener::class);
		$context->registerEventListener(NodeRemovedFromCache::class, AllListener::class);
		$context->registerEventListener(PasswordUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(PostLoginEvent::class, AllListener::class);
		$context->registerEventListener(RegisterChecksEvent::class, AllListener::class);
		$context->registerEventListener(RegisterDirectEditorEvent::class, AllListener::class);
		$context->registerEventListener(RegisterEntitiesEvent::class, AllListener::class);
		$context->registerEventListener(RegisterOperationsEvent::class, AllListener::class);
		$context->registerEventListener(SabrePluginAuthInitEvent::class, AllListener::class);
		$context->registerEventListener(ShareCreatedEvent::class, AllListener::class);
		$context->registerEventListener(SubAdminAddedEvent::class, AllListener::class);
		$context->registerEventListener(SubAdminRemovedEvent::class, AllListener::class);
		$context->registerEventListener(SubscriptionCreatedEvent::class, AllListener::class);
		$context->registerEventListener(SubscriptionDeletedEvent::class, AllListener::class);
		$context->registerEventListener(SubscriptionUpdatedEvent::class, AllListener::class);
		$context->registerEventListener(TwoFactorProviderDisabled::class, AllListener::class);
		$context->registerEventListener(UserAddedEvent::class, AllListener::class);
		$context->registerEventListener(UserBackendRegistered::class, AllListener::class);
		$context->registerEventListener(UserChangedEvent::class, AllListener::class);
		$context->registerEventListener(UserCreatedEvent::class, AllListener::class);
		$context->registerEventListener(UserDeletedEvent::class, AllListener::class);
		$context->registerEventListener(UserLiveStatusEvent::class, AllListener::class);
		$context->registerEventListener(UserLoggedInEvent::class, AllListener::class);
		$context->registerEventListener(UserLoggedInWithCookieEvent::class, AllListener::class);
		$context->registerEventListener(UserLoggedOutEvent::class, AllListener::class);
		$context->registerEventListener(UserRemovedEvent::class, AllListener::class);
		$context->registerEventListener(ValidatePasswordPolicyEvent::class, AllListener::class);
		$context->registerEventListener(VerifyMountPointEvent::class, AllListener::class);

		$context->registerEventListener(GetQuotaEvent::class, AllListener::class);
		$context->registerEventListener(GenericEvent::class, AllListener::class);
	}

	public function boot(IBootContext $context): void {
		$serverContainer = $context->getServerContainer();
	}
}
