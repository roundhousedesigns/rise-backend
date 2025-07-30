import {
	Box,
	Button,
	GridItem,
	GridProps,
	HStack,
	Icon,
	SimpleGrid,
	Skeleton,
	Spinner,
	Text
} from '@chakra-ui/react';
import RSSPostItem from '@components/RSSPostItem';
import { clearRSSCache, useRSSFeed } from '@hooks/hooks';
import { RSSPostFieldMap } from '@lib/types';
import { AnimatePresence, motion } from 'framer-motion';
import { useMemo, useState } from 'react';
import { HiRefresh } from 'react-icons/hi';

interface Props {
	feeds: {
		title: string;
		feedUrl: string;
		fieldMap?: RSSPostFieldMap;
	}[];
	limit?: number;
	columns?: number;
}

const LoadingState = () => (
	<Box textAlign='center' py={4}>
		<Spinner />
	</Box>
);

const ErrorState = ({ message }: { message: string }) => (
	<Box textAlign='center' py={4}>
		<Text color='red.500'>{message}</Text>
	</Box>
);

export default function RSSFeed({
	feeds,
	limit = 3,
	columns = 2,
	...props
}: Props & GridProps): JSX.Element {
	const [visibleCount, setVisibleCount] = useState(limit);
	const [isRefreshing, setIsRefreshing] = useState(false);

	// Fetch posts from all feeds
	const feedResults = feeds.map((feed) => useRSSFeed(feed.feedUrl, feed.fieldMap));

	// Combine all posts from successful feeds and sort by date - memoized to prevent recalculation
	const allPosts = useMemo(
		() =>
			feedResults
				.flatMap((result, index) => {
					// Only include posts from feeds that loaded successfully
					if (result.error || result.posts.length === 0) {
						return [];
					}
					return result.posts.map((post) => ({
						post,
						fieldMap: feeds[index].fieldMap,
						feedTitle: feeds[index].title,
					}));
				})
				.sort((a, b) => new Date(b.post.date).getTime() - new Date(a.post.date).getTime()),
		[feedResults, feeds]
	);

	// Check if any successful feed is still loading
	const loading = feedResults.some((result) => result.loading && !result.error);

	// Only show error if ALL feeds failed
	const successfulFeeds = feedResults.filter((result) => !result.error);
	const failedFeeds = feedResults.filter((result) => result.error);
	const allFeedsFailed = successfulFeeds.length === 0 && failedFeeds.length > 0;

	// Log feed status for debugging
	useMemo(() => {
		const feedStatus = feedResults.map((result, index) => ({
			feed: feeds[index]?.title || 'Unknown',
			url: feeds[index]?.feedUrl || 'Unknown',
			status: result.loading ? 'loading' : result.error ? 'failed' : `success (${result.posts.length} posts)`,
			error: result.error || null
		}));

		// Always log feed status (helpful for debugging)
		console.groupCollapsed(`📰 RSS Feed Status (${successfulFeeds.length}/${feedResults.length} working)`);
		feedStatus.forEach(feed => {
			if (feed.status.startsWith('success')) {
				console.log(`✅ ${feed.feed}: ${feed.status}`);
			} else if (feed.status === 'failed') {
				console.warn(`❌ ${feed.feed}: ${feed.error}`);
				console.log(`   URL: ${feed.url}`);
			} else {
				console.log(`⏳ ${feed.feed}: ${feed.status}`);
			}
		});
		console.groupEnd();

		// Additional warning if any feeds failed
		if (failedFeeds.length > 0) {
			console.warn(`⚠️  ${failedFeeds.length} RSS feed(s) failed to load`);
		}
	}, [feedResults, feeds, successfulFeeds.length, failedFeeds.length]);

	const handleLoadMore = () => {
		setVisibleCount((prev) => prev + limit);
	};

	const handleRefresh = () => {
		setIsRefreshing(true);
		
		// Clear cache for all feeds
		feeds.forEach(feed => clearRSSCache(feed.feedUrl));
		
		// Refresh the page to reload feeds without cache
		setTimeout(() => {
			window.location.reload();
		}, 500);
	};

	const visiblePosts = allPosts.slice(0, visibleCount);
	const hasMorePosts = visiblePosts.length < allPosts.length;

	// Only show error state if all feeds failed
	if (allFeedsFailed) {
		const errorMessage = failedFeeds.length === 1 
			? failedFeeds[0].error || 'Failed to load RSS feed'
			: `Failed to load ${failedFeeds.length} RSS feeds`;
		return <ErrorState message={errorMessage} />;
	}

	return (
		<Box w='full'>
			{/* Header with refresh button */}
			<HStack justify='space-between' mb={4}>
				<Text fontSize='lg' fontWeight='semibold' color='gray.700'>
					Theater News
				</Text>
				<Button
					onClick={handleRefresh}
					isLoading={isRefreshing}
					loadingText='Refreshing'
					size='sm'
					variant='ghost'
					leftIcon={<Icon as={HiRefresh} />}
					aria-label='Refresh RSS feeds'
				>
					Refresh
				</Button>
			</HStack>

			<Skeleton isLoaded={!loading && !isRefreshing}>
				<SimpleGrid columns={{ base: 1, md: columns }} gap={6} {...props}>
					{visiblePosts.length > 0 && (
						<AnimatePresence>
							{visiblePosts.map(({ post, fieldMap, feedTitle }) => (
								<GridItem
									key={post.id}
									as={motion.div}
									initial={{ opacity: 0 }}
									animate={{ opacity: 1 }}
									exit={{ opacity: 0 }}
								>
									<RSSPostItem post={post} fieldMap={fieldMap} feedTitle={feedTitle} />
								</GridItem>
							))}
						</AnimatePresence>
					)}
				</SimpleGrid>
				{hasMorePosts && (
					<Box textAlign='center' py={2} mt={4}>
						<Button
							onClick={handleLoadMore}
							colorScheme='blue'
							variant='outline'
							aria-label='Load more news headlines'
						>
							Load More
						</Button>
					</Box>
				)}
			</Skeleton>
		</Box>
	);
}
