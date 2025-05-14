import { Box, Flex, IconButton, LinkBox, LinkOverlay, MenuItem } from '@chakra-ui/react';
import { useProfileUrl } from '@hooks/hooks';
import { Candidate, ProfileNotification } from '@lib/classes';
import useCandidates from '@queries/useCandidates';
import { isEqual } from 'lodash';
import { useEffect, useState } from 'react';
import { FiX } from 'react-icons/fi';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	notification: ProfileNotification;
}

export default function ProfileNotificationMenuItem({ notification }: Props) {
	const { notificationType, value, title } = notification;
	const [userProfile, setUserProfile] = useState<Candidate | null>(null);
	const profileUrl = useProfileUrl(userProfile?.slug || '');

	// Parse profile ID from notification value if needed
	const profileId = notificationType === 'starred_profile_updated' ? parseInt(value) : null;

	const [candidates] = useCandidates(profileId ? [profileId] : []);

	const handleDismiss = () => {
		console.log('dismissing notification', notification);
		// TODO: Implement dismiss
	};

	// Update user profile when candidates are loaded
	useEffect(() => {
		if (candidates?.length && !isEqual(candidates[0], userProfile)) {
			setUserProfile(candidates[0]);
		}
	}, [candidates, userProfile]);

	// Generate notification link based on type
	const link =
		notificationType === 'starred_profile_updated'
			? userProfile
				? profileUrl
				: ''
			: notificationType === 'job_posted'
			? `/jobs/${value}`
			: '';

	return (
		<MenuItem as={Box} _hover={{ bg: 'whiteAlpha.50' }} w='full'>
			<LinkBox w='full'>
				<Flex
					fontSize='xs'
					w='full'
					m={0}
					alignItems='center'
					justifyContent='space-between'
					gap={2}
				>
					<LinkOverlay as={RouterLink} to={link} textDecoration='none'>
						{title}
					</LinkOverlay>
					<IconButton
						icon={<FiX />}
						aria-label='Dismiss'
						size='2xs'
						colorScheme='red'
						onClick={handleDismiss}
					/>
				</Flex>
			</LinkBox>
		</MenuItem>
	);
}
