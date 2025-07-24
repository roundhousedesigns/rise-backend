import { Heading, ListItem, ListItemProps, Stack } from '@chakra-ui/react';
import LinkWithIcon from '@common/LinkWithIcon';
import { UpcomingEvent } from '@lib/classes';
import { formatUpcomingEventDate } from '@lib/utils';
import parse from 'html-react-parser';
import { FiClock, FiExternalLink, FiMapPin } from 'react-icons/fi';
import WrapWithIcon from './common/WrapWithIcon';

interface UpcomingEventListItemProps {
	event: UpcomingEvent;
}

export default function UpcomingEventListItem({
	event,
	...props
}: UpcomingEventListItemProps & ListItemProps) {
	const { id, title, partnerName, startDate, endDate, link, location } = event;
	const eventId = `tribe_event_${id}`;

	return (
		<ListItem id={eventId} mb={3} {...props}>
			<Heading variant='contentSubtitle' as='h4' mb={0}>
				<LinkWithIcon
					icon={FiExternalLink}
					iconSide='right'
					iconProps={{ ml: 1, boxSize: 3 }}
					href={link}
					target='_blank'
				>
					{parse(title)}
				</LinkWithIcon>
			</Heading>
			<Heading as='h5' fontSize='sm' lineHeight='shorter' variant='contentSubtitle'>
				Hosted by {partnerName}
			</Heading>
			<Stack fontSize='sm' lineHeight='shorter'>
				<WrapWithIcon icon={FiClock} m={0}>
					{formatUpcomingEventDate(startDate, endDate)}
				</WrapWithIcon>
				{location && (
					<WrapWithIcon icon={FiMapPin} m={0}>
						{location}
					</WrapWithIcon>
				)}
			</Stack>
		</ListItem>
	);
}
