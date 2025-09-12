import { Flex, Icon, IconProps, Link, LinkProps, Spacer, Text } from '@chakra-ui/react';
import { MouseEventHandler, ReactNode } from 'react';

interface Props {
	href?: string;
	onClick?: MouseEventHandler;
	icon: any;
	iconSide?: 'left' | 'right';
	isExternal?: boolean;
	children: ReactNode;
	iconProps?: IconProps;
}

export default function LinkWithIcon({
	href,
	onClick,
	icon,
	iconSide = 'left',
	isExternal,
	children,
	iconProps,
	...props
}: Props & LinkProps): React.JSX.Element {
	return (
		<Link
			href={href ? href : undefined}
			onClick={onClick ? onClick : undefined}
			fontWeight='medium'
			display='inline-block'
			variant='dotted'
			isExternal={!!isExternal}
			my={0}
			{...props}
		>
			<Flex
				alignItems='center'
				flexDirection={iconSide === 'left' ? 'row' : 'row-reverse'}
				justifyContent='flex-start'
				gap={0}
				w='auto'
			>
				<Icon as={icon} pos='relative' mr={2} {...iconProps} />
				<Text as='span' m={0} lineHeight='normal' display='block'>
					{children}
				</Text>
				<Spacer />
			</Flex>
		</Link>
	);
}
