/**
 * Copyright (c) 2024 Maestra Music and Roundhouse Designs. All rights reserved.
 */

import { Box, Flex, useMediaQuery } from '@chakra-ui/react';
import { SearchContextProvider } from '@context/SearchContext';
import Main from '@layout/Main';
import Sidebar from '@layout/Sidebar';

export default function App() {
	const [isLargerThanMd] = useMediaQuery('(min-width: 36rem)');

	return (
		<Box
			id='app-root'
			_dark={{
				bg: 'bg.dark',
				color: 'text.light',
			}}
			_light={{
				bg: 'bg.light',
				color: 'text.dark',
			}}
			w='100vw'
			h='full'
			overflow='auto'
		>
			<SearchContextProvider>
				<Box h='100%' w='full'>
					<Flex w='full' h='100%' justifyContent='stretch' alignItems='stretch' position='relative'>
						<Sidebar position={isLargerThanMd ? 'relative' : 'absolute'} />
						<Main />
					</Flex>
				</Box>
			</SearchContextProvider>
		</Box>
	);
}
