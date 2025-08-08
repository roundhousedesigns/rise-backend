/**
 * useRegisterUser hook. Mutation to register a new user.
 */

import { gql, useMutation } from '@apollo/client';
import { RegisterUserInput } from '@lib/types';

const MUTATE_REGISTER_USER = gql`
	mutation RegisterUserMutation($input: RegisterUserInput = { username: "" }) {
		registerUser(input: $input) {
			user {
				databaseId
			}
		}
	}
`;

const useRegisterUser = () => {
	const [mutation, results] = useMutation(MUTATE_REGISTER_USER);

	const registerUserMutation = (user: RegisterUserInput) => {
		const { email, firstName, lastName, password, confirmPassword, isOrg, orgName } = user;

		if (password !== confirmPassword) {
			throw new Error('Passwords do not match');
		}

		return mutation({
			variables: {
				input: {
					username: email,
					email,
					lastName,
					firstName,
					password,
					isOrg,
					orgName,
				},
			},
		});
	};

	return { registerUserMutation, results };
};

export default useRegisterUser;
