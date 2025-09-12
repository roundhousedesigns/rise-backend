import { Container, Flex, Spinner, Text } from '@chakra-ui/react';
import useViewer from '@queries/useViewer';
import LoginView from '@views/LoginView';
import { ReactNode, useEffect } from 'react';
import { useLocation } from 'react-router-dom';

interface Props {
	hideOnly?: boolean;
	children: ReactNode;
}

/**
 * Renders the children component if the user is logged in, otherwise displays the login view.
 *
 * @param {Props} props - Component props
 * @param {boolean} props.hideOnly -  Determines whether to hide the child component or not. Defaults to false.
 * @param {ReactNode} props.children - The component to render if the user is logged in.
 */
export default function LoggedIn({ hideOnly, children }: Props): React.JSX.Element {
	const [{ loggedInId }, { loading }] = useViewer();

	// get the current route
	const { pathname } = useLocation();

	// Detect if we're in the middle of an admin redirect triggered by a fresh login
	let adminRedirecting = false;
	try {
		adminRedirecting = sessionStorage.getItem('rise_admin_login_redirect') === '1';
	} catch {}

	// Clear the flag immediately so it only affects the post-login render
	useEffect(() => {
		if (adminRedirecting) {
			try {
				sessionStorage.removeItem('rise_admin_login_redirect');
			} catch {}
		}
		// We intentionally don't include dependencies to run once per mount
		// and clear any residual flag set by a fresh login redirect
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, []);

	// Allowed URL endpoints when logged out
	const publicEndpoints = ['/register', '/lost-password', '/reset-password'];

	const showContent =
		(!hideOnly && !loggedInId && publicEndpoints.includes(pathname)) || loggedInId;

	return loading || adminRedirecting ? (
		<Flex alignItems='center'>
			<Spinner />
			{adminRedirecting && <Text>Redirecting to admin...</Text>}
		</Flex>
	) : showContent ? (
		<>{children}</>
	) : (
		<Container p={0} mt={8} maxW='4xl'>
			<LoginView signInTitle={true} />
		</Container>
	);
}
