/**
 * Copyright (c) 2024 Maestra Music and Roundhouse Designs. All rights reserved.
 */

import { Box, Flex } from '@chakra-ui/react';
import { SearchContextProvider } from '@context/SearchContext';
import Main from '@layout/Main';
import Sidebar from '@layout/Sidebar';

export default function App() {
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
			minH='100%'
			w='100vw'
		>
			<SearchContextProvider>
				<Box minH='100%' w='full'>
					<Flex w='full' minH='100%' justifyContent='stretch' alignItems='stretch'>
						<Sidebar />
						<Main />
					</Flex>
				</Box>
			</SearchContextProvider>
		</Box>
	);
}
