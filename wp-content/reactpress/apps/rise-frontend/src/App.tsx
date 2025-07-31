/**
 * Copyright (c) 2024 Maestra Music and Roundhouse Designs. All rights reserved.
 */

import { Box, Flex, useMediaQuery } from '@chakra-ui/react';
import { SearchContextProvider } from '@context/SearchContext';
import Main from '@layout/Main';
import Sidebar from '@layout/Sidebar';
import { useEffect, useState } from 'react';

export default function App() {
	const [isLargerThanMd] = useMediaQuery('(min-width: 36rem)');
	const [sidebarExpanded, setSidebarExpanded] = useState(false);

	const sidebarMinWidth = '52px';
	const sidebarMaxWidth = '200px';

	// Initialize sidebar expansion based on screen size
	useEffect(() => {
		if (isLargerThanMd) {
			setSidebarExpanded(true);
		} else {
			setSidebarExpanded(false);
		}
	}, [isLargerThanMd]);

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
				<Flex h='100%' minH='500px' w='full' flexWrap='nowrap'>
					<Sidebar
						sidebarExpanded={sidebarExpanded}
						setSidebarExpanded={setSidebarExpanded}
						w={sidebarExpanded ? sidebarMaxWidth : sidebarMinWidth}
					/>
					<Main />
				</Flex>
			</SearchContextProvider>
		</Box>
	);
}

