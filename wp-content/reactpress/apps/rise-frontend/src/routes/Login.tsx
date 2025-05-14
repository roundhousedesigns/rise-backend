import Shell from '@layout/Shell';
import useViewer from '@queries/useViewer';
import LoginView from '@views/LoginView';
import { useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';

export default function Login() {
	const navigate = useNavigate();
	const [params] = useSearchParams();
	const alert = params.get('alert');
	const alertStatus = params.get('alertStatus');

	const [{ loggedInId }] = useViewer();

	// If the user is logged in, redirect them to the home page.
	useEffect(() => {
		if (loggedInId) {
			navigate('/');
		}
	});

	return (
		<Shell title='Sign in to RISE' mx='auto'>
			<LoginView alert={alert ? alert : ''} alertStatus={alertStatus ? alertStatus : ''} />
		</Shell>
	);
}
