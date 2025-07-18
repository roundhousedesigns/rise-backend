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
}: Props & BoxProps): JSX.Element {
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
		<Box
			/*_notLast={{ borderBottom: '1px solid', borderColor: 'gray.200', pb: 4, mb: 2 }}*/ {...props}
		>
			{title ? <SectionTitle /> : false}
			{children}
		</Box>
	);
}
