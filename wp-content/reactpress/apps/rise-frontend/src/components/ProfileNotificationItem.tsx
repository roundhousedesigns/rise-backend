import { Box, Flex, Icon, IconButton, Link, Text } from '@chakra-ui/react';
import { useProfileUrl } from '@hooks/hooks';
import { Candidate, ProfileNotification } from '@lib/classes';
import useDismissProfileNotification from '@mutations/useDismissProfileNotification';
import useMarkProfileNotificationAsRead from '@mutations/useMarkProfileNotificationAsRead';
import useCandidates from '@queries/useCandidates';
import { isEqual } from 'lodash';
import { useEffect, useState } from 'react';
import { FiAlertCircle, FiX } from 'react-icons/fi';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	notification: ProfileNotification;
}

export default function ProfileNotificationItem({ notification }: Props) {
	const { id, notificationType, value, title, dateTime, isRead } = notification;
	const [userProfile, setUserProfile] = useState<Candidate | null>(null);
	const profileUrl = useProfileUrl(userProfile?.slug || '');
	const { markProfileNotificationAsReadMutation } = useMarkProfileNotificationAsRead();
	const { dismissProfileNotificationMutation } = useDismissProfileNotification();

	// Parse profile ID from notification value if needed
	const profileId = notificationType === 'starred_profile_updated' ? parseInt(value) : null;

	const [candidates] = useCandidates(profileId ? [profileId] : []);

	const handleMarkAsRead = () => {
		markProfileNotificationAsReadMutation(id);
	};

	const handleDismiss = () => {
		dismissProfileNotificationMutation(id);
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
		<Box>
			<Flex fontSize='sm' w='full' m={0} alignItems='center' justifyContent='space-between' gap={2}>
				<Flex alignItems='center' gap={1}>
					{!isRead && <Icon as={FiAlertCircle} color='brand.orange' my={0} />}
					<Box>
						<Link
							as={RouterLink}
							to={link}
							m={0}
							onClick={handleMarkAsRead}
							fontWeight={isRead ? 'normal' : 'bold'}
						>
							{title}
						</Link>
						<Text as='span' fontSize='xs' color='gray.500' my={0} ml={1}>
							{dateTime.toLocaleString(undefined, {
								year: 'numeric',
								month: 'numeric',
								day: 'numeric',
								hour: 'numeric',
								minute: 'numeric',
							})}
						</Text>
					</Box>
				</Flex>
				<IconButton
					icon={<FiX />}
					aria-label='Dismiss'
					size='2xs'
					colorScheme='red'
					onClick={handleDismiss}
				/>
			</Flex>
		</Box>
	);
}
