import { Box, BoxProps, Heading } from '@chakra-ui/react';
import HeadingCenterline from './HeadingCenterline';

interface Props {
	title?: string;
	titleStyle?: 'centerline' | 'contentTitle';
	centerLineColor?: string;
	children: JSX.Element;
}

const Widget = ({
	children,
	title,
	titleStyle,
	centerLineColor = 'brand.orange',
	...props
}: Props & BoxProps) => (
	<Box {...props}>
		{title && (
			<>
				{titleStyle === 'centerline' && (
					<HeadingCenterline lineColor={centerLineColor} headingProps={{ fontSize: '2xl' }} mb={2}>
						{title}
					</HeadingCenterline>
				)}
				{titleStyle === 'contentTitle' && (
					<Heading as='h2' variant='contentTitle'>
						{title}
					</Heading>
				)}
			</>
		)}
		{children}
	</Box>
);

export default Widget;
