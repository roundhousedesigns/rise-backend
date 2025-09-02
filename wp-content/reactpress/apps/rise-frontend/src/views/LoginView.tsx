import {
	Alert,
	Box,
	Button,
	Divider,
	Flex,
	Heading,
	Highlight,
	Link,
	Stack,
	Text,
	chakra,
	useMediaQuery,
} from '@chakra-ui/react';
import TextInput from '@common/inputs/TextInput';
import Turnstile from '@common/Turnstile';
import { useErrorMessage } from '@hooks/hooks';
import { LoginInput } from '@lib/types';
import { decodeString } from '@lib/utils';
import useLogin from '@mutations/useLogin';
import { ChangeEvent, FormEvent, useState } from 'react';
import { Link as RouterLink, useNavigate } from 'react-router-dom';

interface Props {
	alert?: string;
	alertStatus?: string;
	signInTitle?: boolean;
}

export default function LoginView({ alert, alertStatus, signInTitle }: Props) {
	const { VITE_DEV_MODE, VITE_WP_URL } = import.meta.env;

	const [credentials, setCredentials] = useState<LoginInput>({
		login: '',
		password: '',
	});
	const [errorCode, setErrorCode] = useState<string>('');
	const [turnstileStatus, setTurnstileStatus] = useState<'error' | 'expired' | 'solved' | ''>('');

	const [isLargerThanMd] = useMediaQuery('(min-width: 48rem)');

	const {
		loginMutation,
		results: { loading: submitLoading },
	} = useLogin();

	const errorMessage = useErrorMessage(errorCode);

	const navigate = useNavigate();

	const handleInputChange = (e: ChangeEvent<HTMLInputElement>) => {
		setCredentials({
			...credentials,
			[e.target.name]: e.target.value,
		});
	};

	const handleLoginSubmit = (e: FormEvent) => {
		e.preventDefault();

		loginMutation({ ...credentials })
			.then((res) => {
				if (res.data?.directoryLogin?.roles?.includes('administrator')) {
					window.location.href = `${VITE_WP_URL}/wp-admin`;
				} else {
					navigate('/');
				}
			})
			.catch((errors: { message: string }) => {
				setErrorCode(errors.message);
			});
	};

	const sanitizedAlertStatus = alertStatus === 'error' ? 'error' : 'success';

	return (
		<>
			<Flex alignItems='center' gap={8} flexWrap='wrap' maxW='4xl' mx='auto' my={12}>
				<Box flex='1'>
					<Box maxWidth='md'>
						{signInTitle ? (
							<Heading variant='pageTitle' as='h1' my={0} lineHeight='normal'>
								Sign in to RISE
							</Heading>
						) : (
							false
						)}
						<Text fontSize='lg'>
							You'll need an account to create a profile or to search for candidates.
						</Text>
						<Divider my={4} />
						<Box flex='1 1 auto'>
							{alert ? <Alert status={sanitizedAlertStatus}>{alert}</Alert> : false}
							<chakra.form onSubmit={handleLoginSubmit}>
								<TextInput
									value={credentials.login}
									name='login'
									label='Email'
									autoComplete='username'
									isRequired
									onChange={handleInputChange}
									error={
										['invalid_username', 'invalid_email', 'invalid_account'].includes(errorCode)
											? errorMessage
											: ''
									}
									inputProps={{
										autoComplete: 'username',
										fontSize: 'lg',
									}}
								/>
								<TextInput
									value={credentials.password}
									name='password'
									label='Password'
									isRequired
									onChange={handleInputChange}
									error={errorCode === 'incorrect_password' ? errorMessage : ''}
									inputProps={{
										type: 'password',
										autoComplete: 'current-password',
										fontSize: 'lg',
									}}
								/>

								<Box mt={2}>
									<Turnstile
										onError={() => setTurnstileStatus('error')}
										onExpire={() => setTurnstileStatus('expired')}
										onSuccess={() => setTurnstileStatus('solved')}
									/>
								</Box>

								<Flex
									gap={4}
									alignItems='center'
									justifyContent='space-between'
									mt={2}
									flexWrap='wrap'
								>
									{turnstileStatus === 'solved' || VITE_DEV_MODE ? (
										<Button type='submit' colorScheme='blue' px={6} isLoading={!!submitLoading}>
											Sign In
										</Button>
									) : null}
									{/* TODO Un-hardcode the lost password link */}
									<Link href={`${VITE_WP_URL}/womp-womp`} fontSize='sm' my={0}>
										Lost your password?
									</Link>
								</Flex>

								{turnstileStatus === 'error' ||
									(turnstileStatus === 'expired' && (
										<Alert status='error'>
											There was an error verifying your browser. Please try again.
										</Alert>
									))}

								<Divider />

								<Box textAlign='center' flex='1' mb={0}>
									<Heading variant='pageSubtitle' fontSize='xl'>
										Don't have an account?
									</Heading>
									<Button
										as={RouterLink}
										to='/register'
										borderRadius={{ base: 'md', md: 'lg' }}
										colorScheme='green'
										color='text.dark'
										size='lg'
									>
										Join Now
									</Button>
								</Box>
							</chakra.form>
						</Box>
					</Box>
				</Box>
				{!isLargerThanMd ? <Divider my={0} /> : false}
				<Box textAlign='center' flex='1' pb={{ base: 8, md: 2 }}>
					<Stack textAlign='center' gap={4}>
						<Heading as='h2' my={0} fontSize={{ base: '2xl', md: '3xl' }}>
							<Highlight query={['project']} styles={{ bg: 'blue.200' }}>
								Find your next project
							</Highlight>
							<br />
							<Highlight query={['team']} styles={{ bg: 'green.200' }}>
								Discover your next team
							</Highlight>
						</Heading>
						<Box>
							<Button
								as={RouterLink}
								to={`${VITE_WP_URL}/about`}
								target='_blank'
								size='2xl'
								colorScheme='yellow'
							>
								{`What is RISE? ${decodeString('&raquo;')}`}
							</Button>
						</Box>
					</Stack>
				</Box>
			</Flex>
		</>
	);
}
