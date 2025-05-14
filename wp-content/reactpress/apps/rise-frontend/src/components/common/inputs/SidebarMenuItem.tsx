import { Flex, IconProps, ListIcon, ListItem, ListItemProps, Text } from '@chakra-ui/react';
import { ReactNode } from 'react';
import { IconType } from 'react-icons/lib';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	icon: IconType;
	target: string | (() => void);
	isActive?: boolean;
	iconProps?: IconProps;
	children: ReactNode;
}

export default function SidebarMenuItem({
	icon,
	target,
	isActive,
	iconProps,
	children,
	...props
}: Props & ListItemProps) {
	return (
		<ListItem
			w='full'
			borderBottomWidth='1px'
			borderBottomColor='gray.700'
			_last={{ borderBottomWidth: '0px' }}
			{...props}
		>
			<Flex
				as={RouterLink}
				to={typeof target === 'string' ? target : undefined}
				onClick={typeof target === 'function' ? target : undefined}
				alignItems='center'
				justifyContent='flex-start'
				gap={1}
				px={4}
				textDecoration='none'
				w='full'
				transition='background-color 200ms ease'
				_light={{
					bg: isActive ? 'gray.500' : 'transparent',
				}}
				_dark={{
					bg: isActive ? 'gray.800' : 'transparent',
				}}
				_hover={{
					_light: {
						bg: 'gray.400',
					},
					_dark: {
						bg: 'gray.700',
					},
				}}
			>
				<ListIcon as={icon} {...iconProps} />
				<Text my={0}>{children}</Text>
			</Flex>
		</ListItem>
	);
}
