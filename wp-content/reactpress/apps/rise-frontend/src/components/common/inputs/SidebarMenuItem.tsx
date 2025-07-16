import { Flex, ListItem, ListItemProps, Text } from '@chakra-ui/react';
import { ReactNode } from 'react';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	icon?: ReactNode;
	target: string | (() => void);
	isActive?: boolean;
	isExpanded?: boolean;
	children: ReactNode;
}

export default function SidebarMenuItem({
	icon,
	target,
	isActive,
	isExpanded,
	children,
	...props
}: Props & ListItemProps) {
	return (
		<ListItem
			w='full'
			borderBottomWidth='1.5px'
			borderBottomStyle='dotted'
			_dark={{ borderBottomColor: 'gray.700' }}
			_light={{ borderBottomColor: 'whiteAlpha.300' }}
			{...props}
		>
			<Flex
				as={RouterLink}
				to={typeof target === 'string' ? target : undefined}
				onClick={typeof target === 'function' ? target : undefined}
				alignItems='center'
				justifyContent='flex-start'
				flexWrap='nowrap'
				pr={isExpanded ? 4 : 0}
				pl={isExpanded ? 4 : 3.5}
				gap={2}
				w='100%'
				textDecoration='none'
				transition='all 0.15s ease-in-out'
				color='text.light'
				_light={{
					bg: isActive ? 'whiteAlpha.400' : 'transparent',
				}}
				_dark={{
					bg: isActive ? 'blackAlpha.600' : 'transparent',
				}}
				_hover={{
					_light: {
						bg: 'whiteAlpha.200',
					},
					_dark: {
						bg: 'blackAlpha.400',
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
					transition='all 0.3s ease-in-out'
					fontFamily='heading'
				>
					{children}
				</Text>
			</Flex>
		</ListItem>
	);
}
