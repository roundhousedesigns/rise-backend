import { Box, Button, List, ListItem, ListProps, Spinner, Text } from '@chakra-ui/react';
import ShortPost from '@components/ShortPost';
import { useLocalStorage } from '@hooks/hooks';
import { WPPost } from '@lib/classes';
import { AnimatePresence, motion } from 'framer-motion';
import { useEffect, useState } from 'react';

interface Props {
	feedUrl: string;
	limit?: number;
}

interface FeedState {
	posts: WPPost[];
	loading: boolean;
	error: string | null;
}

const CORS_PROXY = 'https://api.allorigins.win/raw?url=';
const STORAGE_EXPIRY = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
const BATCH_SIZE = 5;

const parseRSSItems = (xmlDoc: Document): WPPost[] => {
	const items = xmlDoc.getElementsByTagName('item');
	return Array.from(items).map((item, index) => {
		const title = item.getElementsByTagName('title')[0]?.textContent || '';
		const content = item.getElementsByTagName('description')[0]?.textContent || '';
		const link = item.getElementsByTagName('link')[0]?.textContent || '';
		const enclosure = item.getElementsByTagName('media:thumbnail')[0];
		const thumbnail = enclosure?.getAttribute('url') || '';

		return new WPPost({
			id: index + 1,
			title,
			content,
			uri: link,
			postType: 'rss',
			featuredImage: thumbnail
				? {
						id: 0,
						sourceUrl: thumbnail,
				  }
				: undefined,
		});
	});
};

const useRSSFeed = (feedUrl: string): FeedState => {
	const [state, setState] = useState<FeedState>({
		posts: [],
		loading: true,
		error: null,
	});

	useEffect(() => {
		const fetchFeed = async () => {
			try {
				setState((prev) => ({ ...prev, loading: true, error: null }));

				const response = await fetch(CORS_PROXY + encodeURIComponent(feedUrl));
				const xmlText = await response.text();
				const parser = new DOMParser();
				const xmlDoc = parser.parseFromString(xmlText, 'text/xml');
				const parsedPosts = parseRSSItems(xmlDoc);

				setState({
					posts: parsedPosts,
					loading: false,
					error: null,
				});
			} catch (err) {
				setState({
					posts: [],
					loading: false,
					error: 'Failed to load RSS feed',
				});
				console.error('Error fetching RSS feed:', err);
			}
		};

		fetchFeed();
	}, [feedUrl]);

	return state;
};

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
	feedUrl,
	limit = BATCH_SIZE,
	...props
}: Props & ListProps): JSX.Element {
	const storageKey = `rss-feed-${feedUrl}-visible-count`;
	const [storedVisibleCount, setStoredVisibleCount] = useLocalStorage(storageKey, limit, {
		expiresIn: STORAGE_EXPIRY,
	});

	const { posts: allPosts, loading, error } = useRSSFeed(feedUrl);
	const [visiblePosts, setVisiblePosts] = useState<WPPost[]>([]);

	useEffect(() => {
		setVisiblePosts(allPosts.slice(0, storedVisibleCount));
	}, [allPosts, storedVisibleCount]);

	const handleLoadMore = () => {
		const currentLength = visiblePosts.length;
		const nextBatch = allPosts.slice(currentLength, currentLength + BATCH_SIZE);
		const newVisibleCount = currentLength + nextBatch.length;
		setVisiblePosts([...visiblePosts, ...nextBatch]);
		setStoredVisibleCount(newVisibleCount);
	};

	const hasMorePosts = visiblePosts.length < allPosts.length;

	if (loading) return <LoadingState />;
	if (error) return <ErrorState message={error} />;

	return (
		<List spacing={4} align='stretch' {...props}>
			{visiblePosts.length > 0 && (
				<AnimatePresence>
					{visiblePosts.map((post) => (
						<ListItem
							key={post.id}
							as={motion.div}
							initial={{ opacity: 0 }}
							animate={{ opacity: 1 }}
							exit={{ opacity: 0 }}
						>
							<ShortPost post={post} />
						</ListItem>
					))}
					{hasMorePosts && (
						<Box textAlign='center' py={2}>
							<Button onClick={handleLoadMore} colorScheme='blue' variant='outline'>
								Load More
							</Button>
						</Box>
					)}
				</AnimatePresence>
			)}
		</List>
	);
}
