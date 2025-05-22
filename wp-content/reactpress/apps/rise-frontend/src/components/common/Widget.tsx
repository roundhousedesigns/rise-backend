import { Box, BoxProps, Heading, useColorMode } from '@chakra-ui/react';
import HeadingCenterline from '@common/HeadingCenterline';

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
	centerLineColor = '',
	...props
}: Props & BoxProps) => {
	const { colorMode } = useColorMode();

	centerLineColor = centerLineColor || (colorMode === 'dark' ? 'text.light' : 'text.dark');

	return (
		<Box {...props}>
			{title && (
				<>
					{titleStyle === 'centerline' && (
						<HeadingCenterline
							lineColor={centerLineColor}
							headingProps={{ fontSize: '2xl' }}
							mb={2}
						>
							{title}
						</HeadingCenterline>
					)}
					{titleStyle === 'contentTitle' && <Heading variant='contentTitle'>{title}</Heading>}
				</>
			)}
			{children}
		</Box>
	);
};

export default Widget;
