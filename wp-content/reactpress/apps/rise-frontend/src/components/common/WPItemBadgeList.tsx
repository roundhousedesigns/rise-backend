import { Wrap, WrapProps } from '@chakra-ui/react';
import WPItemBadgeListItem from '@common/WPItemBadgeListItem';
import { WPItem } from '@lib/classes';
import { decodeString } from '@lib/utils';

interface Props {
	items: WPItem[];
	colorScheme?: string;
}

export default function WPItemBadgeList({
	items,
	colorScheme,
	...props
}: Props & WrapProps): React.JSX.Element {
	return (
		<Wrap spacing={2} justify={{ base: 'left', md: 'right' }} {...props}>
			{items?.map((item: WPItem) => (
				<WPItemBadgeListItem key={item.id} id={item.id.toString()} colorScheme={colorScheme}>
					{decodeString(item.name || '')}
				</WPItemBadgeListItem>
			))}
		</Wrap>
	);
}
