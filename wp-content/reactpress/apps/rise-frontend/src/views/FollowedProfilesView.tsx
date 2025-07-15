import { Box, Icon } from '@chakra-ui/react';
import InlineIconText from '@components/InlineIconText';
import FollowedProfileList from '@views/FollowedProfileList';
import { FiStar } from 'react-icons/fi';

export default function FollowedProfilesView() {
	return (
		<Box>
			<InlineIconText
				icon={<Icon as={FiStar} fill='brand.orange' />}
				iconProps={{ _dark: { bg: 'whiteAlpha.200' }, _light: { bg: 'blackAlpha.100' } }}
				text='Click the star button next to a profile to save it or remove it from your list.'
				query='star'
				description='star'
				fontSize='md'
			/>
			<FollowedProfileList mt={4} />
		</Box>
	);
}
