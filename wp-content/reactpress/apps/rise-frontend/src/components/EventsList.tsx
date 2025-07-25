import { List, ListProps, Spinner } from '@chakra-ui/react';
import UpcomingEventListItem from '@components/UpcomingEventListItem';
import useUpcomingEvents from '@queries/useUpcomingEvents';

interface EventsListProps {
	showTitle?: boolean;
	showManagement?: boolean;
}

export default function EventsList({
	showTitle = true,
	showManagement = false,
	...props
}: EventsListProps & ListProps) {
	const [events, { loading, error }] = useUpcomingEvents();

	if (loading) return <Spinner />;
	if (error) return <div>Error: {error.message}</div>;

	return events.length ? (
		<List my={2} {...props}>
			{events.map((event) => (
				<UpcomingEventListItem key={event.id} event={event} />
			))}
		</List>
	) : null;
}
