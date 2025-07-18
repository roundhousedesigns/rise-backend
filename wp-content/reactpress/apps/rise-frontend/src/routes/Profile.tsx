import { Button, ButtonGroup } from '@chakra-ui/react';
import Shell from '@layout/Shell';
import useUserIdBySlug from '@queries/useUserIdBySlug';
import useUserProfile from '@queries/useUserProfile';
import useViewer from '@queries/useViewer';
import ProfileView from '@views/ProfileView';
import { FiEdit3 } from 'react-icons/fi';
import { Link as RouterLink, useParams } from 'react-router-dom';

export default function Profile(): JSX.Element {
	const [{ loggedInId, loggedInSlug }] = useViewer();
	const params = useParams();

	const slug = params.slug ? params.slug : '';
	const [userId] = useUserIdBySlug(slug);
	const profileIsLoggedInUser = loggedInSlug === slug;

	const [profile, { loading }] = useUserProfile(userId);

	const PageActions = () => (
		<ButtonGroup size='md' alignItems='center'>
			{profileIsLoggedInUser && (
				<Button
					aria-label='Edit profile'
					leftIcon={<FiEdit3 />}
					as={RouterLink}
					to='/profile/edit'
					colorScheme='green'
				>
					Edit profile
				</Button>
			)}
		</ButtonGroup>
	);

	return (
		<Shell
			title={profileIsLoggedInUser ? 'My Profile' : ''}
			actions={<PageActions />}
			loading={loading}
			pb={8}
		>
			{profile ? <ProfileView profile={profile} allowStar={loggedInId !== userId} /> : false}
		</Shell>
	);
}
