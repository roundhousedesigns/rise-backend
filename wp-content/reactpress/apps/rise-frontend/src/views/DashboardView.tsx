import { Card, Grid, GridItem, List, ListItem, Spinner, Stack } from '@chakra-ui/react';
import ColorCascadeBox from '@common/ColorCascadeBox';
import Widget from '@common/Widget';
import ProfileNotificationItem from '@components/ProfileNotificationItem';
import ShortPost from '@components/ShortPost';
import useProfileNotifications from '@queries/useProfileNotifications';
import useUserNotices from '@queries/useUserNotices';
import useUserProfile from '@queries/useUserProfile';
import useViewer from '@queries/useViewer';
import MiniProfileView from '@views/MiniProfileView';
import { AnimatePresence, motion } from 'framer-motion';
import StarredProfileList from './StarredProfileList';

export default function DashboardView() {
	const [{ loggedInId, starredProfiles }] = useViewer();
	const [notices] = useUserNotices();
	const [{ unread, read }] = useProfileNotifications(loggedInId);

	const [profile, { loading: profileLoading }] = useUserProfile(loggedInId);

	return (
		<Grid
			templateColumns={{ base: '1fr', md: 'minmax(340px, 1fr) auto' }}
			gap={8}
			w='full'
			maxW='6xl'
		>
			<GridItem as={Stack} spacing={6} id='dashboard-secondary'>
				<Widget>
					<ColorCascadeBox>
						{profile ? (
							<MiniProfileView profile={profile} borderWidth='2px' borderColor='brand.blue' />
						) : profileLoading ? (
							<Spinner />
						) : (
							<></>
						)}
					</ColorCascadeBox>
				</Widget>

				{unread.length > 0 ||
					(read.length > 0 && (
						<Widget title='Notifications' titleStyle='contentTitle'>
							<Card gap={2}>
								<List spacing={1}>
									<AnimatePresence>
										{unread?.map((notification) => (
											<ListItem
												key={notification.id}
												as={motion.div}
												initial={{ opacity: 1 }}
												animate={{ opacity: 1 }}
												exit={{ opacity: 0 }}
											>
												<ProfileNotificationItem notification={notification} />
											</ListItem>
										))}
										{read?.map((notification) => (
											<ListItem key={notification.id}>
												<ProfileNotificationItem notification={notification} />
											</ListItem>
										))}
									</AnimatePresence>
								</List>
							</Card>
						</Widget>
					))}

				{starredProfiles?.length && (
					<Widget title='Following' titleStyle='centerline' centerLineColor='brand.orange'>
						<StarredProfileList showToggle={false} mini />
					</Widget>
				)}
			</GridItem>
			<GridItem as={Stack} spacing={2} id='dashboard-primary' justifyContent='flex-start'>
				{notices.length > 0 ? (
					<Widget title='News' titleStyle='centerline' centerLineColor='brand.orange'>
						<List spacing={4}>
							{notices.map((notice: any) => (
								<ListItem key={notice.id} mt={0}>
									<ShortPost post={notice} mb={4} as={Card} />
								</ListItem>
							))}
						</List>
					</Widget>
				) : null}
			</GridItem>
		</Grid>
	);
}
