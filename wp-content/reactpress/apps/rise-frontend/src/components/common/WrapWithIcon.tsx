import { BoxProps, Flex, Icon } from '@chakra-ui/react';
import { ReactNode } from 'react';

interface Props {
	icon: any;
	iconProps?: any;
	children: ReactNode;
}

export default function WrapWithIcon({
	icon,
	iconProps,
	children,
	...props
}: Props & BoxProps): React.JSX.Element {
	return (
		<Flex my={2} alignItems='center' {...props}>
			{icon ? <Icon as={icon} mr={2} {...iconProps} /> : null}
			{children}
		</Flex>
	);
}
