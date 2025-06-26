import {
	Alert,
	Box,
	Button,
	Divider,
	Drawer,
	DrawerBody,
	DrawerContent,
	DrawerHeader,
	DrawerOverlay,
	Flex,
	Heading,
	Highlight,
	Icon,
	IconButton,
	Link,
	Stack,
	Text,
	chakra,
	useDisclosure,
	useMediaQuery,
} from '@chakra-ui/react';
import TextInput from '@common/inputs/TextInput';
import Turnstile from '@common/Turnstile';
import { useErrorMessage } from '@hooks/hooks';
import { LoginInput } from '@lib/types';
import { decodeString } from '@lib/utils';
import useLogin from '@mutations/useLogin';
import PageContent from '@views/PageContent';
import { ChangeEvent, FormEvent, useState } from 'react';
import { FiExternalLink, FiX } from 'react-icons/fi';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	alert?: string;
	alertStatus?: string;
	signInTitle?: boolean;
}

export default function LoginView({ alert, alertStatus, signInTitle }: Props) {
	const { VITE_DEV_MODE } = import.meta.env;

	const [credentials, setCredentials] = useState<LoginInput>({
		login: '',
		password: '',
	});
	const [errorCode, setErrorCode] = useState<string>('');
	const [turnstileStatus, setTurnstileStatus] = useState<'error' | 'expired' | 'solved' | ''>('');

	const [isLargerThanMd] = useMediaQuery('(min-width: 48rem)');
	const { isOpen, onOpen, onClose } = useDisclosure();

	const {
		loginMutation,
		results: { loading: submitLoading },
	} = useLogin();

	const errorMessage = useErrorMessage(errorCode);

	const handleInputChange = (e: ChangeEvent<HTMLInputElement>) => {
		setCredentials({
			...credentials,
			[e.target.name]: e.target.value,
		});
	};

	const handleLoginSubmit = (e: FormEvent) => {
		e.preventDefault();

		loginMutation({ ...credentials }).catch((errors: { message: string }) => {
			setErrorCode(errors.message);
		});
	};

	const sanitizedAlertStatus = alertStatus === 'error' ? 'error' : 'success';

	return (
		<>
			<Flex alignItems='center' gap={8} flexWrap='wrap' maxW='4xl' mx='auto'>
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
									<Link as={RouterLink} to='/lost-password' fontSize='sm' my={0}>
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
							<Button onClick={onOpen} size='2xl' colorScheme='yellow'>
								{`What is RISE? ${decodeString('&raquo;')}`}
							</Button>
						</Box>
					</Stack>
				</Box>
			</Flex>
			<Drawer
				placement='right'
				size={{ base: 'full', md: 'md' }}
				onClose={onClose}
				closeOnEsc={true}
				isOpen={isOpen}
				isFullHeight={false}
			>
				<DrawerOverlay />
				<DrawerContent display='flex' flexDirection='column' height='100%'>
					<DrawerHeader pt={2} pb={1} px={2} textAlign='right'>
						<IconButton
							onClick={onClose}
							borderRadius='full'
							fontSize='xl'
							icon={<FiX />}
							aria-label='Close'
						/>
					</DrawerHeader>
					<DrawerBody py={0} pb={2}>
						<Box textAlign='center'>
							<PageContent postId='12238' mt={0} pt={0} />
							<Button
								as={Link}
								href='https://risetheatre.org'
								isExternal
								colorScheme='yellow'
								size='lg'
								mt={6}
								mb={24}
							>
								Learn about RISE Theatre{' '}
								<Icon as={FiExternalLink} aria-label='external link' pl={1} />
							</Button>
						</Box>
					</DrawerBody>
				</DrawerContent>
			</Drawer>
		</>
	);
}
