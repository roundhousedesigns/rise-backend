/**
 * useSendPasswordResetEmail hook. Mutation to send the user a password reset email.
 */

import { gql, useMutation } from '@apollo/client';

const MUTATE_SEND_PASSWORD_RESET = gql`
	mutation SendPasswordResetEmailMutation($input: SendPasswordResetEmailInput = { username: "" }) {
		sendPasswordResetEmail(input: $input) {
			clientMutationId
			success
		}
	}
`;

const useSendPasswordResetEmail = () => {
	const [mutation, results] = useMutation(MUTATE_SEND_PASSWORD_RESET);

	const sendPasswordResetEmailMutation = ({ username }: { username: string }) => {
		return mutation({
			variables: {
				input: {
					clientMutationId: 'sendPasswordResetEmailMutation',
					username,
				},
			},
		});
	};

	return { sendPasswordResetEmailMutation, results };
};

export default useSendPasswordResetEmail;
