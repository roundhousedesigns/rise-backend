import {
	Box,
	Button,
	ButtonGroup,
	Container,
	Flex,
	Stack,
	Text,
	TextProps,
	useBreakpointValue,
	useDisclosure,
	VisuallyHidden,
} from '@chakra-ui/react';
import DarkModeToggle from '@components/DarkModeToggle';
import DisableProfileToggle from '@components/DisableProfileToggle';
import IsOrgToggle from '@components/IsOrgToggle';
import { SettingsModal } from '@components/SettingsModal';
import SettingsSection from '@components/SettingsSection';
import ChangeEmailView from '@views/ChangeEmailView';
import ChangePasswordView from '@views/ChangePasswordView';
import ChangeProfileSlugView from '@views/ChangeProfileSlugView';
import DeleteAccountView from '@views/DeleteAccountView';

export default function SettingsView() {
	const isLargerThanMd = useBreakpointValue(
		{
			base: false,
			md: true,
		},
		{ ssr: false }
	);

	const {
		isOpen: isOpenPassword,
		onOpen: onOpenPassword,
		onClose: onClosePassword,
	} = useDisclosure();

	const {
		isOpen: isOpenDeleteAccount,
		onOpen: onOpenDeleteAccount,
		onClose: onCloseDeleteAccount,
	} = useDisclosure();

	const { isOpen: isOpenEmail, onOpen: onOpenEmail, onClose: onCloseEmail } = useDisclosure();

	const handlePasswordClick = () => {
		onOpenPassword();
	};

	const handleEmailClick = () => {
		onOpenEmail();
	};

	const handleEmailClose = () => {
		onCloseEmail();
	};

	return (
		<Container maxW='4xl' pl={0} mx={0}>
			<SettingsSection title='Options'>
				<Stack
					direction={['column', 'row']}
					gap={2}
					alignItems='center'
					justifyContent='space-between'
					w='full'
					flexWrap='wrap'
				>
					<DisableProfileToggle
						showHelperText={true}
						size='lg'
						flex='1'
						minW={{ base: 'none', sm: '400px' }}
					/>
					<ResponsiveHelperText isLargerThanMd={isLargerThanMd || false}>
						Set your profile to private to hide it from searches. Your profile will still be
						viewable to anyone with the link, but you will not appear in the Directory. You can turn
						this on or off at any time.
					</ResponsiveHelperText>
				</Stack>
				<Stack
					direction={['column', 'row']}
					gap={2}
					alignItems='center'
					justifyContent='space-between'
					w='full'
					flexWrap='wrap'
				>
					<IsOrgToggle
						showHelperText={true}
						size='lg'
						flex='1'
						minW={{ base: 'none', sm: '400px' }}
					/>
					<ResponsiveHelperText isLargerThanMd={isLargerThanMd || false}>
						Your RISE account can represent either an individual or an organization. Only
						individuals appear in search results, but organizations can share their profiles with
						RISE Members and continue to be a valuable part of the RISE community. If you are a
						company, group, organization, or if your primary use for your RISE account is to search
						our Directory, please check this box.
					</ResponsiveHelperText>
				</Stack>
				<Stack
					direction={['column', 'row']}
					gap={2}
					alignItems='center'
					justifyContent='space-between'
					w='full'
					flexWrap='wrap'
				>
					<DarkModeToggle
						showHelperText={true}
						size='lg'
						flex='1'
						minW={{ base: 'none', sm: '400px' }}
					/>
					<ResponsiveHelperText isLargerThanMd={isLargerThanMd || false}>
						Change the Directory's color mode to light or dark.
					</ResponsiveHelperText>
				</Stack>
			</SettingsSection>

			<SettingsSection title='Account'>
				<Flex gap={2} flexWrap='wrap'>
					<Box>
						<Button onClick={handleEmailClick} colorScheme='gray'>
							Change your email address
						</Button>
						<SettingsModal
							title='Change your account email'
							isOpen={isOpenEmail}
							onClose={handleEmailClose}
						>
							<ChangeEmailView onSubmitCallback={handleEmailClose} />
						</SettingsModal>
					</Box>

					<Box>
						<Button onClick={handlePasswordClick} colorScheme='gray'>
							Change your password
						</Button>
						<SettingsModal
							title='Change your password'
							isOpen={isOpenPassword}
							onClose={onClosePassword}
						>
							<ChangePasswordView />
						</SettingsModal>
					</Box>
				</Flex>
			</SettingsSection>

			<SettingsSection title='Profile'>
				<ChangeProfileSlugView />
			</SettingsSection>

			<SettingsSection title='Close your account'>
				<Text m={0}>
					If you'd like to remove your account entirely and delete your data, please use the button
					below. You can re-register at any time.
				</Text>
				<ButtonGroup>
					<Button colorScheme='red' onClick={onOpenDeleteAccount}>
						Delete account
					</Button>
				</ButtonGroup>
				<SettingsModal
					title='Delete your account'
					isOpen={isOpenDeleteAccount}
					onClose={onCloseDeleteAccount}
				>
					<DeleteAccountView onClose={onCloseDeleteAccount} />
				</SettingsModal>
			</SettingsSection>
		</Container>
	);
}

interface ResponsiveHelperTextProps {
	isLargerThanMd: boolean;
	children: React.ReactNode;
}

const ResponsiveHelperText = ({
	isLargerThanMd,
	children,
	...props
}: ResponsiveHelperTextProps & TextProps) => {
	if (isLargerThanMd) {
		return (
			<Text variant='helperText' flex='1' {...props}>
				{children}
			</Text>
		);
	}
	return (
		<VisuallyHidden>
			<Text as='span' variant='helperText' flex='1' {...props}>
				{children}
			</Text>
		</VisuallyHidden>
	);
};
