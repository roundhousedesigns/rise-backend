import {
	Avatar,
	Box,
	Card,
	CardHeader,
	CardProps,
	Flex,
	Heading,
	LinkBox,
	LinkOverlay,
	Text,
	useColorMode,
} from '@chakra-ui/react';
import { RSSPost } from '@lib/classes';
import { RSSPostFieldMap } from '@lib/types';
import parse from 'html-react-parser';

interface Props {
	post: RSSPost;
	fieldMap?: RSSPostFieldMap;
}

export default function RSSPostItem({ post, fieldMap, ...props }: Props & CardProps): JSX.Element {
	const { id, title, content, uri, date, thumbnail } = post;

	const { colorMode } = useColorMode();

	const formattedDate = date
		? new Date(date).toLocaleDateString(undefined, {
				year: 'numeric',
				month: 'long',
				day: 'numeric',
		  })
		: '';

	// Get the display title, falling back to empty string if not available
	const displayTitle = title?.trim() || ' ';

	return (
		<LinkBox aria-labelledby={`rss-post-${id}`}>
			<Card
				pt={0}
				px={0}
				my={0}
				gap={2}
				opacity={0.9}
				transition='opacity 200ms ease'
				_hover={{ opacity: uri ? 1 : 0.9 }}
				{...props}
			>
				<CardHeader px={3} py={2} bg={colorMode === 'dark' ? 'blackAlpha.300' : 'blackAlpha.100'}>
					<Flex gap={2} alignItems='flex-start' justifyContent='space-between' flex='1'>
						<Heading variant='contentSubtitle' my={0} id={`rss-post-${id}`} lineHeight='short'>
							<LinkOverlay href={uri} isExternal textDecoration='none' color='initial'>
								{displayTitle}
							</LinkOverlay>
						</Heading>
						{formattedDate && (
							<Text fontSize='xs' color='gray.500' m={0} textAlign='right' flex='1 0 auto'>
								{formattedDate}
							</Text>
						)}
					</Flex>
				</CardHeader>
				{content && (
					<Flex my={0} px={3} gap={2}>
						{thumbnail && <Avatar src={thumbnail} name={displayTitle} size='lg' />}
						<Box>{parse(content)}</Box>
					</Flex>
				)}
			</Card>
		</LinkBox>
	);
}
