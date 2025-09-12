import { Box, BoxProps, Heading } from '@chakra-ui/react';

interface Props {
	heading?: string;
	children: React.JSX.Element;
}

export default function SearchFilterSection({
	heading,
	children,
	...props
}: Props & BoxProps): React.JSX.Element {
	return (
		<Box {...props}>
			{heading ? (
				<Heading as='h3' variant='searchFilterTitle'>
					{heading}
				</Heading>
			) : (
				false
			)}
			{children}
		</Box>
	);
}
