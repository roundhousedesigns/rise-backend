/**
 * useTECUpcomingEvents hook. Query to retrieve Events.
 */

import { gql, useQuery } from '@apollo/client';
import { UpcomingEvent } from '@lib/classes';
import { omit } from 'lodash';

export const QUERY_UPCOMING_EVENTS = gql`
	query UpcomingEventsQuery {
		upcomingEvents {
			endDate
			id
			partnerName
			link
			location
			startDate
			title
		}
	}
`;

const useUpcomingEvents = (): [UpcomingEvent[], any] => {
	const result = useQuery(QUERY_UPCOMING_EVENTS);

	if (!result?.data?.upcomingEvents) {
		return [[], omit(result, ['data'])];
	}

	const upcomingEvents: UpcomingEvent[] =
		result?.data?.upcomingEvents?.map((node: any) => {
			const { id, title, partnerName, startDate, endDate, link, location } = node;

			return new UpcomingEvent({
				id,
				title,
				partnerName,
				startDate,
				endDate,
				link,
				location,
			});
		}) ?? [];

	return [upcomingEvents, omit(result, ['data'])];
};

export default useUpcomingEvents;
