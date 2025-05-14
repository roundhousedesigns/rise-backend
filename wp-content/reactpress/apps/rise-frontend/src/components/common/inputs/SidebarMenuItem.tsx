import { Flex, IconProps, ListItem, ListItemProps, Text } from '@chakra-ui/react';
import { ReactNode } from 'react';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	icon?: ReactNode;
	target: string | (() => void);
	isActive?: boolean;
	iconProps?: IconProps;
	isExpanded?: boolean;
	children: ReactNode;
}

export default function SidebarMenuItem({
	icon,
	target,
	isActive,
	iconProps,
	isExpanded,
	children,
	...props
}: Props & ListItemProps) {
	return (
		<ListItem
			w='full'
			borderBottomWidth='1px'
			borderBottomColor='gray.700'
			_first={{ borderTopWidth: '1px' }}
			{...props}
		>
			<Flex
				as={RouterLink}
				to={typeof target === 'string' ? target : undefined}
				onClick={typeof target === 'function' ? target : undefined}
				alignItems='center'
				justifyContent={isExpanded ? 'center' : 'flex-start'}
				gap={2}
				px={4}
				textDecoration='none'
				w='100%'
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
				{icon}
				<Text visibility={isExpanded ? 'visible' : 'hidden'} flex={isExpanded ? 1 : 0}>
					{children}
				</Text>
			</Flex>
		</ListItem>
	);
}
