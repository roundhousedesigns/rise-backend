import { Card, List } from '@chakra-ui/react';
import UpcomingEventListItem from '@components/UpcomingEventListItem';
import useUpcomingEvents from '@queries/useUpcomingEvents';

export default function EventsList() {
	const [events, { loading, error }] = useUpcomingEvents();

	if (loading) return <div>Loading...</div>;
	if (error) return <div>Error: {error.message}</div>;

	return events.length ? (
		<Card py={1}>
			<List my={2}>
				{events.map((event) => (
					<UpcomingEventListItem key={event.id} event={event} />
				))}
			</List>
		</Card>
	) : null;
}
