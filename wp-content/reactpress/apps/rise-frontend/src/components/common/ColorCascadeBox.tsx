import { Box, BoxProps } from '@chakra-ui/react';
import { ReactNode } from 'react';

interface Props {
	spread?: number; // Multiplier for the spread of the cascade.
	withBorder?: boolean;
	children: ReactNode;
}

export default function ColorCascadeBox({
	spread = 1,
	withBorder = false,
	children,
	...props
}: Props & BoxProps) {
	return (
		<Box position='relative' m={1} {...props}>
			<Box
				bg='brand.orange'
				borderRadius='md'
				w='full'
				h='full'
				transform={`translate(${9 * spread}px, ${9 * spread}px)`}
				position='absolute'
				top={0}
				left={0}
			/>
			<Box
				bg='brand.yellow'
				borderRadius='md'
				w='full'
				h='full'
				transform={`translate(${6 * spread}px, ${6 * spread}px)`}
				position='absolute'
				top={0}
				left={0}
			/>
			<Box
				bg='brand.blue'
				borderRadius='md'
				w='full'
				h='full'
				transform={`translate(${3 * spread}px, ${3 * spread}px)`}
				position='absolute'
				top={0}
				left={0}
			/>
			{withBorder ? (
				<Box
					borderColor='brand.blue'
					borderWidth={`${Math.sqrt(spread) * 2}px`}
					borderRadius='md'
					overflow='hidden'
				>
					{children}
				</Box>
			) : (
				children
			)}
		</Box>
	);
}
