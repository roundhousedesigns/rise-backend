import { gql, useMutation } from '@apollo/client';
import { QUERY_PROFILE_NOTIFICATIONS } from '@queries/useProfileNotifications';
import useViewer from '@queries/useViewer';
const MUTATE_DISMISS_PROFILE_NOTIFICATION = gql`
	mutation DismissProfileNotification($id: ID = "") {
		dismissProfileNotification(input: { id: $id }) {
			success
			clientMutationId
		}
	}
`;

const useDismissProfileNotification = () => {
	const [{ loggedInId }] = useViewer();
	const [mutation, results] = useMutation(MUTATE_DISMISS_PROFILE_NOTIFICATION);

	const dismissProfileNotificationMutation = (id: number) => {
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

	return { dismissProfileNotificationMutation, results };
};

export default useDismissProfileNotification;
