import { Box, BoxProps, Heading } from '@chakra-ui/react';
import HeadingCenterline from '@common/HeadingCenterline';
import { ReactNode } from 'react';

interface Props {
	title?: string;
	centerlineColor?: string;
	children: ReactNode;
}

export default function ProfileStackItem({
	title,
	centerlineColor,
	children,
	...props
}: Props & BoxProps): React.JSX.Element {
	const SectionTitle = () => {
		return centerlineColor ? (
			<HeadingCenterline lineColor={centerlineColor}>{title}</HeadingCenterline>
		) : (
			<Heading as='h3' variant='fieldSectionTitle'>
				{title}
			</Heading>
		);
	};

	return (
		<Box {...props}>
			{title ? <SectionTitle /> : false}
			{children}
		</Box>
	);
}
