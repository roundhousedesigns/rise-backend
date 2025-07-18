import { Box, BoxProps, Flex, FlexProps, Heading } from '@chakra-ui/react';
import type { As } from '@chakra-ui/system';
import { ReactNode } from 'react';

interface Props {
	lineColor: string;
	children: ReactNode;
	headingAs?: As;
	headingProps?: BoxProps;
}

export default function HeadingCenterline({
	lineColor,
	children,
	headingAs,
	headingProps,
	...props
}: Props & FlexProps): JSX.Element {
	return (
		<Flex alignItems='center' w='full' h='max-content' pos='relative' textAlign='left' {...props}>
			<Box h='4px' top='50%' bgColor={lineColor} pos='absolute' w='full' zIndex='1'></Box>
			<Heading as={headingAs ? headingAs : 'h3'} variant='centerline' {...headingProps}>
				{children}
			</Heading>
		</Flex>
	);
}
