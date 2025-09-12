import { Box, Button, ButtonGroup, Heading, Text } from '@chakra-ui/react';
import useViewer from '@hooks/queries/useViewer';
import { Link } from 'react-router-dom';

interface Props {
	title?: string;
}

export default function NetworkPartnerManagementLinks({
	title = 'Manage Events',
}: Props): React.JSX.Element | null {
	const [{ networkPartnerManagementLinks }] = useViewer();

	return !!networkPartnerManagementLinks ? (
		<Box>
			<Heading variant='contentSubtitle'>{title}</Heading>
			<Text variant='helperText'>
				As one of our Network Partners, you can publicize your organization's upcoming events on the
				RISE public calendar.
			</Text>
			<ButtonGroup>
				<Button as={Link} to={networkPartnerManagementLinks.addEvent} target='_blank'>
					Add Event
				</Button>
				<Button as={Link} to={networkPartnerManagementLinks.listEvents} target='_blank'>
					Manage Events
				</Button>
			</ButtonGroup>
		</Box>
	) : null;
}
