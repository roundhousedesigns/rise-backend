import {
	Box,
	Button,
	GridItem,
	GridProps,
	SimpleGrid,
	Skeleton,
	Spinner,
	Text
} from '@chakra-ui/react';
import RSSPostItem from '@components/RSSPostItem';
import { useRSSFeed } from '@hooks/hooks';
import { RSSPostFieldMap } from '@lib/types';
import { AnimatePresence, motion } from 'framer-motion';
import { useMemo, useState } from 'react';

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

	// Fetch posts from all feeds
	const feedResults = feeds.map((feed) => useRSSFeed(feed.feedUrl, feed.fieldMap));

	// Combine all posts and sort by date - memoized to prevent recalculation
	const allPosts = useMemo(
		() =>
			feedResults
				.flatMap((result, index) =>
					result.posts.map((post) => ({
						post,
						fieldMap: feeds[index].fieldMap,
						feedTitle: feeds[index].title,
					}))
				)
				.sort((a, b) => new Date(b.post.date).getTime() - new Date(a.post.date).getTime()),
		[feedResults, feeds]
	);

	// Check if any feed is loading
	const loading = feedResults.some((result) => result.loading);

	// Get the first error if any feed has an error
	const error = feedResults.find((result) => result.error)?.error;

	const handleLoadMore = () => {
		setVisibleCount((prev) => prev + limit);
	};

	const visiblePosts = allPosts.slice(0, visibleCount);
	const hasMorePosts = visiblePosts.length < allPosts.length;

	if (error) return <ErrorState message={error} />;

	return (
		<Box w='full'>
			<Skeleton isLoaded={!loading}>
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
