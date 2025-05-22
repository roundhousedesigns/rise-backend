import {
	Avatar,
	Box,
	Card,
	CardHeader,
	CardProps,
	Flex,
	Heading,
	Link,
	useColorMode,
} from '@chakra-ui/react';
import { WPPost } from '@lib/classes';
import parse from 'html-react-parser';

interface Props {
	post: WPPost;
}

export default function ShortPost({ post, ...props }: Props & CardProps): JSX.Element {
	const { id, title, content, uri, featuredImage } = post;

	const { colorMode } = useColorMode();

	return (
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
				<Heading variant='contentSubtitle' my={0}>
					<Link href={uri} isExternal>
						{title ? title : ' '}
					</Link>
				</Heading>
			</CardHeader>
			{content && (
				<Flex my={0} px={3} gap={2}>
					{featuredImage?.sourceUrl && (
						<Avatar src={featuredImage.sourceUrl} name={title || 'Post thumbnail'} size='lg' />
					)}
					<Box>{parse(content)}</Box>
				</Flex>
			)}
		</Card>
	);
}
