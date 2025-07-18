import { gql, useMutation } from '@apollo/client';
import { QUERY_PROFILE_NOTIFICATIONS } from '@queries/useProfileNotifications';
import useViewer from '@queries/useViewer';
const MUTATE_DISMISS_PROFILE_NOTIFICATIONS = gql`
	mutation dismissProfileNotifications($ids: [ID] = []) {
		dismissProfileNotifications(input: { ids: $ids }) {
			success
			clientMutationId
		}
	}
`;

const useDismissProfileNotifications = () => {
	const [{ loggedInId }] = useViewer();
	const [mutation, results] = useMutation(MUTATE_DISMISS_PROFILE_NOTIFICATIONS);

	const dismissProfileNotificationsMutation = (ids: number[]) => {
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

	return { dismissProfileNotificationsMutation, results };
};

export default useDismissProfileNotifications;
