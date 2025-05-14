import { useMediaQuery } from '@chakra-ui/react';
import BackToLoginButton from '@common/BackToLoginButton';
import Shell from '@layout/Shell';
import RegisterView from '@views/RegisterView';

export default function Register() {
	const [isLargerThanMd] = useMediaQuery('(min-width: 48rem)');
	const Button = () => (isLargerThanMd ? <BackToLoginButton /> : <></>);

	return (
		<Shell title='Join the Directory' actions={<Button />}>
			<RegisterView />
		</Shell>
	);
}
