import { Flex, IconProps, ListItem, ListItemProps, Text, useMediaQuery } from '@chakra-ui/react';
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
	const [isLargerThanMd] = useMediaQuery('(min-width: 36rem)');

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
				justifyContent='flex-start'
				flexWrap='nowrap'
				pr={isExpanded ? 8 : 0}
				pl={isExpanded ? 4 : 3.5}
				gap={2}
				w='100%'
				textDecoration='none'
				transition='all 200ms ease-in-out'
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
				<Text
					flex='1'
					display='block'
					pr={isExpanded ? 2 : 0}
					pos='relative'
					left={isExpanded ? 0 : 3}
					opacity={isExpanded ? 1 : 0}
					transition='all 200ms ease-in-out'
				>
					{children}
				</Text>
			</Flex>
		</ListItem>
	);
}
