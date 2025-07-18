import { Box, Flex, Icon, IconButton, Link, Text } from '@chakra-ui/react';
import { useProfileUrl } from '@hooks/hooks';
import { Candidate, ProfileNotification } from '@lib/classes';
import useDismissProfileNotifications from '@mutations/useDismissProfileNotifications';
import useMarkProfileNotificationsAsRead from '@mutations/useMarkProfileNotificationsAsRead';
import useCandidates from '@queries/useCandidates';
import { isEqual } from 'lodash';
import { useEffect, useState } from 'react';
import { FiCircle, FiX } from 'react-icons/fi';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	notification: ProfileNotification;
}

export default function ProfileNotificationItem({ notification }: Props) {
	const { id, notificationType, value, title, dateTime, isRead } = notification;
	const [userProfile, setUserProfile] = useState<Candidate | null>(null);
	const profileUrl = useProfileUrl(userProfile?.slug || '');
	const { markProfileNotificationsAsReadMutation } = useMarkProfileNotificationsAsRead();
	const { dismissProfileNotificationsMutation } = useDismissProfileNotifications();

	// Notification type: starred_profile_updated
	// `value` is the profile ID of the starred profile
	const [profiles] = useCandidates(
		notificationType === 'starred_profile_updated' ? [parseInt(value)] : []
	);

	const handleMarkAsRead = () => {
		markProfileNotificationsAsReadMutation([id]);
	};

	const handleDismiss = () => {
		dismissProfileNotificationsMutation([id]);
	};

	// Update user profile when candidates are loaded
	useEffect(() => {
		if (profiles?.length && !isEqual(profiles[0], userProfile)) {
			setUserProfile(profiles[0]);
		}
	}, [profiles, userProfile]);

	// Generate notification link based on type
	const link =
		notificationType === 'test_notification'
			? ''
			: notificationType === 'starred_profile_updated'
			? userProfile
				? profileUrl
				: ''
			: notificationType === 'job_posted'
			? `/jobs/${value}`
			: '';

	return (
		<Box onMouseEnter={handleMarkAsRead} onFocus={handleMarkAsRead}>
			<Flex fontSize='sm' w='full' m={0} alignItems='center' justifyContent='space-between' gap={2}>
				<Flex alignItems='center' gap={1}>
					{!isRead && (
						<Icon as={FiCircle} fill='brand.orange' color='brand.orange' my={0} boxSize={2} />
					)}
					<Box>
						{link ? (
							<Link as={RouterLink} to={link} m={0} fontWeight={isRead ? 'normal' : 'bold'}>
								{title}
							</Link>
						) : (
							<Text m={0} as='span' fontWeight={isRead ? 'normal' : 'bold'}>
								{title}
							</Text>
						)}
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
