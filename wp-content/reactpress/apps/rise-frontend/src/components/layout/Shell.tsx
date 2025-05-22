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
	description?: string | JSX.Element;
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
}: Props & BoxProps): JSX.Element {
	return loading ? (
		<Center>
			<Spinner position='relative' top={12} />
		</Center>
	) : (
		<Box pt={4} pr={2} pb={8} pl={0} mt={0} mb={0} {...props}>
			{!!title || !!actions ? (
				<Flex
					justifyContent='space-between'
					alignItems='flex-end'
					gap={2}
					flexWrap='wrap'
					m={0}
					py={0}
					px={4}
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
				<Text as={Box} fontSize='sm' px={4} mt={0}>
					{description}
				</Text>
			) : null}

			<Box px={4} mx='auto'>
				{children}
			</Box>
		</Box>
	);
}
