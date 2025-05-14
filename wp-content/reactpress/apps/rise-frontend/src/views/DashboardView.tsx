import {
	Card,
	Divider,
	Grid,
	GridItem,
	Heading,
	List,
	ListItem,
	Spinner,
	Stack,
	VisuallyHidden,
} from '@chakra-ui/react';
import ColorCascadeBox from '@common/ColorCascadeBox';
import HeadingCenterline from '@common/HeadingCenterline';
import Widget from '@common/Widget';
import ProfileNotificationMenuItem from '@components/ProfileNotificationMenuItem';
import SavedSearchItemList from '@components/SavedSearchItemList';
import ShortPost from '@components/ShortPost';
import useProfileNotifications from '@queries/useProfileNotifications';
import useUserNotices from '@queries/useUserNotices';
import useUserProfile from '@queries/useUserProfile';
import useViewer from '@queries/useViewer';
import MiniProfileView from '@views/MiniProfileView';
import StarredProfileList from '@views/StarredProfileList';

export default function DashboardView() {
	const [{ loggedInId, starredProfiles }] = useViewer();
	const [notices] = useUserNotices();
	const [{ unread, read }] = useProfileNotifications(loggedInId);

	const [profile, { loading }] = useUserProfile(loggedInId);

	return (
		<Grid
			templateColumns={{ base: '1fr', md: 'minmax(300px, 1fr) auto' }}
			gap={8}
			w='full'
			maxW='6xl'
		>
			<GridItem as={Stack} spacing={6} id='dashboard-secondary' maxW='300px'>
				<Widget>
					{profile ? (
						<ColorCascadeBox>
							<MiniProfileView profile={profile} borderWidth='2px' borderColor='brand.blue' />
						</ColorCascadeBox>
					) : loading ? (
						<Spinner />
					) : (
						<></>
					)}
				</Widget>

				<Widget>
					<>
						<Heading as='h2' variant='contentTitle'>
							Notifications
						</Heading>
						{unread.length > 0 && (
							<Card gap={2} colorScheme='gray'>
								<VisuallyHidden>
									<Heading as='h3' variant='contentSubtitle' mb={0} lineHeight='shortest'>
										Unread
									</Heading>
								</VisuallyHidden>
								<List spacing={1}>
									{unread.map((notification) => (
										<ListItem key={notification.id}>
											<ProfileNotificationMenuItem notification={notification} />
										</ListItem>
									))}
								</List>
							</Card>
						)}

						{unread.length > 0 && read.length > 0 && <Divider />}

						{read.length > 0 && (
							<Card gap={2}>
								<VisuallyHidden>
									<Heading as='h3' variant='contentSubtitle' mb={0} lineHeight='shortest'>
										Read
									</Heading>
								</VisuallyHidden>
								<List spacing={1}>
									{read.map((notification) => (
										<ListItem key={notification.id}>
											<ProfileNotificationMenuItem notification={notification} />
										</ListItem>
									))}
								</List>
							</Card>
						)}
					</>
				</Widget>

				<Widget>
					<>
						<Heading as='h2' variant='contentTitle'>
							Saved Searches
						</Heading>
						<SavedSearchItemList />
					</>
				</Widget>
			</GridItem>
			<GridItem as={Stack} spacing={2} id='dashboard-primary' justifyContent='flex-start'>
				{notices.length > 0 ? (
					<Widget>
						<>
							<HeadingCenterline lineColor='brand.orange' headingProps={{ fontSize: '2xl' }}>
								News
							</HeadingCenterline>
							<List spacing={4}>
								{notices.map((notice: any) => (
									<ListItem key={notice.id} mt={0}>
										<ShortPost post={notice} mb={4} as={Card} />
									</ListItem>
								))}
							</List>
						</>
					</Widget>
				) : null}

				{starredProfiles?.length ? (
					<Widget>
						<>
							<HeadingCenterline lineColor='brand.orange' headingProps={{ fontSize: '2xl' }}>
								Following
							</HeadingCenterline>
							<StarredProfileList showToggle={false} />
						</>
					</Widget>
				) : null}
			</GridItem>
		</Grid>
	);
}
