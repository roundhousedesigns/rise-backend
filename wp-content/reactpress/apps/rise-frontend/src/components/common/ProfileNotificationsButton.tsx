import { Button, Circle, Icon } from '@chakra-ui/react';
import { forwardRef } from 'react';
import { FiBell } from 'react-icons/fi';

interface ProfileNotificationsIconProps {
	number: number;
}

const ProfileNotificationsButton = forwardRef<HTMLButtonElement, ProfileNotificationsIconProps>(
	({ number }, ref) => {
		const UnreadCount = () => (
			<Circle
				size={3}
				bg='brand.orange'
				color='white'
				position='absolute'
				bottom={1.5}
				right={2.5}
				fontSize='3xs'
			>
				{number}
			</Circle>
		);

		return (
			<Button ref={ref} position='relative' colorScheme='yellow' variant='ghost' tabIndex={0}>
				<Icon as={FiBell} boxSize={5} />
				{number > 0 && <UnreadCount />}
			</Button>
		);
	}
);

export default ProfileNotificationsButton;
