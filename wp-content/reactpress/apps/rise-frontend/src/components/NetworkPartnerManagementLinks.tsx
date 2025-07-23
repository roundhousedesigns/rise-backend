import { Box, Link, List, ListItem, Text } from '@chakra-ui/react';
import useViewer from '@hooks/queries/useViewer';
import { Link as RouterLink } from 'react-router-dom';

export default function NetworkPartnerManagementLinks(): JSX.Element | null {
	const [{ networkPartnerManagementLinks }] = useViewer();

	return !!networkPartnerManagementLinks ? (
		<Box>
			<Text variant='helperText'>
				Add and manage your organization's events to include them on the RISE public calendar of
				events.
			</Text>
			<List display='flex'>
				<ListItem>
					<Link as={RouterLink} to={networkPartnerManagementLinks.addEvent}>
						Add Event
					</Link>
				</ListItem>
			</List>
		</Box>
	) : null;
}
