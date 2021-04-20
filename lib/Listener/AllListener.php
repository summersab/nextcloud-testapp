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

declare(strict_types=1);

namespace OCA\TestApp\Listener;

use OCP\BackgroundJob\IJobList;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\IConfig;
use OCP\IUser;
use OCP\IUserSession;

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

use OC;

class AllListener implements IEventListener {
	/**
	 * @var IUserSession
	 */
	private $userSession;
	/**
	 * @var IConfig
	 */
	private $config;
	/**
	 * @var IJobList
	 */
	private $jobList;

	public function __construct(
		IConfig $config,
		IUserSession $userSession,
		IJobList $jobList
	) {
		$this->userSession = $userSession;
		$this->config = $config;
		$this->jobList = $jobList;
	}

	public function handle(Event $event): void {
		$allListenerBreakpoint = 1;
		if ($event instanceof AddressBookCreatedEvent) {
			return;
		}
		
		if ($event instanceof AddressBookDeletedEvent) {
			return;
		}
		
		if ($event instanceof AddressBookShareUpdatedEvent) {
			return;
		}
		
		if ($event instanceof AddressBookUpdatedEvent) {
			return;
		}
		
		if ($event instanceof CachedCalendarObjectCreatedEvent) {
			return;
		}
		
		if ($event instanceof CachedCalendarObjectDeletedEvent) {
			return;
		}
		
		if ($event instanceof CachedCalendarObjectUpdatedEvent) {
			return;
		}
		
		if ($event instanceof CalendarCreatedEvent) {
			return;
		}
		
		if ($event instanceof CalendarDeletedEvent) {
			return;
		}
		
		if ($event instanceof CalendarObjectCreatedEvent) {
			return;
		}
		
		if ($event instanceof CalendarObjectDeletedEvent) {
			return;
		}
		
		if ($event instanceof CalendarObjectUpdatedEvent) {
			return;
		}
		
		if ($event instanceof CalendarPublishedEvent) {
			return;
		}
		
		if ($event instanceof CalendarShareUpdatedEvent) {
			return;
		}
		
		if ($event instanceof CalendarUnpublishedEvent) {
			return;
		}
		
		if ($event instanceof CalendarUpdatedEvent) {
			return;
		}
		
		if ($event instanceof CardCreatedEvent) {
			return;
		}
		
		if ($event instanceof CardDeletedEvent) {
			return;
		}
		
		if ($event instanceof CardUpdatedEvent) {
			return;
		}
		
		if ($event instanceof SabrePluginAuthInitEvent) {
			return;
		}
		
		if ($event instanceof SubscriptionCreatedEvent) {
			return;
		}
		
		if ($event instanceof SubscriptionDeletedEvent) {
			return;
		}
		
		if ($event instanceof SubscriptionUpdatedEvent) {
			return;
		}
		
		if ($event instanceof FederatedShareAddedEvent) {
			return;
		}
		
		if ($event instanceof LoadAdditionalScriptsEvent) {
			return;
		}
		
		if ($event instanceof Files_SharingBeforeTemplateRenderedEvent) {
			return;
		}
		
		if ($event instanceof SettingsBeforeTemplateRenderedEvent) {
			return;
		}
		
		if ($event instanceof AppFrameworkBeforeTemplateRenderedEvent) {
			return;
		}
		
		if ($event instanceof GroupBackendRegistered) {
			return;
		}
		
		if ($event instanceof UserBackendRegistered) {
			return;
		}
		
		if ($event instanceof LoadViewer) {
			return;
		}
		
		if ($event instanceof LoginFailedEvent) {
			return;
		}
		
		if ($event instanceof TwoFactorProviderDisabled) {
			return;
		}
		
		if ($event instanceof ContactInteractedWithEvent) {
			return;
		}
		
		if ($event instanceof BeforeFileScannedEvent) {
			return;
		}
		
		if ($event instanceof BeforeFolderScannedEvent) {
			return;
		}
		
		if ($event instanceof FileCacheUpdated) {
			return;
		}
		
		if ($event instanceof FileScannedEvent) {
			return;
		}
		
		if ($event instanceof FolderScannedEvent) {
			return;
		}
		
		if ($event instanceof NodeAddedToCache) {
			return;
		}
		
		if ($event instanceof NodeRemovedFromCache) {
			return;
		}
		
		if ($event instanceof BeforeGroupCreatedEvent) {
			return;
		}
		
		if ($event instanceof BeforeGroupDeletedEvent) {
			return;
		}
		
		if ($event instanceof BeforeUserAddedEvent) {
			return;
		}
		
		if ($event instanceof BeforeUserRemovedEvent) {
			return;
		}
		
		if ($event instanceof GroupCreatedEvent) {
			return;
		}
		
		if ($event instanceof GroupDeletedEvent) {
			return;
		}
		
		if ($event instanceof SubAdminAddedEvent) {
			return;
		}
		
		if ($event instanceof SubAdminRemovedEvent) {
			return;
		}
		
		if ($event instanceof UserAddedEvent) {
			return;
		}
		
		if ($event instanceof UserRemovedEvent) {
			return;
		}
		
		if ($event instanceof BeforeMessageSent) {
			return;
		}
		
		if ($event instanceof GenerateSecurePasswordEvent) {
			return;
		}
		
		if ($event instanceof ValidatePasswordPolicyEvent) {
			return;
		}
		
		if ($event instanceof ShareCreatedEvent) {
			return;
		}
		
		if ($event instanceof VerifyMountPointEvent) {
			return;
		}
		
		if ($event instanceof BeforeUserLoggedInWithCookieEvent) {
			return;
		}
		
		if ($event instanceof UserLoggedInWithCookieEvent) {
			return;
		}
		
		if ($event instanceof BeforePasswordUpdatedEvent) {
			return;
		}
		
		if ($event instanceof PasswordUpdatedEvent) {
			return;
		}
		
		if ($event instanceof BeforeUserCreatedEvent) {
			return;
		}
		
		if ($event instanceof UserCreatedEvent) {
			return;
		}
		
		if ($event instanceof BeforeUserDeletedEvent) {
			return;
		}
		
		if ($event instanceof UserDeletedEvent) {
			return;
		}
		
		if ($event instanceof BeforeUserLoggedInEvent) {
			return;
		}
		
		if ($event instanceof BeforeUserLoggedOutEvent) {
			return;
		}
		
		if ($event instanceof CreateUserEvent) {
			return;
		}
		
		if ($event instanceof PostLoginEvent) {
			return;
		}
		
		if ($event instanceof UserChangedEvent) {
			return;
		}
		
		if ($event instanceof UserLiveStatusEvent) {
			return;
		}
		
		if ($event instanceof UserLoggedInEvent) {
			return;
		}
		
		if ($event instanceof UserLoggedOutEvent) {
			return;
		}
		
		if ($event instanceof LoadSettingsScriptsEvent) {
			return;
		}
		
		if ($event instanceof RegisterChecksEvent) {
			return;
		}
		
		if ($event instanceof RegisterEntitiesEvent) {
			return;
		}
		
		if ($event instanceof RegisterOperationsEvent) {
			return;
		}
		
		
		if ($event instanceof GetQuotaEvent) {
			return;
		}
		
		if ($event instanceof GenericEvent) {
			return;
		}
		
		
		// These break things for some reason
		// if ($event instanceof RegisterDirectEditorEvent) {
		// 	return;
		// }
		
		// if ($event instanceof AddContentSecurityPolicyEvent) {
		// 	return;
		// }
		
		// if ($event instanceof AddFeaturePolicyEvent) {
		// 	return;
		// }

		// Pull and edit the params from the event if desired
		//$params = $event->getResponse()->getParams();
		//$event->getResponse()->setParams($params);

	}
}