/**
 * useUpdateJobPost hook. Mutation to create or update a Job Post.
 */

import { gql, useMutation } from '@apollo/client';
import { JobPostOutput } from '@lib/types';

const MUTATE_UPDATE_JOB_POST = gql`
	mutation UpdateOrCreateJobPost($input: UpdateOrCreateJobPostInput = {}) {
		updateOrCreateJobPost(input: $input) {
			updatedJobPost {
				id: databaseId
			}
			awaitingPayment
			wcCheckoutEndpoint
		}
	}
`;

const useUpdateJobPost = () => {
	const [mutation, results] = useMutation(MUTATE_UPDATE_JOB_POST);

	const updateJobPostMutation = (jobPost: JobPostOutput) => {
		return mutation({
			variables: {
				input: {
					jobPost,
				},
			},
		});
	};

	return { updateJobPostMutation, results };
};

export default useUpdateJobPost;
