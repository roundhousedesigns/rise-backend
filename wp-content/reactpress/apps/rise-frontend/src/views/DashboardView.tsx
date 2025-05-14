import { Card, Grid, GridItem, Heading, List, ListItem, Spinner, Stack } from '@chakra-ui/react';
import ColorCascadeBox from '@common/ColorCascadeBox';
import HeadingCenterline from '@common/HeadingCenterline';
import Widget from '@common/Widget';
import SavedSearchItemList from '@components/SavedSearchItemList';
import ShortPost from '@components/ShortPost';
import useSavedSearches from '@queries/useSavedSearches';
import useUserNotices from '@queries/useUserNotices';
import useUserProfile from '@queries/useUserProfile';
import useViewer from '@queries/useViewer';
import MiniProfileView from '@views/MiniProfileView';
import StarredProfileList from '@views/StarredProfileList';

export default function DashboardView() {
	const [{ loggedInId, starredProfiles }] = useViewer();
	const [notices] = useUserNotices();
	const [savedSearches] = useSavedSearches();

	const [profile, { loading }] = useUserProfile(loggedInId);

	return (
		<Grid
			templateColumns={{ base: '1fr', md: 'minmax(300px, 1fr) auto' }}
			gap={8}
			w='full'
			maxW='6xl'
		>
			<GridItem as={Stack} spacing={6} id={'dashboard-secondary'} maxW='300px'>
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
							Saved Searches
						</Heading>
						<SavedSearchItemList />
					</>
				</Widget>
			</GridItem>
			<GridItem as={Stack} spacing={2} id={'dashboard-primary'} justifyContent='flex-start'>
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
