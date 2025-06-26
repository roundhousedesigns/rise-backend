import Shell from '@layout/Shell';
import useViewer from '@queries/useViewer';
import LoginView from '@views/LoginView';
import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

export default function Login() {
	const navigate = useNavigate();

	const [{ loggedInId }] = useViewer();

	// If the user is logged in, redirect them to the home page.
	useEffect(() => {
		if (loggedInId) {
			navigate('/');
		}
	});

	return (
		<Shell title='Sign in to RISE' mx='auto'>
			<LoginView />
		</Shell>
	);
}
