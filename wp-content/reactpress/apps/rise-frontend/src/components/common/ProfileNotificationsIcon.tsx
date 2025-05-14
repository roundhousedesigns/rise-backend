import { Circle, Flex, Icon } from '@chakra-ui/react';
import { FiBell } from 'react-icons/fi';

interface Props {
	number: number;
}

export default function ProfileNotificationsIcon({ number }: Props) {
	return (
		<Circle position='relative'>
			<Icon as={FiBell} color='text.light' />
			<Flex
				borderRadius='full'
				position='absolute'
				bottom={-5}
				right={-3}
				_light={{ bg: 'gray.600' }}
				_dark={{ bg: 'text.dark' }}
				justifyContent='center'
				alignItems='center'
				textAlign='center'
			>
				{number > 0 && (
					<Circle
						color='text.dark'
						bg='brand.yellow'
						m={0.5}
						py={0.5}
						px={1}
						minW='1.5em'
						fontSize='2xs'
						textAlign='center'
					>
						{number}
					</Circle>
				)}
			</Flex>
		</Circle>
	);
}
