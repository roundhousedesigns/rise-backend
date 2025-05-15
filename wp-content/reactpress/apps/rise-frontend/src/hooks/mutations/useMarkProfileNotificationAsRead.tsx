import { gql, useMutation } from '@apollo/client';
import { QUERY_PROFILE_NOTIFICATIONS } from '@queries/useProfileNotifications';
import useViewer from '@queries/useViewer';

const MUTATE_MARK_PROFILE_NOTIFICATION_AS_READ = gql`
	mutation MarkProfileNotificationAsRead($id: ID = "") {
		markProfileNotificationAsRead(input: { id: $id }) {
			success
			clientMutationId
		}
	}
`;

const useMarkProfileNotificationAsRead = () => {
	const [{ loggedInId }] = useViewer();
	const [mutation, results] = useMutation(MUTATE_MARK_PROFILE_NOTIFICATION_AS_READ);

	const markProfileNotificationAsReadMutation = (id: number) => {
		return mutation({
			variables: {
				id,
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

	return { markProfileNotificationAsReadMutation, results };
};

export default useMarkProfileNotificationAsRead;
