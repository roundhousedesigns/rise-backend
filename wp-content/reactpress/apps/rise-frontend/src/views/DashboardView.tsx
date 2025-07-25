import { Card, Grid, GridItem, List, ListItem, Skeleton, Stack } from '@chakra-ui/react';
import ColorCascadeBox from '@common/ColorCascadeBox';
import Widget from '@common/Widget';
import DashboardRSSFeeds from '@components/DashboardRSSFeeds';
import EventsList from '@components/EventsList';
import NetworkPartnerManagementLinks from '@components/NetworkPartnerManagementLinks';
import ShortPost from '@components/ShortPost';
import useUserNotices from '@queries/useUserNotices';
import useUserProfile from '@queries/useUserProfile';
import useViewer from '@queries/useViewer';
import MiniProfileView from '@views/MiniProfileView';

export default function DashboardView() {
	const [{ loggedInId, isNetworkPartner }] = useViewer();
	const [notices] = useUserNotices();

	const [profile, { loading: profileLoading }] = useUserProfile(loggedInId);

	return (
		<Grid
			templateColumns={{ base: '1fr', md: 'minmax(300px, 1fr) auto' }}
			gap={8}
			w='full'
			maxW='6xl'
		>
			<GridItem
				as={Stack}
				spacing={6}
				id='dashboard-secondary'
				maxW={{ base: 'none', md: '300px' }}
			>
				<Widget>
					<ColorCascadeBox>
						{profile ? (
							<MiniProfileView profile={profile} />
						) : profileLoading ? (
							<Skeleton height='200px' />
						) : (
							<></>
						)}
					</ColorCascadeBox>
				</Widget>

				<Widget title='Partner Events' titleStyle='centerline'>
					<EventsList />
				</Widget>

				{isNetworkPartner && (
					<Widget title='Network Partner' titleStyle='centerline'>
						<NetworkPartnerManagementLinks />
					</Widget>
				)}
			</GridItem>

			<GridItem as={Stack} spacing={2} id='dashboard-primary' justifyContent='flex-start'>
				{notices.length > 0 && (
					<Widget title='RISE News' titleStyle='centerline'>
						<List>
							{notices.map((notice: any) => (
								<ListItem key={notice.id}>
									<ShortPost post={notice} mb={4} as={Card} />
								</ListItem>
							))}
						</List>
					</Widget>
				)}

				<Widget title='Industry News' titleStyle='centerline'>
					<Card
						my={0}
						gap={2}
						opacity={0.9}
						transition='opacity 200ms ease'
						_hover={{ opacity: 1 }}
					>
						<DashboardRSSFeeds />
					</Card>
				</Widget>
			</GridItem>
		</Grid>
	);
}
