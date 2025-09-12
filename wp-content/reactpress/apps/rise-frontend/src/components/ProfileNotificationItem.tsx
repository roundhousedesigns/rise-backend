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
	const link = (() => {
		switch (notificationType) {
			case 'test_notification':
				return '';
			case 'starred_profile_updated':
				return userProfile ? profileUrl : '';
			case 'no_profile_credits':
			case 'new_user':
				return '/profile/edit';
			// TODO Enable this when jobs are live
			// case 'job_posted':
			// 	return `/jobs/${value}`;
			default:
				return '';
		}
	})();

	return (
		<Box onMouseEnter={handleMarkAsRead} onFocus={handleMarkAsRead}>
			<Flex alignItems='center' gap={1} fontSize='sm'>
				{!isRead && (
					<Icon as={FiCircle} fill='brand.orange' color='brand.orange' my={0} boxSize={2} />
				)}
				<Box _dark={{ color: 'text.light' }} _light={{ color: 'text.dark' }}>
					<Flex
						justifyContent='space-between'
						alignItems='flex-end'
						w='full'
						m={0}
						_notLast={{
							borderBottom: '1px solid',
							borderColor: 'gray.400',
						}}
						borderColor='gray.400'
					>
						<Box fontSize='sm'>
							{link ? (
								<Link as={RouterLink} to={link} m={0} fontWeight={isRead ? 'normal' : 'bold'}>
									{title}
								</Link>
							) : (
								<Text m={0} fontWeight={isRead ? 'normal' : 'bold'}>
									{title}
								</Text>
							)}
						</Box>
						<Text as='span' mt={0} m={0} ml={1} fontSize='2xs' fontStyle='italic'>
							{dateTime.toLocaleString(undefined, {
								year: 'numeric',
								month: 'numeric',
								day: 'numeric',
								hour: 'numeric',
								minute: 'numeric',
							})}
						</Text>
					</Flex>
					{notificationType === 'test_notification' || notificationType === 'no_profile_credits' ? (
						<Text
							flex='0 0 100%'
							fontSize='2xs'
							fontFamily='special'
							lineHeight='short'
							my={1}
							mb={0}
						>
							{notificationType === 'no_profile_credits'
								? "Add some credits to your profile to make sure you're listed in our Directory!"
								: value}
						</Text>
					) : null}
				</Box>
				<IconButton
					icon={<FiX />}
					aria-label='Dismiss'
					size='sm'
					transform='scale(0.6)'
					borderRadius='full'
					colorScheme='red'
					pos='relative'
					bottom={1}
					onClick={handleDismiss}
				/>
			</Flex>
		</Box>
	);
}
