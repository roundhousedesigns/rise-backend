import { Card, Grid, GridItem, List, ListItem, Skeleton, Stack } from '@chakra-ui/react';
import ColorCascadeBox from '@common/ColorCascadeBox';
import Widget from '@common/Widget';
import DashboardRSSFeeds from '@components/DashboardRSSFeeds';
import ShortPost from '@components/ShortPost';
import useUserNotices from '@queries/useUserNotices';
import useUserProfile from '@queries/useUserProfile';
import useViewer from '@queries/useViewer';
import FollowedProfileList from '@views/FollowedProfileList';
import MiniProfileView from '@views/MiniProfileView';
import NetworkPartnerManagementLinks from '../components/NetworkPartnerManagementLinks';

export default function DashboardView() {
	const [{ loggedInId, starredProfiles, isOrg, isNetworkPartner }] = useViewer();
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

				{isNetworkPartner && (
					<Widget title='Partner Events' titleStyle='centerline'>
						<NetworkPartnerManagementLinks />
					</Widget>
				)}

				{notices.length > 0 ? (
					<Widget title='RISE News' titleStyle='centerline'>
						<List>
							{notices.map((notice: any) => (
								<ListItem key={notice.id} my={0}>
									<ShortPost post={notice} mb={4} as={Card} />
								</ListItem>
							))}
						</List>
					</Widget>
				) : null}
			</GridItem>

			<GridItem as={Stack} spacing={2} id='dashboard-primary' justifyContent='flex-start'>
				<Widget title='Partner Events' titleStyle='centerline'>
					<p>-- EVENTS --</p>
				</Widget>

				<Widget title='Industry News' titleStyle='centerline'>
					<DashboardRSSFeeds />
				</Widget>

				{starredProfiles?.length && (
					<Widget title='Following' titleStyle='centerline' mt={1}>
						<FollowedProfileList mini showToggle={false} mt={1} />
					</Widget>
				)}
			</GridItem>
		</Grid>
	);
}
