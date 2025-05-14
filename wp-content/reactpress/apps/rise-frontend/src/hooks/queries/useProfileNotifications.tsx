/**
 * useProfileNotifications hook. Query to retrieve profile notifications.
 */

import { gql, useQuery } from '@apollo/client';
import { ProfileNotification } from '@lib/classes';
import { ProfileNotificationParams } from '@lib/types';
import { isEmpty, omit } from 'lodash';

// TODO update Job class props to match the query

export const QUERY_PROFILE_NOTIFICATIONS = gql`
	query ProfileNotificationsQuery($authorId: Int = 10, $limit: Int = -1) {
		unreadProfileNotifications(authorId: $authorId, limit: $limit) {
			id
			title
			notificationType
			value
		}
		readProfileNotifications(authorId: $authorId, limit: $limit) {
			id
			title
			notificationType
			value
		}
	}
`;

const useProfileNotifications = (
	authorId: number
): [{ unread: ProfileNotification[]; read: ProfileNotification[] }, any] => {
	const result = useQuery(QUERY_PROFILE_NOTIFICATIONS, {
		variables: {
			authorId,
			limit: 10,
		},
		fetchPolicy: 'cache-and-network',
		pollInterval: 20000,
	});

	console.log(result);

	const allProfileNotifications = { unread: [], read: [] };

	if (!result?.data?.unreadProfileNotifications) {
		allProfileNotifications.unread = [];
	}

	if (!result?.data?.readProfileNotifications) {
		allProfileNotifications.read = [];
	}

	if (
		isEmpty(result?.data?.unreadProfileNotifications) &&
		isEmpty(result?.data?.readProfileNotifications)
	) {
		return [allProfileNotifications, null];
	}

	allProfileNotifications.unread =
		result?.data?.unreadProfileNotifications?.map((node: ProfileNotificationParams) => {
			const { id, title, notificationType, value } = node;

			const profileNotification = new ProfileNotification({
				id,
				title,
				notificationType,
				value,
			});

			return profileNotification;
		}) ?? [];

	allProfileNotifications.read =
		result?.data?.readProfileNotifications?.map((node: ProfileNotificationParams) => {
			const { id, title, notificationType, value } = node;

			return new ProfileNotification({ id, title, notificationType, value });
		}) ?? [];

	console.log(allProfileNotifications);

	return [allProfileNotifications, omit(result, ['data'])];
};

export default useProfileNotifications;
