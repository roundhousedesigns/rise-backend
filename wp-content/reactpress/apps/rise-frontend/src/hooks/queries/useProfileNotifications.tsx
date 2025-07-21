/**
 * useProfileNotifications hook. Query to retrieve profile notifications.
 */

import { gql, useQuery } from '@apollo/client';
import { ProfileNotification } from '@lib/classes';
import { ProfileNotificationParams } from '@lib/types';
import { isEmpty, omit } from 'lodash';

export const QUERY_PROFILE_NOTIFICATIONS = gql`
	query ProfileNotificationsQuery($authorId: Int!, $limit: Int = -1) {
		unreadProfileNotifications(authorId: $authorId, limit: $limit) {
			id
			title
			notificationType
			value
			isRead
			dateTime
		}
		readProfileNotifications(authorId: $authorId, limit: $limit) {
			id
			title
			notificationType
			value
			isRead
			dateTime
		}
	}
`;

const useProfileNotifications = (
	authorId: number,
	limit: number = -1
): [{ unread: ProfileNotification[]; read: ProfileNotification[] }, any] => {
	const result = useQuery(QUERY_PROFILE_NOTIFICATIONS, {
		variables: {
			authorId,
			limit,
		},
		pollInterval: 10000,
		fetchPolicy: 'cache-and-network',
	});

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
			const { id, title, notificationType, value, isRead, dateTime } = node;

			const profileNotification = new ProfileNotification({
				id,
				title,
				notificationType,
				value,
				isRead,
				dateTime,
			});

			return profileNotification;
		}) ?? [];

	allProfileNotifications.read =
		result?.data?.readProfileNotifications?.map((node: ProfileNotificationParams) => {
			const { id, title, notificationType, value, isRead, dateTime } = node;

			return new ProfileNotification({ id, title, notificationType, value, isRead, dateTime });
		}) ?? [];

	return [allProfileNotifications, omit(result, ['data'])];
};

export default useProfileNotifications;
