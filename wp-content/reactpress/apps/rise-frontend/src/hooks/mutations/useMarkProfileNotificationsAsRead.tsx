import { gql, useMutation } from '@apollo/client';
import { QUERY_PROFILE_NOTIFICATIONS } from '@queries/useProfileNotifications';
import useViewer from '@queries/useViewer';

const MUTATE_MARK_PROFILE_NOTIFICATIONS_AS_READ = gql`
	mutation markProfileNotificationsAsRead($ids: [ID] = []) {
		markProfileNotificationsAsRead(input: { ids: $ids }) {
			success
			clientMutationId
		}
	}
`;

const useMarkProfileNotificationsAsRead = () => {
	const [{ loggedInId }] = useViewer();
	const [mutation, results] = useMutation(MUTATE_MARK_PROFILE_NOTIFICATIONS_AS_READ);

	const markProfileNotificationsAsReadMutation = (ids: number[]) => {
		return mutation({
			variables: {
				ids,
			},
			refetchQueries: [
				{
					query: QUERY_PROFILE_NOTIFICATIONS,
					variables: {
						authorId: loggedInId,
						limit: -1,
					},
				},
			],
		});
	};

	return { markProfileNotificationsAsReadMutation, results };
};

export default useMarkProfileNotificationsAsRead;
