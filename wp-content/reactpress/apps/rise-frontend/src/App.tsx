/**
 * Copyright (c) 2024 Maestra Music and Roundhouse Designs. All rights reserved.
 */

import { Box, Container, Heading, Image, Stack, Text, useMediaQuery } from '@chakra-ui/react';
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
			{/* <SearchContextProvider>
				<Flex h='100%' minH='500px' w='full' flexWrap='nowrap'>
					<Sidebar
						sidebarExpanded={sidebarExpanded}
						setSidebarExpanded={setSidebarExpanded}
						w={sidebarExpanded ? sidebarMaxWidth : sidebarMinWidth}
					/>
					<Main />
				</Flex>
			</SearchContextProvider> */}
			<Container h='100%' minH='500px' maxW='4xl' textAlign='center'>
				<Stack alignItems='center' justifyContent='center' w='full' py={10}>
					<Image
						src='https://images.squarespace-cdn.com/content/v1/6465b16bb5778d6753b1c77e/7eeb8b35-5e67-4903-a6ac-0891ad2dbd29/RISE-white.png?format=1500w'
						alt='RISE Theatre Directory'
						w='100%'
						maxW='600px'
						mb={10}
					/>
					<Heading as='h2' variant='pageTitle' fontSize='6xl'>
						We'll Be Right Back!
					</Heading>
					<Text fontSize='2xl'>
						We're hard at work upgrading the RISE Theatre Directory.
						<br /> Thanks for your patience...we won't be long!
					</Text>
				</Stack>
			</Container>
		</Box>
	);
}
