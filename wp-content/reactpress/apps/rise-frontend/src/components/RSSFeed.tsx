import { Box, Button, List, ListItem, ListProps, Spinner, Text } from '@chakra-ui/react';
import RSSPostItem from '@components/RSSPostItem';
import { RSSPost } from '@lib/classes';
import { RSSPostFieldMap } from '@lib/types';
import { generateRandomString } from '@lib/utils';
import { AnimatePresence, motion } from 'framer-motion';
import { useEffect, useState } from 'react';

interface Props {
	feedUrl: string;
	limit?: number;
	fieldMap?: RSSPostFieldMap;
}

interface FeedState {
	posts: RSSPost[];
	loading: boolean;
	error: string | null;
}

const CORS_PROXY = 'https://api.allorigins.win/raw?url=';
const BATCH_SIZE = 5;

const parseRSSItems = (xmlDoc: Document, fieldMap?: RSSPostFieldMap): RSSPost[] => {
	const items = xmlDoc.getElementsByTagName('item');

	return Array.from(items).map((item) => {
		const getElementContent = (field: keyof RSSPostFieldMap, defaultTag: string) => {
			const tagName = fieldMap?.[field] || defaultTag;
			return item.getElementsByTagName(tagName)[0]?.textContent || '';
		};

		const getElementAttribute = (
			field: keyof RSSPostFieldMap,
			defaultTag: string,
			attribute: string
		) => {
			const tagName = fieldMap?.[field] || defaultTag;
			return item.getElementsByTagName(tagName)[0]?.getAttribute(attribute) || '';
		};

		// Map all fields using the fieldMap if provided
		const title = getElementContent('title', 'title');
		const content = getElementContent('content', 'description');
		const link = getElementContent('link', 'link');
		const date = getElementContent('date', 'pubDate');
		const thumbnail = getElementAttribute('thumbnail', 'media:thumbnail', 'url');

		return new RSSPost({
			id: generateRandomString(5),
			title,
			content,
			uri: link,
			date,
			thumbnail,
		});
	});
};

const useRSSFeed = (feedUrl: string, fieldMap?: RSSPostFieldMap): FeedState => {
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
				const parsedPosts = parseRSSItems(xmlDoc, fieldMap);

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
	}, [feedUrl, fieldMap]);

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
	fieldMap,
	...props
}: Props & ListProps): JSX.Element {
	const [visibleCount, setVisibleCount] = useState(limit);
	const { posts: allPosts, loading, error } = useRSSFeed(feedUrl, fieldMap);
	const [visiblePosts, setVisiblePosts] = useState<RSSPost[]>([]);

	useEffect(() => {
		setVisiblePosts(allPosts.slice(0, visibleCount));
	}, [allPosts, visibleCount]);

	const handleLoadMore = () => {
		const currentLength = visiblePosts.length;
		const nextBatch = allPosts.slice(currentLength, currentLength + BATCH_SIZE);
		const newVisibleCount = currentLength + nextBatch.length;
		setVisiblePosts([...visiblePosts, ...nextBatch]);
		setVisibleCount(newVisibleCount);
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
							<RSSPostItem post={post} fieldMap={fieldMap} />
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
