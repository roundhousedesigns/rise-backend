/**
 * Copyright (c) 2024 Maestra Music and Roundhouse Designs. All rights reserved.
 */

import { Box, Grid, useMediaQuery } from '@chakra-ui/react';
import { SearchContextProvider } from '@context/SearchContext';
import Main from '@layout/Main';
import Sidebar from '@layout/Sidebar';
import { useEffect, useState } from 'react';

export default function App() {
	const [isLargerThanMd] = useMediaQuery('(min-width: 36rem)');
	const [sidebarExpanded, setSidebarExpanded] = useState(false);

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
				<Box h='100%' w='full'>
					<Grid
						w='full'
						h='100%'
						position='relative'
						templateAreas='sidebar main'
						templateColumns={`${sidebarExpanded ? '170px' : '45px'} 1fr`}
						transition='all 0.3s ease'
					>
						<Sidebar sidebarExpanded={sidebarExpanded} setSidebarExpanded={setSidebarExpanded} />
						<Main />
					</Grid>
				</Box>
			</SearchContextProvider>
		</Box>
	);
}
