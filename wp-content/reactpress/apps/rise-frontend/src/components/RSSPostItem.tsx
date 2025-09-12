import { Box, BoxProps, Heading, Link, Text, chakra } from '@chakra-ui/react';
import { RSSPost } from '@lib/classes';
import { RSSPostFieldMap } from '@lib/types';

interface Props {
	post: RSSPost;
	fieldMap?: RSSPostFieldMap;
	feedTitle?: string;
}

export default function RSSPostItem({
	post,
	fieldMap,
	feedTitle,
	...props
}: Props & BoxProps): React.JSX.Element {
	const { title, uri, date } = post;

	const formattedDate = date
		? new Date(date).toLocaleDateString(undefined, {
				year: 'numeric',
				month: 'long',
				day: 'numeric',
		  })
		: '';

	const dateDisplay = [feedTitle, formattedDate].filter(Boolean).join(' | ');

	return (
		<Box {...props}>
			<Heading variant='contentSubtitle' my={0} lineHeight='shorter' fontSize='md'>
				<Link href={uri} isExternal>
					<chakra.span mr={1}>{title}</chakra.span>
				</Link>
			</Heading>
			{dateDisplay && (
				<Text
					as='span'
					fontSize='xs'
					_dark={{ color: 'gray.400' }}
					_light={{ color: 'gray.600' }}
					fontWeight='normal'
					my={0}
				>
					{dateDisplay}
				</Text>
			)}
		</Box>
	);
}
