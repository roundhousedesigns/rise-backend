import {
    Box,
    BoxProps,
    Center,
    Flex,
    Heading,
    HeadingProps,
    Spinner,
    Text,
} from '@chakra-ui/react';
import { ReactNode } from 'react';

interface Props {
	title?: string;
	description?: string | React.JSX.Element;
	actions?: ReactNode;
	loading?: boolean;
	children: ReactNode;
	titleProps?: HeadingProps;
}

export default function Shell({
	title,
	description,
	actions,
	loading,
	children,
	titleProps,
	...props
}: Props & BoxProps): React.JSX.Element {
	return loading ? (
		<Center>
			<Spinner position='relative' top={12} />
		</Center>
	) : (
		<Box pt={4} px={0} pb={8} mt={0} mb={0} mx={0} {...props}>
			{!!title || !!actions ? (
				<Flex
					justifyContent='space-between'
					alignItems='flex-end'
					gap={2}
					flexWrap='wrap'
					m={0}
					py={0}
					pr={4}
					pl={{ base: 2, md: 4 }}
				>
					{title ? (
						<Heading
							variant='pageTitle'
							as='h1'
							flex='1'
							my={0}
							px={0}
							lineHeight='normal'
							w='full'
							{...titleProps}
						>
							{title}
						</Heading>
					) : null}

					{actions && (
						<Flex flexWrap='wrap' gap={2} justifyContent='flex-end'>
							{actions}
						</Flex>
					)}
				</Flex>
			) : null}

			{description ? (
				<Text as={Box} fontSize='sm' pr={4} pl={{ base: 2, md: 4 }} mt={0}>
					{description}
				</Text>
			) : null}

			<Box pr={4} pl={{ base: 2, md: 4 }} mx='auto'>
				{children}
			</Box>
		</Box>
	);
}
