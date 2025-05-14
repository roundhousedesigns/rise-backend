import { Box, BoxProps } from '@chakra-ui/react';

interface Props {
	children: React.ReactNode;
}

export default function ColorCascadeBox({ children, ...props }: Props & BoxProps) {
	return (
		<Box position='relative' m={1} {...props}>
			<Box
				bg='brand.orange'
				borderRadius='md'
				w='full'
				h='full'
				transform='translate(12px, 12px)'
				position='absolute'
				top={0}
				left={0}
			/>
			<Box
				bg='brand.yellow'
				borderRadius='md'
				w='full'
				h='full'
				transform='translate(7px, 7px)'
				position='absolute'
				top={0}
				left={0}
			/>
			<Box
				bg='brand.blue'
				borderRadius='md'
				w='full'
				h='full'
				transform='translate(3px, 3px)'
				position='absolute'
				top={0}
				left={0}
			/>
			{children}
		</Box>
	);
}
